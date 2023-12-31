<?php

namespace cls;

use cls\data\conversation_blue_print\ConversationBluePrint;

class TestDataLib {
  /**
   * @throws \Exception
   */
  static function insertUsers(): array {

    $data = [
      [
        "name" => "majo2",
        "email" => "kek@kek.de",
        "content" => "Majo2",
        "password" => password_hash("123", PASSWORD_DEFAULT),
      ],
      [
        "name" => "majo",
        "email" => "kek@kek2.de",
        "content" => "Majo3",
        "password" => password_hash("123", PASSWORD_DEFAULT),
      ],
      [
        "name" => "majo3",
        "email" => "kek@kek3.de",
        "content" => "Majo4",
        "password" => password_hash("123", PASSWORD_DEFAULT),
      ]
    ];

    $accounts = [];
    foreach ($data as $account_data) {
      $account = new \cls\data\account\Account();
      foreach ($account_data as $key => $value) {
        $account->$key = $value;
      }
      $account->save(db: App::get()->get_database());
      $accounts[] = $account;
    }
    return $accounts;
  }

  static function create_default_dialogue_blue_prints(): array {
    $data = [];

    $blueprints = [];
    $blueprints[] = ConversationBluePrint::getDefaultConfigurationDialogue();

    foreach ($data as $blue_print_data) {
      $blue_print = new ConversationBluePrint();
      foreach ($blue_print_data as $key => $value) {
        $blue_print->$key = $value;
      }
      $blue_print->save(App::get()->get_database());
      $blueprints[] = $blue_print;
    }

    return $blueprints;
  }

}