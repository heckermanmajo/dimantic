<?php

namespace cls\data\dialoge;

use cls\App;
use cls\DataClass;
use Exception;

class DialogueMessageSelectionLike extends DataClass {
  var int $dialogue_message_id = 0;
  var int $dialogue_id = 0;
  var int $account_id = 0;
  var string $selection = "";
  var int $created_at = 0;

  /**
   * This function returns the sum of the len of all
   * selections of a person in one dialogue.
   *
   * This is therefore the number of used like credits
   * within the dialogue.
   *
   * @param int $dialogue_id
   * @param int $account_id
   * @return int
   *
   * @throws Exception
   */
  static function get_all_used_like_credits_per_person_per_dialogue(
    int $dialogue_id,
    int $account_id
  ): int {
    $sql = "
      SELECT SUM(LENGTH(selection)) as sum
      FROM DialogueMessageSelectionLike
      WHERE dialogue_id = :dialogue_id
      AND account_id = :account_id
    ";
    $params = [
      "dialogue_id" => $dialogue_id,
      "account_id" => $account_id,
    ];
    $result = static::get_sum(
      App::get()->get_database(),
      $sql,
      $params
    );
    return $result;
  }

  /**
   * @throws Exception
   * @param int $dialogue_message_id
   * @return DialogueMessageSelectionLike[]
   */
  static function get_all_like_selections_of_message(
    int $dialogue_message_id
  ): array {
    return static::get_array(
      pdo: App::get()->get_database(),
      sql: "
        SELECT *
        FROM DialogueMessageSelectionLike
        WHERE dialogue_message_id = :dialogue_message_id
      ",
      params: [
        "dialogue_message_id" => $dialogue_message_id,
      ]
    );
  }
}