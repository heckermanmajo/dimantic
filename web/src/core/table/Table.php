<?php

namespace src\core\table;

use ReflectionClass;
use ReflectionException;
use src\app\mailer\actions\SendAdminMail;
use src\core\App;
use src\global\compositions\GetCurrentlyLoggedInAccount;
use src\global\compositions\GetEnvironmentMode;
use Throwable;

abstract class Table {

  use TableReadFromDatabase;
  use TableSaveAndDeleteFunctions;


  private static function isEnum(string $class): bool {
    if($class === "int" || $class === "string" || $class === "float" || $class === "bool"){
      return false;
    }
    try {
      $refl = new ReflectionClass($class);
    } catch (ReflectionException $e) {
      return false;
    }

    return $refl->isEnum();
  }


  static string $db_to_use = "default"; # default, embeddings, usage

  /**
   * @throws ReflectionException|Throwable
   */
  public function __construct(array $data_from_db = []) {

    # set the created_at field in case it exists
    # if we load from the db it is just overwritten
    if (property_exists(object_or_class: $this, property: "created_at")) {
      $this->created_at = time();
    }

    # overwritten if data from db is set, but prevents not setting it on a new instance
    if (GetCurrentlyLoggedInAccount::somebody_is_logged_in()){
      if($this->author_id <= 0){
        $this->author_id = GetCurrentlyLoggedInAccount::get_account()->id;
      }
    }

    $reflection_class = new ReflectionClass(objectOrClass: static::class);

    foreach ($data_from_db as $key => $value) {

      if($key === "db_to_use"){
        # ignore static field
        #App::info("Ignoring db_to_use field in " . static::class);
        #App::info("This should not happen");
        continue;
      }

      if (property_exists(object_or_class: $this, property: $key)) {

        if ($value === null) {
          $can_be_null = $reflection_class->getProperty(name: $key)->getType()->allowsNull();
          if ($can_be_null) {
            $this->$key = null;
          }
          continue; # ignore null values that are not allowed
        }

        $simple_types = ["int", "string", "float", "bool"];
        # check if type implements DBSaveEnum interface
        $is_enum =  static::isEnum($reflection_class->getProperty(name: $key)->getType()->getName());


        if (
          in_array(
            needle: $reflection_class->getProperty(name: $key)->getType()->getName(),
            haystack: $simple_types
          )
        ) {
          if(is_bool($this->$key)){

            if(is_string($value) && !is_numeric($value)){
              $value = strtolower($value);
              if($value === "true"){
                $value = true;
              }
              else if($value === "false"){
                $value = false;
              }
              else if ($value === "b:1;"){ # serialized boolean
                $value = true;
              }
              else if ($value === "b:0;"){ # serialized boolean
                $value = false;
              }
              else{
                static::err("Could not convert $value to boolean");
              }
            }else{
              $value = (bool)$value; # in case it is 0 or 1
            }
            $this->$key = $value;
          }
          else{
            $this->$key = $value;
          }
        }

        elseif($is_enum){
          $this->$key = $reflection_class->getProperty(name: $key)->getType()->getName()::from($value);
        }

        else {
          try {

            $this->$key = unserialize(data: $value);

          }
          catch (Throwable $t) {
            $err("Class: " . static::class);
            $err("Could not unserialize $key");
            $err($t->getMessage());
            $err($t->getTraceAsString());
            $err("Value: $value");

            if (GetEnvironmentMode::is_debug()) {
              throw $t;
            }
            else {
              $action = new SendAdminMail(
                head: "During un-serialisation of instance from database error happend!!",
                body: $t->getMessage() . "\n\n" . $t->getTraceAsString() . "\n\n" . "Value: $value"
              );
              $action->execute();
            }

          }
        }

      }

      else {

        #static::warn("Property $key does not exist in " . static::class);
        #static::warn("was ignored ... ");

      }

    }

  } # end of constructor


  function __unserialize(array $data): void {
    foreach ($data as $key => $value) {
      if (property_exists($this, $key)) {
        if ($value === null) {
          # set null if null is allowed
          continue;
        }
        $this->$key = $value;
      }
    }
  }

  function rollback(): void {
    # todo: implement rollback
  }

  /**
   * Get the "random" SQL function based on the current database driver.
   *
   * @return string The random SQL function.
   * @throws ReflectionException|Throwable
   */
  static function get_random_sql_function(): string {
    return match(App::get_database(static::$db_to_use)->getAttribute(attribute: PDO::ATTR_DRIVER_NAME)) {
      "sqlite" => "RANDOM()",
      "mysql" => "RAND()",
      default => "RAND()"
    };
  }

  static function get_table_name(): string {
    return (new ReflectionClass(static::class))->getShortName();
  }

}