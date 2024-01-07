<?php
declare(strict_types=1);

namespace cls;

use cls\data\account\Account;

use cls\data\account\news\InviteToLobbyNewsEntry;
use cls\data\conversation_blue_print\ConversationBluePrint;
use cls\data\conversation_blue_print\Lobby;
use cls\data\conversation_blue_print\LobbyMembership;
use cls\data\conversation_blue_print\ProtoRule;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueMembership;
use cls\data\dialoge\DialogueMessage;
use cls\data\dialoge\DialogueMessageComment;
use cls\data\dialoge\DialogueMessageSelectionLike;
use cls\data\dialoge\DialogueRule;
use cls\data\dialoge\DialogueRuleRating;
use cls\data\space\Space;
use cls\data\space\SpaceDocument;
use cls\data\space\SpaceMembership;
use Exception;
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
 * Used for different css style-requires and possibly
 * different html structure.
 *
 * @todo: implement ...
 */
function FN_IS_MOBILE(): bool {
  return false;
}

const DEVELOPERS_HOME_PATHS = [
  "/home/majo/",
  # ...
  # add your home path here ...
];

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
  # todo: remove the line below after debugging is done
  return true; # for debuggng
  # cli mode is always debug mode
  if (FN_IS_CLI()) {
    return true;
  }

  $root_path = $_SERVER["DOCUMENT_ROOT"];
  foreach (DEVELOPERS_HOME_PATHS as $developer) {
    if (str_contains(haystack: $root_path, needle: $developer)) {
      return true;
    }
  }
  return false;
}

/**
 * This function  returns true, if this is run on localhost.
 * @return bool
 */
