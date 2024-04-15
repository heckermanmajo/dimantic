<?php

namespace src\core\settings;

use Exception;
use src\global\compositions\GetPath;

/**
 * Class to contain all application settings.
 *
 * This allows for different application data for development and deployment.
 *
 * If no alternative path is provided, the settings are loaded from the default
 * path, which is the home directory of the user running the script.
 *
 * @see GetPath::get_home_path()
 */
final class Settings {

  private static ?self $instance = null;

  private function __construct(

    public readonly string                 $open_ai_api_key,
    public readonly DataBaseConnectionData $db_credentials,
    public readonly DataBaseConnectionData $usage_db_credentials,
    public readonly DataBaseConnectionData $embeddings_db_credentials,
    public readonly string                 $cron_password,

  ) {
  }

  /**
   * @throws Exception
   */
  static function get_instance(?string $alternative_path = null): self {

    if (self::$instance !== null) return self::$instance;

    if ($alternative_path !== null) {
      $path_to_settings = $alternative_path;
    } else {
      $path_to_settings = GetPath::get_home_path() . "dimantic_settings.json";
    }

    if (!file_exists($path_to_settings)) {
      throw new Exception("Settings file not found at $path_to_settings");
    }

    $raw_settings = json_decode(file_get_contents($path_to_settings), associative: true);

    if ($raw_settings === null) {
      throw new Exception("Settings file at $path_to_settings is not valid json");
    }

    $settings = new Settings(

      open_ai_api_key: $raw_settings["open_ai_api_key"] ?? die("No open_ai_api_key in settings"),

      db_credentials: new DataBaseConnectionData(
        db_type: $raw_settings["db_credentials"]["db_type"] ?? die("No db_type in db_credentials"),
        host_or_path: $raw_settings["db_credentials"]["host_or_path"] ?? die("No host in db_credentials"),
        database_name_or_filename: $raw_settings["db_credentials"]["database_name_or_filename"] ?? die("No database in db_credentials"),
        user: $raw_settings["db_credentials"]["user"] ?? "",
        password: $raw_settings["db_credentials"]["password"] ?? "",
      ),

      usage_db_credentials: new DataBaseConnectionData(
        db_type: $raw_settings["usage_db_credentials"]["db_type"] ?? die("No db_type in usage_db_credentials"),
        host_or_path: $raw_settings["usage_db_credentials"]["host_or_path"] ?? die("No host in usage_db_credentials"),
        database_name_or_filename: $raw_settings["usage_db_credentials"]["database_name_or_filename"] ?? die("No database in usage_db_credentials"),
        user: $raw_settings["usage_db_credentials"]["user"] ??"",
        password: $raw_settings["usage_db_credentials"]["password"] ?? "",
      ),

      embeddings_db_credentials: new DataBaseConnectionData(
        db_type: $raw_settings["embeddings_db_credentials"]["db_type"] ?? die("No db_type in embeddings_db_credentials"),
        host_or_path: $raw_settings["embeddings_db_credentials"]["host_or_path"] ?? die("No host in embeddings_db_credentials"),
        database_name_or_filename: $raw_settings["embeddings_db_credentials"]["database_name_or_filename"] ?? die("No database in embeddings_db_credentials"),
        user: $raw_settings["embeddings_db_credentials"]["user"] ?? "",
        password: $raw_settings["embeddings_db_credentials"]["password"] ?? "",
      ),

      cron_password: $raw_settings["cron_password"] ?? die("No cron_password in settings"),

    );

    return self::$instance ??= $settings;

  }

}