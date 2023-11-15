<?php

if(php_sapi_name() == "cli"){
  include __DIR__ . "/../cls/App.php";
}
else{
  include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
}

App::$database_path = __DIR__ . "/test_rating_1_interpreter.sqlite";
App::get_connection();

if(php_sapi_name() == "cli") {
  $_SERVER["DOCUMENT_ROOT"] = __DIR__ . "/..";
}