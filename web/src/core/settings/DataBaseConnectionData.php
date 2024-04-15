<?php

namespace src\core\settings;

use Exception;
use PDO;
use src\global\compositions\GetPath;

final readonly class DataBaseConnectionData {

  public function __construct(
    public readonly string $db_type,
    public readonly string $host_or_path,
    public readonly string $database_name_or_filename,
    public readonly string $user,
    public readonly string $password,
  ) {
  }

  /**
   * @throws Exception
   */
  function get_connection(): PDO {

    $home = GetPath::get_home_path();

    if($this->db_type === "sqlite") {
      $db = new PDO("sqlite:" . $home . $this->host_or_path . "/" . $this->database_name_or_filename);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } else {
      throw new Exception("Database type " . $this->db_type . " not supported");
    }

    return $db;

  }

}