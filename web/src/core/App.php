<?php
namespace src\core;

use Exception;
use PDO;
use src\core\settings\Settings;
use src\global\compositions\GetEnvironmentMode;


/**
 * This class is used by the php-autoloader.
 * When php cant find a class it calls the callback
 * registered with spl_autoload_register.
 *
 * In our case this is FN_AUTOLOAD.
 *
 * It works since in the whole project
 * all classes are in a folder structure that
 * reflects the namespace.
 *
 * Example:
 * \cls\Protocol is in the file cls/Protocol.php
 *
 * The autoloader does NOT work for functions.
 * Therefore we need to require all functions
 * via require_once.
 *
 * -> functions are only used
 * in the request folder.
 *
 * @param $class
 * @return void
 */
function FN_AUTOLOAD($class): void {
  $class = str_replace(search: '\\', replace: '/', subject: $class);
  if (php_sapi_name() == "cli") {
    $file = __DIR__ . "/../" . "$class.php";
    $_SERVER['DOCUMENT_ROOT'] = __DIR__ . "/../";
  } else {
    $file = $_SERVER['DOCUMENT_ROOT'] . "/$class.php";
  }
  if (file_exists(filename: $file)) require_once $file;
  else {
    echo "Class $class not found in $file";
    exit();
  }
}

// autoloader: classes on namespace
spl_autoload_register(callback: FN_AUTOLOAD(...));


error_reporting(error_level: E_ALL);
if (GetEnvironmentMode::is_debug()) {
  ini_set(option: 'display_errors', value: '1');
  ini_set(option: 'display_startup_errors', value: '1');
  ini_set(option: 'log_errors', value: '0');
} else {
  ini_set(option: 'display_errors', value: '0');
  ini_set(option: 'display_startup_errors', value: '0');
  ini_set(option: 'log_errors', value: '1');
}

# start output buffering
# all output is stored in a buffer and not sent to the client
# until the buffer is flushed or the script ends.
# this allows to cancel the output if an error occurs.
# and don't send half rendered html to the client.
ob_start();




// we want warnings  to be exceptions, so we can catch and log them
// into the application problems database.
set_error_handler(

  function ($errno, $errstr, $errfile, $errline) {

    if (!(error_reporting() & $errno)) {
      return;
    }

    # todo: log all warnings into the problems database
    #Problem::write_problem_message(
    #  message: "PHP Warning",
    #  extra_data: [
    #    "errno" => $errno,
    #    "errstr" => $errstr,
    #    "errfile" => $errfile,
     #   "errline" => $errline
     # ]
    #);

  }

);

# Start the session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) session_start();


#if (App::get_currently_logged_in_account() != null) {
#  App::select_attention_profile_if_none_is_selected();
#}

#if(FN_IS_DEBUG()){
#  App::initialize_and_update_database();
#}


final class App {

  /**
   * The default database connection; contains all the main data, like users
   * spaces, posts and conversations.
   */
  private static ?PDO $db = null;

  /**
   * Contains all the embeddings for all entries that need to have embeddings.
   */
  private static ?PDO $embeddings_db = null;

  /**
   * Place to log errors, usage statistics and other data that is not needed
   * for the main application to work, but is useful for the developer to
   * understand how the application is used.
   */
  private static ?PDO $usage_db = null;

  /**
   * Returns the database connection.
   *
   * @param string $what_database "default" | "embeddings" | "usage"
   *
   * @throws Exception
   * @see Settings::get_instance()
   */
  static function get_database(string $what_database = "default"): PDO {
    $settings = Settings::get_instance();
    return match ($what_database) {
      "default" => self::$db ??= $settings->db_credentials->get_connection(),
      "embeddings" => self::$embeddings_db ??= $settings->embeddings_db_credentials->get_connection(),
      "usage" => self::$usage_db ??= $settings->usage_db_credentials->get_connection(),
      default => throw new Exception("Unknown database requested: $what_database")
    };
  }

}