function FN_IS_LOCAL_HOST(): bool {
  $root_path = $_SERVER["DOCUMENT_ROOT"];
  foreach (DEVELOPERS_HOME_PATHS as $developer) {
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
  }
  else {
    $file = $_SERVER['DOCUMENT_ROOT'] . "/$class.php";
  }
  if (file_exists(filename: $file)) require_once $file;
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
   * @var bool If true, then the inline tests are run
   * This means tests that are put beneath classes in the source file.
   */
  public static bool $run_inline_tests = true;

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

  /**
   * @var string|null The action that was executed.
   * @see App::handle_action_requests
   */
  public ?string $executed_action = null;

  /**
   * @var RequestError|null The error that occurred during the action.
   * @see App::handle_action_requests
   */
  public ?RequestError $action_error = null;

  /**
   * @var mixed|null This field contains the result of the action if the action was successful.
   * @see App::handle_action_requests
   */
  public mixed $success_result = null;

  /**
   * Returns all the logs.
   * @return array<string> all the logs
   */
  static function get_logs(): array {
    return static::$logs;
  }

  /**
   * The path to the database file used in clientmode only
   * (f.e. for tests).
   * @var string
   */
  static string $cli_db_path = "";

  /**
   * Returns an instance of the App class.
   * - implements the singleton pattern -> only one instance
   *
   * @return App the instance of the App class
   */
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

  /**
   * This function is used when a script is run in cli mode.
   * This makes sense for tests and also for cli-run-analysis.
   * @return void
   */
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

    # todo: make this configurable -> not in debug mode
    $this->init_database();
  }

  /**
   * This function is used to set a field in the session.
   *
   * !!NO setting of the session variable directly.
   *
   * @param string $name the name of the field
   * @param mixed $value the value of the field
   *
   * @return void
   */
  function set_session_field(string $name, mixed $value): void {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    $log("set_session_field", [$name, $value]);
    $this->session[$name] = $value;
  }

  /**
   * This function is used to get a field from the session.
   * If the field is not set, then the default value is returned.
   *
   * @param string $name
   * @param mixed|null $default
   * @return mixed
   */
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
      throw new Exception("Nobody is logged in.");
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
   * Handles all the logout logic.
   *
   * @return void
   */
  function logout(): void {
    unset($this->session["account"]);
    // destroy the session
    session_destroy();
  }

  /**
   * Returns the database connection.
   * Currently only sqlite is supported.
   */
  function get_database(): PDO {
    # extra no log for a simple call of this function, since it is called very often and clutters the logs
    if ($this->db == null) {
      [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
      if (FN_IS_CLI()) {
        if(static::$cli_db_path == ""){
          throw new Exception("static::\$cli_db_path is not set.");
        }
        $log("COMMAND_LINE_INTERFACE_DETECTED_CONNECTION");
        $log("connection to sqlite-database at " . static::$cli_db_path);
        $this->db = new PDO("sqlite:" . static::$cli_db_path);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
      else {
        $log("connection to sqlite-database at " . $_SERVER["DOCUMENT_ROOT"] . "/../dimantic.sqlite");
        $this->db = new PDO("sqlite:" . $_SERVER["DOCUMENT_ROOT"] . "/../dimantic.sqlite");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }

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
   * @throws Exception
   */
  function init_database(): void {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    $log("Creates all tables and updates them if they already exist.");
    $db = $this->get_database();

    Account::create_table($db);
    Dialogue::create_table($db);
    DialogueMembership::create_table($db);
    DialogueMessage::create_table($db);
    DialogueMessageComment::create_table($db);
    DialogueRule::create_table($db);
    DialogueRuleRating::create_table($db);
    DialogueMessageSelectionLike::create_table($db);
    Space::create_table($db);
    SpaceMembership::create_table($db);
    SpaceDocument::create_table($db);
    ConversationBluePrint::create_table($db);
    ProtoRule::create_table($db);
    Lobby::create_table($db);
    LobbyMembership::create_table($db);
    InviteToLobbyNewsEntry::create_table($db);

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
    if ($class == "") {
      $file = basename($file);
      if ($function == "") {
        App::$logs[] = "&[$file($line)]";
      }
      else {
        App::$logs[] = "&[$file::$function($line)]";
      }
    }
    else {
      App::$logs[] = "&[$class::$function($line)]";
    }

    $meta_lambda = function ($class, $function, $mode) {
      return function (string $message, null|array|string $data = null) use ($class, $function, $mode) {
        if ($mode == "log") {
          static::$logs[] = "   $message";
        }
        else {
          static::$logs[] = "   ($mode)$message";
        }
        if ($data !== null) {
          if (is_array($data)) {
            $data = "   " . json_encode($data, JSON_PRETTY_PRINT);
          }
          static::$logs[] = $data;
        }
      };
    };
    return [
      $meta_lambda($class, $function, "log"),
      $meta_lambda($class, $function, "warn"),
      $meta_lambda($class, $function, "err"),
      $meta_lambda($class, $function, "todo"),
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
    echo "<hr><pre style='background-color: #3a3a3a; font-size: 85%'  class='w3-card w3-margin w3-padding'>";
    foreach (static::$logs as $log) {
      # todo:  echo "\033[0;31m"; in cli mode
      if (str_contains($log, "ERROR")) {
        echo "<span style='color: red'>";
      }
      elseif (str_contains($log, "(warn)")) {
        echo "<span style='color: orange'>";
      }
      elseif (str_contains($log, "TODO")) {
        echo "<span style='color: #ffff00'>";
      }
      else {
        echo "<span style='color: rgb(222,222,222)'>";
      }
      if (str_starts_with($log, "&[")) {
        echo "<small><b>";
        echo $log;
        echo "</b></small>";
        echo "<br>";
      }
      else {
        echo $log;
        echo "<br>";
      }
      echo "</span>";
    }
    echo "</pre>";
    ?>
    <?php
  }


  /**
   * This function is called at the top of most page-files.
   * It handles all post-requests.
   *
   * Reads all request files from the request folder maps
   * them on name and executes the request if the name matches.
   *
   * @return void
   */
  function handle_action_requests(): void {

    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

    if (!isset($_POST["action"])) {
      $log("no action to execute");
      return;
    }

    $all_requests = $_SERVER["DOCUMENT_ROOT"] . "/request/*/*/*.php";
    $all_requests = glob(pattern: $all_requests);
    $requests_mapped_on_name = [];
    foreach ($all_requests as $request) {
      $name = explode(separator: "/", string: $request);
      // request name is same name as file name
      $name = explode(separator: ".", string: $name[count($name) - 1]);
      $name = $name[0];
      $requests_mapped_on_name[$name] = $request;
    }

    foreach ($requests_mapped_on_name as $request_name => $request_file_path) {
      $log("checking action: $request_name");
      if ($_POST["action"] === $request_name) {
        $log("<span style='color: purple'>IMPORTANT: executing action: $request_name</span>");
        $this->executed_action = $request_name;
        $request_function = require($request_file_path);
        $result = $request_function($this, $_POST);

        # result is a RequestError -> action failed
        if ($result instanceof RequestError) {
          $this->action_error = $result;
          $warn("action error: " . $result->dev_message);
        }
        # result is null -> action success
        else {
          $log("<span style='color:green'>action success</span>");
          $this->action_error = null;
          $this->success_result = $result;
        }
      }
    }

    if (isset($_POST["action"]) && $this->executed_action === null) {
      $warn("unknown action: " . $_POST["action"]);
    }

    # Special treatment for login and register -> redirect to home
    if ($this->executed_action === "login" || $this->executed_action === "register") {
      if ($this->action_error === null) {
        ob_get_clean();
        header("Location: /index.php");
        exit;
      }
    }

    # special treatment for logout -> redirect to index
    if ($this->executed_action == "logout") {
      ob_get_clean();
      header("Location: /index.php");
      exit;
    }

  }

  /**
   * Converts markdown to html.
   * Don't use the parse-down library directly, so we
   * can add more functionality later, like removing unwanted tags, etc.
   *
   * - currently just uses the parsedown library in safe mode.
   *
   * @param string $markdown an input string in markdown format
   * @return string the html representation of the markdown
   *
   * @see \cls\lib\Parsedown
   */
  function markdown_to_html(string $markdown): string {
    # todo: we dont want to allow underlined text in markdown
    #       since we need the underlining for hinting meta data
    #       for the text of the message, like comments, likes, etc.
    $parsedown = new \cls\lib\Parsedown();
    $parsedown->setSafeMode(true);
    return $parsedown->text($markdown);
  }

}