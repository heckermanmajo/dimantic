<?php

use cls\controller\algo\RatingController;
use cls\controller\request\root\UpdateDBStructureRequest;
use cls\data\account\Account;
use cls\data\post\Post;
use tests\tests\lib\TestAttentionDimensions;
use tests\tests\lib\TestMembers;
use tests\tests\lib\TestPosts;

include __DIR__ . "/../../../cls/App.php";
$_SERVER["DOCUMENT_ROOT"] = __DIR__ . "/../../..";
App::$database_path = "/tests/controller/rating_controller/test_rating_controller.sqlite";
function reset_db(){
  App::reset_database();
  if (file_exists($_SERVER["DOCUMENT_ROOT"] . App::$database_path)) {
    unlink($_SERVER["DOCUMENT_ROOT"] . App::$database_path);
  }
  UpdateDBStructureRequest::execute();
  TestMembers::create_10_default_users();
  TestAttentionDimensions::create();
  TestPosts::create();
}
echo "test_rating_controller.php: \n";
reset_db();

// login majo
$majo = Account::get_by_id(App::get_connection(), 1);
App::login($majo);

# post one is our post
$array_of_dimension_entries = RatingController::get_attention_dimension_entries_the_current_user_can_rate_on(
  Post::get_by_id(App::get_connection(), 1)
);

assert(count($array_of_dimension_entries) == 0, "got: " . count($array_of_dimension_entries));
echo "\033[32m test 1 passed ✅ \n";
echo "\033[0m";

# post two is not our post -> expect 3
$array_of_dimension_entries = RatingController::get_attention_dimension_entries_the_current_user_can_rate_on(
  Post::get_by_id(App::get_connection(), 2)
);

assert(count($array_of_dimension_entries) == 3, "got: " . count($array_of_dimension_entries));
echo "\033[32m test 2 passed ✅ \n";
echo "\033[0m";

# ensure exactly 3 posts are in the database
$sql = "DELETE FROM `Post` WHERE id > 3;";
App::get_connection()->exec($sql);
assert(Post::get_by_id(App::get_connection(), 1) !== null);
assert(Post::get_by_id(App::get_connection(), 2) !== null);
assert(Post::get_by_id(App::get_connection(), 3) !== null);
assert(count(Post::get_array(App::get_connection(), "SELECT * FROM `Post`;")) == 3);

$sql = "DELETE FROM `AttentionDimensionEntry` WHERE AttentionDimensionEntry.post_id > 3;";
App::get_connection()->exec($sql);

$array_of_dimension_entries = RatingController::get_attention_dimension_entries_the_current_user_can_rate_on(
  Post::get_by_id(App::get_connection(), 2)
);

assert(count($array_of_dimension_entries) == 3, "got: " . count($array_of_dimension_entries));

for($i=0; $i< 10; $i++) {
  foreach ($array_of_dimension_entries as $entry) {
    $rating_partner_entry = RatingController::get_rating_partner_post_attention_dimension_entry(
      $entry,
      JSON_PRETTY_PRINT
    );
    assert($rating_partner_entry->post_id != 1, "cannot rate on own post");
    assert($rating_partner_entry->post_id != 2, "cannot rate again on same post");
    #echo json_encode(
    #  $rating_partner_entry,
    #  JSON_PRETTY_PRINT
    #);
    #echo "\n";
  }
}
# white after newline
echo "\033[32m test 3 passed ✅ \n";
echo "\033[0m";
reset_db();

$post_to_rate = Post::get_by_id(App::get_connection(), 5);


# todo: test randomness ...

#RatingController::get_rating_partner_post_attention_dimension_entry();

if (file_exists($_SERVER["DOCUMENT_ROOT"] . App::$database_path)) {
  unlink($_SERVER["DOCUMENT_ROOT"] . App::$database_path);
}