<?php

namespace cls\data\post;

use App;

trait PostQueries {

  /**
   * @return array<int, Post>
   * @throws \Exception
   */
  static function get_my_posts(): array {
    return Post::get_array(
      App::get_connection(),
      "SELECT *,
          (SELECT `name` FROM Account WHERE Account.id = `Post`.`author_id`) AS _author_name,
          (SELECT COUNT(*)  FROM `Post` as P WHERE P.parent_post_id = Post.id ) AS _number_of_direct_children,
          (SELECT COUNT(*) FROM `PostMembership` as PM WHERE PM.post_id = Post.id ) AS _number_of_members,
          (SELECT COUNT(*) FROM `ObservationEntry` as O WHERE O.post_id = Post.id AND O.attention_path_id = :attention_path_id ) AS _is_observed
          FROM `Post` WHERE `author_id` = :author_id;",
      ["author_id" => App::get_current_account()->id, "attention_path_id" => App::$attention_profile->id]
    );
  }


  /**
   * @return array<int, Post>
   * @throws \Exception
   */
  static function get_posts_by_attention_profile(int $attention_profile_id): array {
    return Post::get_array(
      App::get_connection(),
      "SELECT * FROM `Post` WHERE `id` IN (
            SELECT `post_id` FROM `AttentionHistoryEntry` WHERE `attention_path_id` = ?
           ) ORDER BY `id` DESC;",
      [$attention_profile_id]
    );
  }


  static function get_one_by_id(int $id): ?static {
    return Post::get_one(
      App::get_connection(),
      "SELECT *,
             (SELECT `name` FROM Account WHERE Account.id = `Post`.`author_id`) AS _author_name,
             (SELECT COUNT(*)  FROM `Post` as P WHERE P.parent_post_id = Post.id ) AS _number_of_direct_children,
             (SELECT COUNT(*) FROM `PostMembership` as PM WHERE PM.post_id = Post.id ) AS _number_of_members,
             (SELECT COUNT(*) FROM `ObservationEntry` as O WHERE O.post_id = Post.id AND O.attention_path_id = :attention_path_id ) AS _is_observed
            FROM `Post` WHERE `id` = :id;",
      ["id" => $id, "attention_path_id" => App::$attention_profile->id]
    );
  }

  /**
   * @param int $parent_post_id
   * @return Post[]
   */
  static function get_direct_children_of_post(int $parent_post_id): array {
    $direct_children = Post::get_array(
      App::get_connection(),
      "SELECT *,
                 (SELECT `name` FROM Account WHERE Account.id = `Post`.`author_id`) AS _author_name,
                 (SELECT COUNT(*)  FROM `Post` as P WHERE P.parent_post_id = Post.id ) AS _number_of_direct_children,
                 (SELECT COUNT(*) FROM `PostMembership` as PM WHERE PM.post_id = Post.id ) AS _number_of_members
                 FROM `Post` WHERE `parent_post_id` = ?;",
      [$parent_post_id]
    );
    return $direct_children;
  }

}