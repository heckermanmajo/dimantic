<?php
declare(strict_types=1);

namespace cls;

use cls\data\account\Account;
use cls\data\account\NewsEntry;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueMembership;
use cls\data\dialoge\DialogueMessage;
use PDO;
use ReflectionException;

/**
 * Returns true if the script is running in cli mode.
 *
 * Cli mode is used for tests and possible analysis
 * where we want to use dataclasses and other
 * already written utility functions.
 */
function FN_IS_CLI(): bool {
  return php_sapi_name() == "cli";
}

/**
 * This function detects if the user is on a mobile device.
 *
 * Used for different css style-includes and possibly
 * different html structure.
 *
 * @todo: implement ...
 */
function FN_IS_MOBILE(): bool {
  return false;
}

/**
 * Debug mode allows to see error messages and also
 * get the application logs.
 *
 * If you develop on this project you should
 * add your name to the list of developers
 * with the home and username of your computer.
 *
 */
function FN_IS_DEBUG(): bool {
  # cli mode is always debug mode
  if (FN_IS_CLI()) {
    return true;
  }
  $root_path = $_SERVER["DOCUMENT_ROOT"];
  $developers = [
    "/home/majo/",
    # ...
    # add your home path here ...
  ];
  foreach ($developers as $developer) {
    if (str_contains(haystack: $root_path, needle: $developer)) {
      return true;
    }
  }
  return false;
}

# set init to mbstring
#mb_internal_encoding(encoding: "UTF-8");


error_reporting(error_level: E_ALL);
if (FN_IS_DEBUG()) {
  ini_set(option: 'display_errors', value: '1');
  ini_set(option: 'display_startup_errors', value: '1');
  ini_set(option: 'log_errors', value: '0');
}
else {
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
 * Therefore we need to include all functions
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
  }
  else {
    $file = $_SERVER['DOCUMENT_ROOT'] . "/$class.php";
  }
  if (file_exists(filename: $file)) include_once $file;
}

// autoloader: classes on namespace
spl_autoload_register(callback: FN_AUTOLOAD(...));

# Start the session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) session_start();


/**
 * App is the main state class of the application.
 *
 * Usually it is passed in as a instance into most functions
 * and in the server-mode only one instance is created and used.
 *
 * However, for test cases we need to create multiple instances
 * and want more control over the "simulated" state of the application.
 */
class App {

  /**
   * On the server we only have one instance of App.
   * This is where the instance is stored.
   * Singleton pattern.
   */
  private static ?App $instance = null;

  /**
   * Logs are always global.
   * So we collect them in this array.
   */
  private static array $logs = [];

  /**
   * The database connection.
   * Can be mysql or sqlite.
   * If null, then the connection is not yet established.
   */
  var ?PDO $db = null;
  /**
   * We don't change the session, except through the
   * App functions.
   */
  private array $session = [];

  static function get_logs(): array {
    return static::$logs;
  }

  static function get(): App {
    if (self::$instance == null) {
      self::$instance = new App();
    }
    return self::$instance;
  }

  /**
   * This function is a helper to create standard test instances.
   * @todo: implement ...
   */
  static function get_test_instance(): App { }

  static function init_cli_test_context(): void { }

  /**
   * Init context is called at the start of each page and each request (if the request is called directly).
   * It is used to initialize the state of the application.
   */
  static function init_context(
    string $basename_file,
  ): void {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    #$log("init_context");
    #$warn("init_context");
    #$err("init_context");
    #$todo("init_context");
  }

  private function __construct(
    string $db_type = "sqlite",
    ?array $session = null
  ) {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    if ($session == null) {
      $log("read session from global variable", $_SESSION);
      $this->session = &$_SESSION;
    }
    else {
      $log("read session from parameter", $session);
      $this->session = &$session;
    }
  }

  function set_session_field(string $name, mixed $value): void {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    $log("set_session_field", [$name, $value]);
    $this->session[$name] = $value;
  }

  function get_session_field(string $name, mixed $default = null): mixed {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    $log("get_session_field", [$name, $this->session[$name] ?? $default]);
    return $this->session[$name] ?? $default;
  }

  /**
   * This function is called at the end for each page.
   */
  function page_clean_up(): void { }

  /**
   * This function is called at the end for each request.
   * Only if the request was called directly as a json endpoint.
   */
  function request_clean_up(): void { }

  /**
   * Returns true if somebody is logged in.
   * Based on the account in the session.
   */
  function somebody_logged_in(): bool {
    return ($this->session["account"] ?? null) != null;
  }

  /**
   * Returns the account of the currently logged-in user.
   * If nobody is logged in, then an exception is thrown.
   */
  function get_currently_logged_in_account(): Account {
    # todo: assert that somebody is logged in
    if (!$this->somebody_logged_in()) {
      throw new \Exception("Nobody is logged in.");
    }
    return $this->session["account"];
  }

