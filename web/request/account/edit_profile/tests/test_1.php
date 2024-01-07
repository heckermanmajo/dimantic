<?php

use cls\App;

include __DIR__ . "/../../../../cls/App.php";

# todo: insert path to test-database
App::init_cli_test_context();
App::get_test_instance();


## todo: create test