<?php

use cls\controller\request\root\UpdateDBStructureRequest;
use cls\data\post\Post;
use tests\tests\lib\TestAttentionDimensions;
use tests\tests\lib\TestMembers;

include __DIR__ . "/../../../cls/App.php";
$_SERVER["DOCUMENT_ROOT"] = __DIR__ . "/../../..";
App::$database_path = "/tests/controller/rating_controller/test_rating_controller.sqlite";
function reset_db() {
  App::reset_database();
  if (file_exists($_SERVER["DOCUMENT_ROOT"] . App::$database_path)) {
    unlink($_SERVER["DOCUMENT_ROOT"] . App::$database_path);
  }
  UpdateDBStructureRequest::execute();
  TestMembers::create_10_default_users();
  TestAttentionDimensions::create();
  # TestPosts::create();
}

echo __FILE__ . " \n";
reset_db();

// create 1000 posts
for ($i = 0; $i < 1000; $i++) {
  $post = new Post();
  $post->content = "test content";
  $post->created_at = date("Y-m-d H:i:s");
  $post->save(App::get_connection());

  // the post is competing in one dimension
  $attention_dimension_entry = new \cls\data\AttentionDimensionEntry();
  $attention_dimension_entry->post_id = $post->id;
  $attention_dimension_entry->attention_dimension_id = 1;
  // we want for test purposes a different order
  $attention_dimension_entry->position_points = $i+1;
  $attention_dimension_entry->number_of_ratings = 50; // eac post has 50 ratings
  $attention_dimension_entry->save(App::get_connection());
}


// calculate the positons in a dimension for all posts
for ($i = 1; $i < 1001; $i++) {
  $post = Post::get_by_id(
    App::get_connection(),
    $i
  );

  // the post is competing in one dimension
  $attention_dimension_entry = \cls\data\AttentionDimensionEntry::get_one(
    App::get_connection(),
    "SELECT * FROM `AttentionDimensionEntry` WHERE `post_id` = ? AND `attention_dimension_id` = ?;",
    [
      $post->id,
      1
    ]
  );

  $postion = \cls\controller\algo\PostVirtue::get_absolute_postion_value_of_entry_within_dimension(
    $attention_dimension_entry
  );
  #echo "Post($post->id)-position: " . $postion . ", points:  $attention_dimension_entry->position_points "." number_of_ratings:".$attention_dimension_entry->number_of_ratings ."\n";
  assert($postion == $post->id); # since a higher position is better
}

// make text green in console
echo "\033[32m test 1 passed ✅ \n";
echo "\033[0m";

// now collect the virtue values for all posts

$results = [];
for ($i = 1; $i < 1001; $i++) {
  $post = Post::get_by_id(
    App::get_connection(),
    $i
  );

  // the post is competing in one dimension
  $attention_dimension_entry = \cls\data\AttentionDimensionEntry::get_one(
    App::get_connection(),
    "SELECT * FROM `AttentionDimensionEntry` WHERE `post_id` = ? AND `attention_dimension_id` = ?;",
    [
      $post->id,
      1
    ]
  );

  $value =  \cls\controller\algo\PostVirtue::generate_virtue_value_for_post_based_on_rating_values_of_its_entries(
    $post
  );
  #echo "$value for post $post->id\n";
  #echo "$value,";
  #if($i%20 == 0) {
  #  echo "\n";
  #}
  $results[] = $value;
}


assert($results == [
    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
    0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,100,
    100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,
    100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,
    100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,
    100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,
    100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,
    100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,
    100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,
    100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,
    100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,
    100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,
    250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,250,500,
    500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,
    500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,
    500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,
    500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,
    500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,
    500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,
    500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,500,
    500,500,500,500,500,500,500,500,500,500,1000,1000,1000,1000,1000,1000,1000,1000,1000,1000,
    1000,1000,1000,1000,1000,1000,1000,1000,1000,1000,1000,1000,1000,1000,1000,1000,1000,1000,1000,1000,
    1000,1000,1000,1000,1000,1000,1000,1000,1000,1000,2000,2000,2000,2000,2000,2000,2000,2000,2000,2000,
  ], "The results are not as expected. Got" . json_encode($results));

echo "\033[32m test 2 passed ✅ \n";
echo "\033[0m";