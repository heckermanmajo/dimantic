<?php
declare(strict_types=1);

namespace cls;
/**
 * @todo: add debug logs to all the methods ...
 */

use Exception;
use JsonSerializable;
use PDO;
use PDOException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;


/**
 * Baseclass for all dataclasses.
 */
abstract class DataClass implements JsonSerializable {
  /**
   * identification number of the dataclass, increases with every new instance
   */
  var int $id = 0;

  public function __construct(array $data_from_db = []) {
    #if fields created at and updated at exists -> set it now
    #if (property_exists($this, "updated_at")) $this->created_at = time();
    #if (property_exists($this, "updated_at")) $this->updated_at = time();
    
    # set all the data
    foreach ($data_from_db as $key => $value) {
      if (property_exists($this, $key)) {
        if ($value == null) {
          continue;
        }
        $this->$key = $value;
      }
    }
  }
  
  # todo: get the sql
  
  /**
   * @throws ReflectionException
   * @throws PDOException
   * @throws Exception
   */
  static function create_table(PDO $pdo): void {
    #var_dump($pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
    $is_mysql = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === "mysql";
    $is_sqlite3 = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === "sqlite";
    
    $sql_relevant_fields = static::get_relevant_fields();
    $reflection_class = new ReflectionClass(static::class);
    $table_name = $reflection_class->getShortName();
    
    $statements = [];
    
    if ($is_sqlite3) {
      $statements[] = "CREATE TABLE IF NOT EXISTS `$table_name` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL
      );";
    }
    elseif ($is_mysql) {
      $statements[] = "CREATE TABLE IF NOT EXISTS `$table_name` (
        `id` INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL
      );";
    }
    else {
      throw new Exception("Unknown database type");
    }
    
    foreach ($sql_relevant_fields as $field_name => $field_type) {
      if ($field_name === "id") continue;
      if ($field_type === "int") {
        $field_type = "INTEGER";
      }
      elseif ($field_type === "string") $field_type = "TEXT";
      elseif ($field_type === "float") $field_type = "REAL";
      elseif ($field_type === "bool") $field_type = "INTEGER";
      elseif ($field_type === "array") $field_type = "TEXT";
      elseif ($field_type === "object") $field_type = "TEXT";
      else throw new Exception("Unknown field type: $field_type");
      
      if (str_starts_with($field_name, "int64_")) $field_type = "BIGINT";
      
      // get a default
      $default = "";
      if ($field_type === "INTEGER") $default = "DEFAULT 0";
      if ($field_type === "TEXT") $default = "DEFAULT ''";
      if ($field_type === "REAL") $default = "DEFAULT 0.0";
      
      $statements[] = "ALTER TABLE `$table_name` ADD COLUMN `$field_name` $field_type $default;";
    }
    foreach ($statements as $statement) {
      try {
        
        $pdo->exec($statement);
        
      } catch (PDOException $e) {
        $message = $e->getMessage();
        $properly_already_exists = str_contains($message, "already exists") || str_contains($message, "duplicate column name");
        if (!$properly_already_exists) {
          throw $e;
        }
      }
    }
  }
  
  /**
   * - don't start with "_"
   * @return array<string> all the fields that are relevant for the database
   * @throws ReflectionException
   */
  static function get_relevant_fields(): array {
    $fields = [];
    $reflection_class = new ReflectionClass(static::class);
    foreach (get_class_vars(get_called_class()) as $key => $value) {
      if (!str_starts_with($key, "_")) {
        $fields[$key] = $reflection_class->getProperty($key)->getType()->getName();
      }
    }
    return $fields;
  }
  
  # get ine get array with throw on array
  
  /**
   * @throws ReflectionException
   */
  private function insert(PDO $db): void {
    $table_name = (new ReflectionClass(static::class))->getShortName();
    $relevant_fields = static::get_relevant_fields();
    
    $columns = [];
    $values = [];
    
    foreach ($relevant_fields as $field_name => $field_type) {
      if ($field_name !== 'id' && property_exists(static::class, $field_name)) {
        $columns[] = "`$field_name`";
        $values[] = ":$field_name";
      }
    }
    
    $columns = implode(', ', $columns);
    $values = implode(', ', $values);
    
    $sql = "INSERT INTO `$table_name` ($columns) VALUES ($values)";
    
    $stmt = $db->prepare($sql);
    
    foreach ($relevant_fields as $field_name => $field_type) {
      if ($field_name !== 'id' && property_exists(static::class, $field_name)) {
        $stmt->bindValue(":$field_name", $this->$field_name);
      }
    }
    
    $stmt->execute();
    $last_insert_id = $db->lastInsertId();
    if ($last_insert_id === false) {
      throw new Exception("Could not get last insert id.");
    }
    $this->id = (int)$last_insert_id;
    
    $this->on_create();
  }
  
  /**
   * @throws ReflectionException
   */
  private function update(PDO $db): void {
    $table_name = (new ReflectionClass(static::class))->getShortName();
    $relevant_fields = static::get_relevant_fields();
    
    $updates = [];
    foreach ($relevant_fields as $field_name => $field_type) {
      if ($field_name !== 'id' && property_exists(static::class, $field_name)) {
        $updates[] = "`$field_name` = :$field_name";
      }
    }
    
    $updates = implode(', ', $updates);
    
    $sql = "UPDATE `$table_name` SET $updates WHERE `id` = :id";
    
    $stmt = $db->prepare($sql);
    
    foreach ($relevant_fields as $field_name => $field_type) {
      if ($field_name !== 'id' && property_exists(static::class, $field_name)) {
        $stmt->bindValue(":$field_name", $this->$field_name);
      }
    }
    $stmt->bindValue(":id", $this->id);
    
    $stmt->execute();
    
    $this->on_update();
  }
  
  function save(PDO $db): void {
    if ($this->id === 0) {
      $this->insert($db);
    }
    else {
      $this->update($db);
    }
  }
  
  # on create
  
  function on_create() {
    # implemented by subclass
  }
  
  # on update
  function on_update() {
    # implemented by subclass
  }
  
  
  # on delete
  function on_delete() {
    # implemented by subclass
  }
  
  /**
   * @param PDO $pdo
   * @param string $sql
   * @param array $params
   * @return array<static>
   */
  static function get_array(PDO $pdo, string $sql, array $params = []): array {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    $results = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $dataclass = new static($row);
      $results[] = $dataclass;
    }
    
    return $results;
  }


  
  /**
   * @param PDO $pdo
   * @param string $sql
   * @param array $params
   * @param bool $throw_on_null
   * @return static|null
   * @throws Exception
   */
  static function get_one(PDO $pdo, string $sql, array $params, bool $throw_on_null = false): ?static {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row === false) {
      if ($throw_on_null) {
        throw new Exception('Record not found.');
      }
      return null;
    }
    
    return new static($row);
  }

  static function get_count(PDO $pdo, string $sql, array $params = []): int {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    // COUNT(*) returns an integer
    return (int)$stmt->fetchColumn();
  }
  
  /**
   * @param PDO $pdo
   * @param array $field_value_pairs
   * @param bool $throw_on_null
   * @return static|null
   */
  static function get_one_by_field_value_pair(PDO $pdo, array $field_value_pairs, bool $throw_on_null = false): ?static {
    $table_name = (new ReflectionClass(static::class))->getShortName();
    
    $conditions = [];
    $params = [];
    
    foreach ($field_value_pairs as $field => $value) {
      $conditions[] = "`$field` = :$field";
      $params[":$field"] = $value;
    }
    
    $conditions = implode(' AND ', $conditions);
    
    $sql = "SELECT * FROM `$table_name` WHERE $conditions LIMIT 1";
    
    return static::get_one($pdo, $sql, $params, $throw_on_null);
  }
  
  /**
   * @param PDO $pdo
   * @param array $field_value_pairs
   * @param bool $throe_on_null
   * @return array<static>
   */
  static function get_array_by_field_value_pair(PDO $pdo, array $field_value_pairs, bool $throw_on_null = false, int $offset = 0, int $limit = 10): array {
    $table_name = (new ReflectionClass(static::class))->getShortName();
    
    $conditions = [];
    $params = [];
    
    foreach ($field_value_pairs as $field => $value) {
      $conditions[] = "`$field` = :$field";
      $params[":$field"] = $value;
    }
    
    $conditions = implode(' AND ', $conditions);
    
    $sql = "SELECT * FROM `$table_name` WHERE $conditions LIMIT $offset, $limit";
    
    return static::get_array($pdo, $sql, $params);
  }
  
  /**
   * @param PDO $pdo
   * @param int $id
   * @param bool $throw_on_null
   * @return static|null
   */
  static function get_by_id(PDO $pdo, int $id, bool $throw_on_null = false): ?static {
    $table_name = (new ReflectionClass(static::class))->getShortName();
    $sql = "SELECT * FROM `$table_name` WHERE `id` = :id LIMIT 1";
    
    $params = [':id' => $id];
    
    return static::get_one($pdo, $sql, $params, $throw_on_null);
  }
  
  # json
  
  function check_values() {
    # implemented by subclass
  }
  
  public function jsonSerialize(): mixed {
    return get_object_vars($this);
  }
  
  # drop table
  static function truncate_table(PDO $pdo): void {
    $table_name = (new ReflectionClass(static::class))->getShortName();
    $sql = "TRUNCATE TABLE `$table_name`";
    $pdo->exec($sql);
  }
  
  function delete(PDO $pdo): void {
    $table_name = (new ReflectionClass(static::class))->getShortName();
    $sql = "DELETE FROM `$table_name` WHERE `id` = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":id", $this->id);
    $stmt->execute();
    $this->on_delete();
  }

  /**
   * We use this function to get a copy of this dataclass,
   * but with escaped fields.
   *
   * @return $this
   */
  function get_html_escaped_copy(): static {
    #Utils::debug_log('get_html_escaped_copy from not overwritten function: security risk', $this);
    return clone $this;
  }
  
  function __unserialize(array $data): void {
    foreach ($data as $key => $value) {
      if (property_exists($this, $key)) {
        if ($value == null) {
          continue;
        }
        $this->$key = $value;
      }
    }
  }

  function get_client_save_values(): array {
    return get_object_vars($this);
  }

}