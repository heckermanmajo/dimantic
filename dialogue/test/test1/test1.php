<?php

use cls\App;

include __DIR__ . "/../../cls/App.php";

App::init_context(basename(__FILE__));
# get logging functions
[$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

if (file_exists(__DIR__ . "/testdb.sqlite")){
  unlink(__DIR__ . "/testdb.sqlite");
}

App::$cli_db_path = __DIR__ . "/testdb.sqlite";
$app = App::get();


\cls\TestDataLib::insertUsers($app);

\cls\TestDataLib::create_default_dialogue_blue_prints($app);



