<?php

declare(strict_types=1);

use cls\data\account\Account;
use cls\data\account\Action;
use cls\data\account\Impression;
use cls\data\attention_profile\AttentionDimensionInterestEntry;
use cls\data\attention_profile\AttentionHistoryEntry;
use cls\data\attention_profile\AttentionProfile;
use cls\data\attention_profile\NewsEntry;
use cls\data\idea_space\IdeaSpace;
use cls\data\idea_space\IdeaSpaceMembership;
use cls\data\league\AttentionDimension;
use cls\data\league\AttentionLeague;
use cls\data\league\AttentionLeagueSeason;
use cls\data\league\AttentionLeagueSeasonPostEntry;
use cls\data\league\AttentionLeagueSupportRatingEntry;
use cls\data\league\LeagueRatingDimension;
use cls\data\post\ObservationEntry;
use cls\data\post\Post;
use cls\data\post\PostMembership;
use cls\data\post\PostSupportEntry;
use cls\data\tree\Node;
use cls\data\tree\Tree;

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('log_errors', '0');

ob_start();
// autoloader classes on namespace
spl_autoload_register(function ($class) {
  $class = str_replace('\\', '/', $class);
  if (php_sapi_name() == "cli") {
    $file = __DIR__ . "/../" . "$class.php";
  }
  else {
    $file = $_SERVER['DOCUMENT_ROOT'] . "/$class.php";
  }
  if (file_exists($file)) include_once $file;
});
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

# comment
class App {
  static ?PDO $__db = null;
  static string $database_path = "/../db.sqlite";
  static array $logs = [];
  static bool $debug_mode = true;
  /** @var array<string> List of logs from the home command execution. */
  static array $command_result_logs = [];
  /** The buffer where home commands can write to. Put beneath the home-command-line. */
  static string $page_buffer = "";
  /** @var array<string, string> An array to store the feedback for a form that is handled on the same page */
  static array $form_feedback = [];

  static ?\cls\data\attention_profile\AttentionProfile $attention_profile = null;

  static function reset_database(){
    App::$__db = null;
  }

  static function login(Account $account): void {
    $_SESSION["account"] = $account;

    # todo: we dont want the last one, but the current selected one
    #       for this to work, write it into the account as a field
    $attention_profile = \cls\data\attention_profile\AttentionProfile::get_one(
      App::get_connection(),
      "SELECT * FROM `AttentionProfile` WHERE `owner_member_id` = ? ORDER BY `id` DESC LIMIT 1;",
      [$account->id]
    );

    if ($attention_profile == null) {
      $attention_profile = new \cls\data\attention_profile\AttentionProfile();
      $attention_profile->owner_member_id = $account->id;
      $attention_profile->title = date("Y-m-d H:i:s");
      $attention_profile->description = "This is your first attention path. Here are all your visited posts logged.";
      $attention_profile->created_at = date("Y-m-d H:i:s");
      $attention_profile->save(App::get_connection());
    }

    App::set_attention_profile($attention_profile);
  }

  static function somebody_is_logged_in(): bool {
    return isset($_SESSION["account"]) && $_SESSION["account"] instanceof Account;
  }

  static function get_current_account(): Account {
    return $_SESSION["account"] ?? throw new Exception("No account logged in.");
  }

  static function get_connection(): PDO {
    #self::$__db;
    if (self::$__db !== null) {
      return self::$__db;
    }
    #$user_name = "d03df7ed";
    #$db_name = "d03df7ed";
    #$password = "pT8PRx4kQHu4GoVznQQM";
    #$url = "frellow.de";
    #$dsn = "mysql:host=$url;dbname=$db_name;charset=utf8mb4";
    #$__db = new PDO($dsn, $user_name, $password);
    #$__db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // sqlite 3
    try {
      $__db = new PDO("sqlite:" . $_SERVER['DOCUMENT_ROOT'] . App::$database_path);
    }
    catch (Throwable $t) {
      echo "sqlite:" . $_SERVER['DOCUMENT_ROOT'] . App::$database_path;
      var_dump($t);
    }
    $__db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // set timeout 1000
    $__db->exec("PRAGMA busy_timeout = 1000;");
    App::$logs[] = "get_connection: " . App::$database_path;
    self::$__db = $__db;
    return $__db;
  }

  static function head_html(string $style = "") {
    ?>
    <!DOCTYPE html>
    <head lang="<?=!App::somebody_is_logged_in() ? "en" : App::get_current_account()->language?>">

      <!-- jquery -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <!-- jstreehttps://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/jstree.min.js -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/jstree.min.js"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/themes/default/style.min.css"/>
      <script src="https://kit.fontawesome.com/ac3fc65406.js" crossorigin="anonymous"></script>
      <script src="/res/tree.js"></script>
      <script src="/res/observe.js"></script>
      <link rel="stylesheet" href="/res/w3.css">
      <link rel="stylesheet" href="/res/main_styles.css">
      <style>
          body {
              background-color: #1d1e20;
              color: whitesmoke;
              overflow: hidden;
          }
          <?=$style?>
      </style>
      <title>Dimantic</title>

    </head>
    <?php
  }

