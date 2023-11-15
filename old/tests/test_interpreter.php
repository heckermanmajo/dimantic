<?php

# Run as CLI: /usr
# /bin/php /home/majo/Desktop/network/app/tests/test_interpreter.php

use cls\data\post\Post;
use cls\Interpreter;

if(php_sapi_name() == "cli"){
  include __DIR__ . "/../cls/App.php";
}
else{
  include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
}

App::$database_path = __DIR__ . "/test_interpreter.sqlite";
App::get_connection();

if(php_sapi_name() == "cli") {
  $_SERVER["DOCUMENT_ROOT"] = __DIR__ . "/..";
}


Post::create_table(App::get_connection());


Interpreter::test_get_tokens_of_line();

$post = new Post();
$post->content = "!bar";
Interpreter::execute_once_commands($post);

assert($post->command_feedback_log == "\n!!Bar command worked", "got: " . $post->command_feedback_log);
assert($post->content == "Bar was executed.", "got: " . $post->content);

$post->content = "!Foo";
$html = Interpreter::execute_always_commands($post);

assert($html == "Foo was executed.", $html);


echo "All tests passed.";

