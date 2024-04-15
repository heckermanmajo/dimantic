<?php
namespace src\core;


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
  # todo: make better ... -> use lib?
  $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);

  $mobileDevices = array(
    'iphone', 'ipad', 'android', 'blackberry', 'nokia',
    'opera mini', 'windows mobile', 'windows phone',
    'iemobile', 'mobile');

  foreach ($mobileDevices as $device) {
    if (strpos($userAgent, $device) !== false) {
      return true;
    }
  }

  return false;
}


const DEVELOPERS_HOME_PATHS = [
  "/home/majo/",
  # ...
  # add your home path here if you are a developer ...
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
  return true; # for alpha ....
# cli mode is always debug mode
#if (FN_IS_CLI()) {
#  return true;
#}

#$root_path = $_SERVER["DOCUMENT_ROOT"];
#foreach (DEVELOPERS_HOME_PATHS as $developer) {
#  if (str_contains(haystack: $root_path, needle: $developer)) {
#    return true;
#  }
#}
#return false;
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

function FN_GET_HOME_PATH(): string {
  foreach (DEVELOPERS_HOME_PATHS as $developer) {
    if (str_contains(haystack: $_SERVER["DOCUMENT_ROOT"], needle: $developer)) {
      return $developer;
    }
  }
  return "";
}

# set init to mbstring
#mb_internal_encoding(encoding: "UTF-8");


error_reporting(error_level: E_ALL);
if (FN_IS_DEBUG()) {
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
  if (FN_IS_CLI()) {
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

// we want warnings  to be exceptions, so we can catch and log them
// into the application problems database.
set_error_handler(

  function ($errno, $errstr, $errfile, $errline) {

    if (!(error_reporting() & $errno)) {
      return;
    }

    Problem::write_problem_message(
      message: "PHP Warning",
      extra_data: [
        "errno" => $errno,
        "errstr" => $errstr,
        "errfile" => $errfile,
        "errline" => $errline
      ]
    );

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

}