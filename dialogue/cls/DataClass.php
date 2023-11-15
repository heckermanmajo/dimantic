<?php
declare(strict_types=1);

namespace cls;

use Exception;
use JsonSerializable;
use PDO;
use PDOException;
use ReflectionClass;
use ReflectionException;


/**
 * Baseclass for all dataclasses.
 *
 * Basically my own ORM.
 *
 * Automates create table, alter table, insert, update
 * sql statements.
 *
 * -> DOES NOT AUTOMATE select, since select has a too high
 * variance to be automated.
 *
 * Fields with a leading "_" are not saved to the database.
 * BUt they are written if the dbb returns them,
 * if they are in the select statement, for example as part
 * of a named subquery.
 *
 */
abstract class DataClass implements JsonSerializable {
  /**
   * Identification number of the dataclass, increases with every new instance.
   * !Should not be changed manually.
   */
  var int $id = 0;

  public function __construct(array $data_from_db = []) {
    # set the created_at field in case it exists
    # if we load from the db it is just overwritten
    if (property_exists(object_or_class: $this, property: "created_at")) {
      $this->created_at = time();
    }

    # set all the data
    # todo: here is the place to intercept and apply check functions for data
    #       consistency, so we can detect bad db state and fix it
    foreach ($data_from_db as $key => $value) {
      if (property_exists(object_or_class: $this, property: $key)) {
        if ($value == null) {
          continue;
        }
        $this->$key = $value;
      }
    }

  } # end of constructor


  /**
   * Create a table.
   *
   * All fields are added via ALTER TABLE.
   *
   * @throws ReflectionException
   * @throws PDOException
   * @throws Exception
   */
  static function create_table(PDO $pdo): void {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__);
    $log("Create Table: " . static::class);
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

      }
      catch (PDOException $e) {
        $message = $e->getMessage();
        $properly_already_exists = str_contains($message, "already exists") || str_contains($message, "duplicate column name");
        if (!$properly_already_exists) {
          throw $e;
        }
        else {
          $log("Field in $table_name already exists - ignore alter table", $statement);
        }
      }
    }
  }

  /**
   * Return fields that don't start with "_".
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

  /**
   * If save is called and the id is not set, we anticipate that the entry
   * does not exist, and we insert it.
   *
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
   * If save is called and the id is set, we anticipate that the entry
   * already exists, and we update it.
   *
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
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__);

    if ($this->id === 0) {
      $this->insert($db);
      $log("Inserted instance of " . static::class . " with id: " . $this->id);
    }
    else {
      $this->update($db);
      $log("Updated instance of " . static::class . " with id: " . $this->id);
    }

  }

  /**
   * This function can be overwritten by subclasses to inject
   * behaviour on create.
   *
   * @return void
   */
  function on_create(): void {
    # implemented by subclass
  }


  /**
   * This function can be overwritten by subclasses to inject
   * behaviour on update.
   */
  function on_update(): void {
    # implemented by subclass
  }


  /**
   * This function can be overwritten by subclasses to inject
   * behaviour on delete.
   */
  function on_delete(): void {
    # implemented by subclass
  }

  /**
   * Returns an array of instances of the dataclass.
   *
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
   * Returns a single instance of the dataclass.
   *
   * Even if the sql returns multiple rows, only the first
   * row is used and the rest is ignored.
   *
   * @param PDO $pdo
   * @param string $sql
   * @param array $params
   * @param bool $throw_on_null
   *
   * @return static|null
   *
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

  /**
   * Returns the count-result of the given sql.
   *
   * @param PDO $pdo - the pdo connection
   * @param string $sql - must start with "SELECT COUNT(*)"
   * @param array $params - the params for the sql
   *
   * @return int
   *
   * @throws Exception
   */
  static function get_count(PDO $pdo, string $sql, array $params = []): int {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__);
    if (!str_starts_with($sql, "SELECT COUNT(*)")) {
      $err("get_count called with sql that does not start with SELECT COUNT(*)");
      $err("sql: $sql");
      throw new Exception("get_count called with sql that does not start with SELECT COUNT(*)");
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();  // COUNT(*) returns an integer
  }

  /**
   * Returns an instance by id.
   *
   * Simple helper, since this is a very common use case.
   *
   * @param PDO $pdo
   * @param int $id
   * @param bool $throw_on_null
   * @return static|null
   * @throws Exception
   */
  static function get_by_id(PDO $pdo, int $id, bool $throw_on_null = false): ?static {
    $table_name = (new ReflectionClass(static::class))->getShortName();
    $sql = "SELECT * FROM `$table_name` WHERE `id` = :id LIMIT 1";

    $params = [':id' => $id];

    return static::get_one($pdo, $sql, $params, $throw_on_null);
  }

  /**
   * Since all fields in a dataclass are std-types or
   * Dataclasses, we can just use json_encode to get a
   * json representation of the dataclass.
   *
   * @return array
   */
  public function jsonSerialize(): array {
    return get_object_vars($this);
  }


  # we dont want this function, since it is to dangerous
  #static function truncate_table(PDO $pdo): void {
  #  $table_name = (new ReflectionClass(static::class))->getShortName();
  #  $sql = "TRUNCATE TABLE `$table_name`";
  #  $pdo->exec($sql);
  #}

  /**
   * Delete a dataclass instance from the database.
   *
   * It does NOT delete or change the instance itself.
   *
   * @param PDO $pdo
   * @return void
   */
  function delete(PDO $pdo): void {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__);
    $log("Delete instance of " . static::class . " with id: " . $this->id);
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
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__);
    $warn("get_html_escaped_copy not overwritten, but used in " . static::class);
    $warn("this is potentially a security risk, since we don't escape the values");
    return clone $this;
  }

  #todo: is this function still needed?
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

  /**
   * This function returns client save values, for example to return as json to the end
   * user.
   *
   * @example If we want to send a list of users, we don't want to send all emails and
   *          password hashes.
   *
   * This function is intended to be overwritten by subclasses.
   *
   * @param App $app
   * @return array
   */
  function get_client_save_values(App $app): array {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__);
    $warn("get_client_save_values not overwritten, but used in " . static::class);
    return get_object_vars($this);
  }

  /**
   * This function allows to check if the given value is valid.
   *
   * This function is intended to be overwritten by subclasses.
   *
   * @param string $field_name
   * @param string $value
   * @param App $app
   * @return string|null
   */
  static function check_value(string $field_name, string $value, App $app): string|null {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__);
    $warn("check_value not overwritten, but used in " . static::class);
    $warn("field_name: $field_name, value: $value");
    $warn("this is potentially a security risk, since we don't check the value");
    return null;
  }

}