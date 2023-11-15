<?php

namespace cls\controller\command\once;

use cls\data\post\Post;
use cls\data\post\PostMembership;

class Invite implements \cls\Command {

  static function execute(Post $post, array $tokens, array &$not_executed_and_error_message_lines): void {

    $membername = $tokens[1] ?? null;

    if ($membername === null) {
      $not_executed_and_error_message_lines[] = "!err Error: Invite misses membername";
      $post->command_error_log .= "\n!err Invite command failed; Invite misses membername";
      $not_executed_and_error_message_lines[] = "!err Usage: !invite <membername>";
      $not_executed_and_error_message_lines[] = implode(" ", $tokens);
      return;
    }

    $account = \cls\data\account\Account::get_one(
      \App::get_connection(),
      "SELECT * FROM `Account` WHERE `name` = ?;",
      [$membername]
    );

    if ($account === null) {
      $not_executed_and_error_message_lines[] = "!err Error: Cant find member with name $membername";
      $post->command_error_log .= "\n!err Error: Cant find member with name $membername";
      $not_executed_and_error_message_lines[] = "!err Usage: !invite <membername>";
      $not_executed_and_error_message_lines[] = implode(" ", $tokens);
      return;
    }

    $possible_membership = PostMembership::get_one(
      \App::get_connection(),
      "SELECT * FROM `PostMembership` WHERE `member_id` = ? AND `post_id` = ?;",
      [$account->id, $post->id]
    );

    if ($possible_membership !== null) {
      if($possible_membership->status === "application"){
        $possible_membership->status = "member";
        $possible_membership->save(\App::get_connection());
        $not_executed_and_error_message_lines[] = "!info Success Invite was executed.";
        return;
      }
      $not_executed_and_error_message_lines[] = "!err Error: Membership entry already exists!";
      $post->command_error_log .= "\n!err Invite command failed; Membership entry already exists!.";
      return;
    }

    $membership = new PostMembership();
    $membership->member_id = $account->id;
    $membership->post_id = $post->id;
    $membership->status = "member";  # todo: or is invited better? and it needs to be accepted??
    $membership->save(\App::get_connection());

    $not_executed_and_error_message_lines[] = "!info Invite was executed.";

  }

  static function get_command_name(): string {
    return "!invite";
  }
}