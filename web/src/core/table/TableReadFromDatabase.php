<?php

namespace src\core\table;

use Exception;
use PDO;
use ReflectionClass;
use src\core\App;
use src\core\L;

trait TableReadFromDatabase {

  /**
   * Returns an array of instances of the dataclass.
   *
   * @param PDO $pdo
   * @param string $sql
   * @param array $params
   * @param array $fields_to_not_escape
   * @return array<static>
   */
  static function get_array(string $sql, array $params = [], array $fields_to_not_escape = []): array {
    $pdo = App::get_database(static::$db_to_use);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    # todo: this CAN in theory lead to bugs if we insert stuff where we escape
    #       f.e. % in some markdown text
    if (str_contains(haystack: $sql, needle: " LIKE ")) {
      foreach ($params as $num => $param) {
        if (is_string($param)) {
          if (in_array(needle: $num, haystack: $fields_to_not_escape)) {
            continue;
          }
          else {
            $params[$num] = $pdo->quote($param);
          }
        }
      }
    }

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
   * @param array $fields_to_not_escape
   * @return static|null
   *
   * @throws Exception
   */
  static function get_one(string $sql, array $params, bool $throw_on_null = false, array $fields_to_not_escape = []): ?static {
    $pdo = App::get_database(static::$db_to_use);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    # todo: this CAN in theory lead to bugs if we insert stuff where we escape
    #       f.e. % in some markdown text
    if (str_contains(haystack: $sql, needle: " LIKE ")) {
      foreach ($params as $num => $param) {
        if (is_string($param)) {
          if (in_array(needle: $num, haystack: $fields_to_not_escape)) {
            continue;
          }
          else {
            $params[$num] = $pdo->quote($param);
          }
        }
      }
    }

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
   * @param array $fields_to_not_escape
   * @return int
   *
   * @throws Exception
   */
  static function get_count(string $sql, array $params = [], array $fields_to_not_escape = []): int {
    $pdo = App::get_database(static::$db_to_use);
    if (
      !str_starts_with($sql, "SELECT COUNT(*)")
    ) {
      L::err("get_count called with sql that does not start with SELECT COUNT(*)");
      L::err("sql: $sql");
      throw new Exception("get_count called with sql that does not start with SELECT COUNT(*)");
    }

    # todo: this CAN in theory lead to bugs if we insert stuff where we escape
    #       f.e. % in some markdown text
    if (str_contains(haystack: $sql, needle: " LIKE ")) {
      foreach ($params as $num => $param) {
        if (is_string($param)) {
          if (in_array(needle: $num, haystack: $fields_to_not_escape)) {
            continue;
          }
          else {
            $params[$num] = $pdo->quote($param);
          }
        }
      }
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();  // COUNT(*) returns an integer,
  }

  static function get_float(string $sql, array $params = [], array $fields_to_not_escape = []): float {
    $pdo = App::get_database(static::$db_to_use);
    if (
      !str_starts_with(trim($sql), "SELECT")
    ) {
      L::err("get_float called with sql that does not start with SELECT");
      L::err("sql: $sql");
      throw new Exception("get_float called with sql that does not start with SELECT");
    }


    # like abouve
    if (str_contains(haystack: $sql, needle: " LIKE ")) {
      foreach ($params as $num => $param) {
        if (is_string($param)) {
          if (in_array(needle: $num, haystack: $fields_to_not_escape)) {
            continue;
          }
          else {
            $params[$num] = $pdo->quote($param);
          }
        }
      }
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (float)$stmt->fetchColumn();  //
  }




  /**
   * @throws Exception
   */
  static function get_sum(string $sql, array $params = [], array $fields_to_not_escape = []): int|float {
    $pdo = App::get_database(static::$db_to_use);
    if (
      !str_starts_with(trim($sql), "SELECT SUM")
    ) {
      L::err("get_sum called with sql that does not start with SELECT SUM");
      L::err("sql: $sql");
      throw new Exception("get_sum called with sql that does not start with SELECT SUM");
    }

    # todo: this CAN in theory lead to bugs if we insert stuff where we escape
    #       f.e. % in some markdown text
    if (str_contains(haystack: $sql, needle: " LIKE ")) {
      foreach ($params as $num => $param) {
        if (is_string($param)) {
          if (in_array(needle: $num, haystack: $fields_to_not_escape)) {
            continue;
          }
          else {
            $params[$num] = $pdo->quote($param);
          }
        }
      }
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();  //
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
  static function get_by_id(int $id, bool $throw_on_null = false): ?static {


    $pdo = App::get_database(static::$db_to_use);
    $table_name = (new ReflectionClass(static::class))->getShortName();
    $sql = "SELECT * FROM `$table_name` WHERE `id` = :id LIMIT 1";

    $params = [':id' => $id];

    $instance = static::get_one( $sql, $params, $throw_on_null);

    return $instance;

  }

  static function get_by_field() { }

  /**
   * Retrieves all records from the database table associated with the current class.
   *
   * @return array<static> An array of records retrieved from the database.
   *
   * @throws Exception Throws an exception if there is an error in retrieving the records.
   */
  static function get_all() : array {
    $table_name = (new ReflectionClass(static::class))->getShortName();
    $sql = "SELECT * FROM `$table_name`";
    return static::get_array($sql);
  }

  static function get_by_matching_fields() { }

  function get_by_associated_id(string $classname, string $association_field_name) { }


  /**
   * @param array $id_list List of ids to fetch from the database.
   * @return array<static>
   * @throws Exception
   */
  static function get_by_given_id_list(array $id_list): array {
    $table_name = (new ReflectionClass(static::class))->getShortName();
    $id_list_string = implode(",", $id_list);
    $sql = "SELECT * FROM `$table_name` WHERE `id` IN ($id_list_string)";
    return static::get_array($sql);
  }

}