  /**
   * Logs in the given account.
   * This is used when the user logs in.
   */
  function login(Account $account): void {
    # todo: error when somebody is already logged in (somebody else)
    $this->session["account"] = $account;
  }

  /**
   * Returns the database connection.
   * Currently only sqlite is supported.
   */
  function get_database(): PDO {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    if ($this->db == null) {
      $log("connection to sqlite-database at " . $_SERVER["DOCUMENT_ROOT"] . "/../dimantic.sqlite");
      $this->db = new PDO("sqlite:" . $_SERVER["DOCUMENT_ROOT"] . "/../dimantic.sqlite");
      $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $this->db;
  }

  /**
   * This function creates the database and also updates fields
   * that are not yet in the database.
   *
   * So it creates non-existing tables and adds changes.
   *
   * Non needed fields are NOT removed.
   *
   * @throws ReflectionException
   */
  function init_database(): void {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    $log("Creates all tables and updates them if they already exist.");
    $db = $this->get_database();

    Account::create_table($db);
    Dialogue::create_table($db);
    DialogueMembership::create_table($db);
    DialogueMessage::create_table($db);
    NewsEntry::create_table($db);
    # add new tables (Dataclasses) here ...
    # ...
  }

  /**
   * Returns an array of 4 logging functions.
   *
   * The first one is the normal log function.
   * The second one is the warning function.
   * The third one is the error function.
   * The fourth one is the todo function.
   *
   * Use the array destructuring syntax to get the functions.
   *
   * @param string $class __CLASS__ - magic constant of the calling class
   * @param string $function __FUNCTION__ - magic constant of the calling function
   * @return array<callable>
   * @example
   * [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__);
   *
   */
  static function get_logging_functions(
    string $class,
    string $function,
    string $file = "",
    int    $line = 0
  ): array {
    App::$logs[] = "($file:::$line)[$class:$function]";
    return [
      function (string $message, null|array|string $data = null) use ($class, $function) {
        static::$logs[] = "[$class:$function] $message";
        if ($data != null) {
          if (is_array($data)) {
            $data = json_encode($data, JSON_PRETTY_PRINT);
          }
          static::$logs[] = $data;
        }
      },
      function (string $message, null|array|string $data = null) use ($class, $function) {
        static::$logs[] = "[$class:$function] WARNING $message";
        if ($data != null) {
          if (is_array($data)) {
            $data = json_encode($data, JSON_PRETTY_PRINT);
          }
          static::$logs[] = $data;
        }
      },
      function (string $message, null|array|string $data = null) use ($class, $function) {
        static::$logs[] = "[$class:$function] ERROR $message";
        if ($data != null) {
          if (is_array($data)) {
            $data = json_encode($data, JSON_PRETTY_PRINT);
          }
          static::$logs[] = $data;
        }
      },
      function (string $message, null|array|string $data = null) use ($class, $function) {
        static::$logs[] = "[$class:$function] TODO $message";
        if ($data != null) {
          if (is_array($data)) {
            $data = json_encode($data, JSON_PRETTY_PRINT);
          }
          static::$logs[] = $data;
        }
      },
    ];
  }

  /**
   * Dump all te logs as html.
   * Used at the end of a page.
   *
   * @example
   * try{
   *  ...
   *  App::dump_logs(); #  in HtmlUtils::footer
   * } catch(\Throwable $t) {
   *  App::dump_logs($t);
   * }
   *
   * @see HtmlUtils::footer
   * @see index.php (at the end of the file) (and any other page)
   */
  static function dump_logs(?\Throwable $t = null): void {
    if (!FN_IS_DEBUG()) return; # no logs in production mode
    if ($t != null) {
      echo "<pre class='w3-card w3-margin w3-padding'>";
      echo "<b style='color: #9c27b0'>";
      echo $t->getMessage();
      echo "</b>";
      echo "<br><br>";
      echo $t->getFile() . ":<b>" . $t->getLine() . "</b>";
      echo "<br><br>";
      echo $t->getTraceAsString();
      echo "</pre>";
    }
    echo "<hr><pre style='background-color: #3a3a3a'  class='w3-card w3-margin w3-padding'>";
    foreach (static::$logs as $log) {
      # todo:  echo "\033[0;31m"; in cli mode
      if (str_contains($log, "ERROR")) {
        echo "<span style='color: red'>";
      }
      elseif (str_contains($log, "WARNING")) {
        echo "<span style='color: orange'>";
      }
      elseif (str_contains($log, "TODO")) {
        echo "<span style='color: #ffff00'>";
      }
      else {
        echo "<span style='color: rgb(222,222,222)'>";
      }
      echo "$log\n";
      echo "</span>";
    }
    echo "</pre>";
    ?>
    <?php
  }

}