<?php

namespace src\core\table;

use Exception;
use PDO;
use PDOException;
use ReflectionClass;
use ReflectionException;
use src\core\App;
use src\core\DBSaveEnum;
use src\core\L;

trait TableSaveAndDeleteFunctions {

  /**
   * Identification number of the dataclass, increases with every new instance.
   * !Should not be changed manually.
   */
  var int $id = 0;

  var int $author_id = 0;

  var int $created_at = 0;

  /**
   * @throws ReflectionException
   */
  function get_db_ready_fields(bool $no_id = false): array {
    /** @var $this DataClass */
    $fields = [];

    $reflection_class = new ReflectionClass(static::class);
    $simple_types = ["int", "string", "float", "bool"];

    foreach (get_object_vars($this) as $key => $value) {

      if (str_starts_with($key, "_")) {
        continue;
      }

      if ($no_id && $key === "id") {
        continue;
      }

      $is_enum =  static::isEnum($reflection_class->getProperty(name: $key)->getType()->getName());

      if (
        in_array(
          needle: $reflection_class->getProperty($key)->getType()->getName(),
          haystack: $simple_types
        )
      ) {
        if (is_bool($value)) {
          $fields[$key] = $value ? "true" : "false";
        }
        else {
          $fields[$key] = $value;
        }
      }

      elseif ($is_enum) {
        $fields[$key] = $value->value;
      }

      else {
        $fields[$key] = serialize($value);
      }

    }
    return $fields;
  }

  /**
   * Create a table.
   *
   * All fields are added via ALTER TABLE.
   *
   * @throws ReflectionException
   * @throws PDOException
   * @throws Exception
   */
  static function create_table(): void {
    # [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    #$log("Create Table: " . static::class);
    #var_dump($pdo->getAttribute(PDO::ATTR_DRIVER_NAME));

    $pdo = App::get_database(static::$db_to_use);
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
      if ($field_type === "int") $field_type = "INTEGER";
      elseif ($field_type === "string") $field_type = "TEXT";
      elseif ($field_type === "float") $field_type = "REAL";
      elseif ($field_type === "bool") $field_type = "INTEGER";
      else $field_type = "TEXT";


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
          #$log("Field in $table_name already exists - ignore alter table", $statement);
        }
      }
    }
  }

  /**
   * Return fields that don't start with "_".
   * @return array<string> all the fields that are relevant for the database
   * @throws ReflectionException
   * @throws Exception
   *
   */
  static function get_relevant_fields(): array {
    $fields = [];
    $reflection_class = new ReflectionClass(static::class);
    foreach (get_class_vars(get_called_class()) as $key => $value) {

      if($key === "db_to_use"){
        # ignore this static field ...
        continue;
      }
      if (!str_starts_with($key, "_")) {


        $is_enum =  static::isEnum($reflection_class->getProperty(name: $key)->getType()->getName());

        if($is_enum){

          $reflection_enum = new \ReflectionEnum(
            $reflection_class->getProperty(name: $key)->getType()->getName()
          );

          $backing_type = $reflection_enum->getBackingType();
          if ($backing_type->getName() === "int") {
            $fields[$key] = "int";
          }
          elseif ($backing_type->getName() === "string") {
            $fields[$key] = "string";
          }
          else {
            throw new Exception("Unknown backing type for enum, only int and string are allowed.");
          }



        }else{

          $fields[$key] = $reflection_class->getProperty($key)->getType()->getName();

        }
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
  private function insert(): void {
    $db = App::get_database(static::$db_to_use);
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

    $my_values_read_for_db = $this->get_db_ready_fields(no_id: true);

    foreach ($relevant_fields as $field_name => $field_type) {
      if ($field_name === 'id') continue;
      $stmt->bindValue(
        param: ":$field_name",
        value: $my_values_read_for_db[$field_name]
      );
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
  private function update(): void {
    $db = App::get_database(static::$db_to_use);
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

    $my_values_read_for_db = $this->get_db_ready_fields(no_id: false);

    foreach ($relevant_fields as $field_name => $field_type) {
      $stmt->bindValue(
        param: ":$field_name",
        value: $my_values_read_for_db[$field_name]
      );
    }

    $stmt->execute();

    $this->on_update();
  }

  /**
   * @throws ReflectionException
   */
  function save(): void {
    $db = App::get_database(static::$db_to_use);
    if ($this->id === 0) {
      $this->insert();
      L::info("Inserted instance of " . static::class . " with id: " . $this->id);
    }
    else {
      $this->update();
      L::info("Updated instance of " . static::class . " with id: " . $this->id);
    }


  }

  /**
   * Delete a dataclass instance from the database.
   *
   * It does NOT delete or change the instance itself.
   *
   * @param PDO $pdo
   * @return void
   * @throws Exception
   */
  function delete(): void {
    $pdo = App::get_database(static::$db_to_use);
    if ($this->id === 0) {
      L::err("Delete called on instance with id 0");
      L::err("This is not allowed, since we don't know what to delete");
      throw new Exception("Delete called on instance with id 0");
    }

    L::info("Delete instance of " . static::class . " with id: " . $this->id);
    $table_name = (new ReflectionClass(static::class))->getShortName();
    $sql = "DELETE FROM `$table_name` WHERE `id` = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":id", $this->id);
    $stmt->execute();
    $this->on_delete();

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

}