  static function put_logs(): void {
    if (!App::$debug_mode) return;

    if (isset($_SESSION["last_api_call_logs"])) {
      ?>
      <h3>Last API-Logs</h3>
      <pre>
      <?php
      foreach ($_SESSION["last_api_call_logs"] as $log) {
        App::$logs[] = $log;
      }
    }
    ?>
    </pre>
    <h4>This request logs </h4>
    <pre>
      <?php
      foreach (App::$logs as $log) {
        echo $log . "\n";
      }
      ?>
    </pre>
    <?php
  }

  static function get_correct_time(string $date, string $format = 'Y-m-d H:i:s') {
    $datetime = new DateTime($date);
    // apply timezone
    if (App::somebody_is_logged_in()) {
      $datetime->setTimezone(new DateTimeZone(App::get_current_account()->time_zone));
    }

    if ($format == "distance") {
      # vor 12 minuten
      $now = new DateTime();
      $interval = $now->diff($datetime);
      $minutes = $interval->format('%i');
      $hours = $interval->format('%h');
      $days = $interval->format('%d');
      $months = $interval->format('%m');
      $years = $interval->format('%y');
      if ($years > 0) {
        return $years . " years ago";
      }
      if ($months > 0) {
        return $months . " months ago";
      }
      if ($days > 0) {
        return $days . " days ago";
      }
      if ($hours > 0) {
        return $hours . " hours ago";
      }
      if ($minutes > 0) {
        return $minutes . " minutes ago";
      }
      return "just now";
    }

    // format date
    return $datetime->format($format);
  }

  static function set_attention_profile(AttentionProfile $attention_profile): void {
    App::$attention_profile = $attention_profile;
    $_SESSION["selected_attention_profile"] = $attention_profile;
  }

  static function echo_nav_bar() {
    ?>
    <a class="main-nav-button" href="/index.php"> Start </a>
    &nbsp;
    <a class="main-nav-button" href="/account.php"> Account </a>
    &nbsp;
    <a class="main-nav-button" href="/admin.php"> Admin </a>
    &nbsp;&nbsp;
    <?php
  }

  static function page_cleanup() {
    foreach ($_SESSION as $name => $_) {
      if (str_ends_with($name, "_api_err")) {
        unset($_SESSION[$name]);
      }
    }

    while (count($_SESSION["uri_history"]) > 10) {
      array_shift($_SESSION["uri_history"]);
    }
  }

  static function footer() {
    ?>
    <br><br><br>    <br><br><br>    <br><br><br>    <br><br><br>    <br><br><br>
    <br><br><br>    <br><br><br>    <br><br><br>    <br><br><br>
    <footer>
      IMPRESSUM
    </footer>
    <?php
    App::put_logs();
    App::page_cleanup();
    ?>
    </body>
    </html>
    <?php
  }

  static function assert($cond, $message) {
    if (!$cond) {
      throw new Exception($message);
    }
  }

   /**
   * Get either a Gravatar URL or complete image tag for a specified email address.
   *
   * @param string $email The email address
   * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
   * @param string $d Default imageset to use [ 404 | mp | identicon | monsterid | wavatar ]
   * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
   * @param bool $img True to return a complete IMG tag False for just the URL
   * @param array $atts Optional, additional key/value attributes to include in the IMG tag
   * @return String containing either just a URL or a complete image tag
   * @source https://gravatar.com/site/implement/images/php/
   */
  static function get_gravatar( $email, $s = 80, $d = 'mp', $r = 'g', $img = false, $atts = array() ) {
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
      $url = '<img src="' . $url . '"';
      foreach ( $atts as $key => $val )
        $url .= ' ' . $key . '="' . $val . '"';
      $url .= ' />';
    }
    return $url;
  }

  static function create_tables(): void {
    Account::create_table(App::get_connection());
    Action::create_table(App::get_connection());
    Impression::create_table(App::get_connection());
    AttentionDimensionInterestEntry::create_table(App::get_connection());
    AttentionHistoryEntry::create_table(App::get_connection());
    AttentionProfile::create_table(App::get_connection());
    NewsEntry::create_table(App::get_connection());
    IdeaSpace::create_table(App::get_connection());
    IdeaSpaceMembership::create_table(App::get_connection());
    AttentionDimension::create_table(App::get_connection());
    AttentionLeague::create_table(App::get_connection());
    AttentionLeagueSeason::create_table(App::get_connection());
    AttentionLeagueSeasonPostEntry::create_table(App::get_connection());
    AttentionLeagueSupportRatingEntry::create_table(App::get_connection());
    LeagueRatingDimension::create_table(App::get_connection());
    ObservationEntry::create_table(App::get_connection());
    Post::create_table(App::get_connection());
    PostMembership::create_table(App::get_connection());
    PostSupportEntry::create_table(App::get_connection());
    Node::create_table(App::get_connection());
    Tree::create_table(App::get_connection());
  }

}


App::$attention_profile = $_SESSION["selected_attention_profile"] ?? null;