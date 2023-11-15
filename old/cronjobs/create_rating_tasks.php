<?php

include $_SERVER['DOCUMENT_ROOT'] . "/cls/App.php";

/**
 * Create rating tasks:
 *
 * - When to call this cronjob?
 * - When its called how many tasks should be created?
 * - What members should be targeted with the tasks?
 *
 */

const N = 10;

# todo: for the start just random and the members with les open tasks

# get all dimensions with at least N posts
$dimensions = \cls\data\league\AttentionDimension::get_array(
  App::get_connection(),
  "
    SELECT * FROM AttentionDimension 
             WHERE id IN 
                (
                    SELECT attention_dimension_id FROM AttentionDimensionEntry 
                        GROUP BY attention_dimension_id HAVING COUNT(*) >= :n
                 )
 ",
  [":n" => N * 2]
);
# get the post-pairs
# get N random posts of one dimension 2 x
foreach ($dimensions as $dimension) {
  $posts = \cls\data\post\Post::get_array(
    App::get_connection(),
    "SELECT * FROM Post WHERE id IN (
        SELECT post_id FROM AttentionDimensionEntry 
                       WHERE attention_dimension_id = :dimension_id ORDER BY RANDOM() LIMIT :n)",
    [":dimension_id" => $dimension->id, ":n" => N*2]
  );
  // split the array
  $posts_left = array_slice($posts, 0, N);
  usort($posts_left, function ($a, $b) {
    return $a->virtue <=> $b->virtue;
  });
  $posts_right = array_slice($posts, N, N);
  usort($posts_right, function ($a, $b) {
    return $a->virtue <=> $b->virtue;
  });

  //now get post pairs by zipping
  $post_pairs = array_map(null, $posts_left, $posts_right);

  # todo: we need a better way to get better suited members for rating ...
  # todo: aber auch keine fachdiotenschaft züchten - > Mischung aus best suited und random?


  # -> the task that will be created, will be selected based on the preferences of the attention-profile
  $members = \cls\data\account\Account::get_array(
    App::get_connection(),
    "SELECT *, 
       (SELECT COUNT(*) WHERE RatingTask.target_member_id = `Account`.id 
                          AND `RatingTask`.status = 'open')
           as _number_of_open_rating_tasks FROM `Account` ORDER BY _number_of_open_rating_tasks LIMIT :n",
    [":n" => N]
  );

  foreach ($members as $counter => $member){
    $post_pair = $post_pairs[$counter];
    $rating_task = new \cls\data\RatingTask();
    $rating_task->post_left_id = $post_pair[0]->id;
    $rating_task->post_right_id = $post_pair[1]->id;
    $rating_task->target_member_id = $member->id;
    $rating_task->potential_geistmark_amount = 1;
    $rating_task->attention_dimension_id = $dimension->id;
    $rating_task->status = "open";
    $rating_task->create_date = date("Y-m-d H:i:s");
    $rating_task->save(App::get_connection());
  }

}
# sort by virtue
# map both sorted lists on each other

























