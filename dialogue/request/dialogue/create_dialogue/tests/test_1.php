<?php
declare(strict_types=1);
use cls\App;

include __DIR__ . '/../../../../cls/App.php';
App::init_cli_test_context();
include __DIR__ . '/../create_dialogue.php';

$test_app = App::get_test();
$result = create_dialoge(
  app: $test_app,
  post_data: [],
);
# todo: expect here a RequestError

