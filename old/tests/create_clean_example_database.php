<?php

if(php_sapi_name() == "cli"){
  include __DIR__ . "/../cls/App.php";
}
else{
  include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
}
if(php_sapi_name() == "cli") {
  $_SERVER["DOCUMENT_ROOT"] = __DIR__ . "/..";
}

unlink( $_SERVER["DOCUMENT_ROOT"] . App::$database_path);

#App::$database_path = __DIR__ . "/test_interpreter.sqlite";
App::get_connection();


App::create_tables();


\tests\tests\lib\TestMembers::create_10_default_users();
\tests\tests\lib\TestAttentionLeagues::create();
\tests\tests\lib\TestAttentionDimensions::create();
\tests\tests\lib\TestPosts::create();
\tests\tests\lib\TestIdeaSpaces::create();