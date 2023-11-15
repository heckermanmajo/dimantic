<?php

namespace tests\tests\lib;

use cls\data\account\Account;

class TestMembers {
  static function create_10_default_users(): void {

    $if = function (string $name) {
      $account = Account::get_one(
        \App::get_connection(),
        "SELECT * FROM Account WHERE `name` = ?",
        [$name],
      );
      return $account == null;
    };

    if ($if("majo")) {
      $majo = new \cls\data\account\Account();
      $majo->name = "majo";
      $majo->password = password_hash("majo", PASSWORD_DEFAULT);
      $majo->email = "majo@majo.de";
      $majo->save(\App::get_connection());
    }

    if ($if("jojo")) {
      $jojo = new \cls\data\account\Account();
      $jojo->name = "jojo";
      $jojo->password = password_hash("jojo", PASSWORD_DEFAULT);
      $jojo->email = "jojo@jojo.de";
      $jojo->save(\App::get_connection());
    }

    if ($if("hans")) {
      $hans = new \cls\data\account\Account();
      $hans->name = "hans";
      $hans->password = password_hash("hans", PASSWORD_DEFAULT);
      $hans->email = "hans@hans.de";
      $hans->save(\App::get_connection());
    }

    if ($if("hans2")) {
      $hans2 = new \cls\data\account\Account();
      $hans2->name = "hans2";
      $hans2->password = password_hash("hans2", PASSWORD_DEFAULT);
      $hans2->email = "hans2@hans2.de";
      $hans2->save(\App::get_connection());
    }

    if ($if("hans3")) {
      $hans3 = new \cls\data\account\Account();
      $hans3->name = "hans3";
      $hans3->password = password_hash("hans3", PASSWORD_DEFAULT);
      $hans3->email = "hans3@hans3.de";
      $hans3->save(\App::get_connection());
    }

    if ($if("jeff_skilling")) {
      $jeff_skilling = new \cls\data\account\Account();
      $jeff_skilling->name = "jeff_skilling";
      $jeff_skilling->password = password_hash("jeff_skilling", PASSWORD_DEFAULT);
      $jeff_skilling->email = "jeff_skilling@jeff_skilling.de";
      $jeff_skilling->save(\App::get_connection());
    }

    if ($if("kenny")) {
      $kenny = new \cls\data\account\Account();
      $kenny->name = "kenny";
      $kenny->password = password_hash("kenny", PASSWORD_DEFAULT);
      $kenny->email = "kenny@kenny.de";
      $kenny->save(\App::get_connection());
    }

    if ($if("arthur")) {
      $arthur = new \cls\data\account\Account();
      $arthur->name = "arthur";
        $arthur->password = password_hash("arthur", PASSWORD_DEFAULT);
      $arthur->email = "arthur@arthur.de";
      $arthur->save(\App::get_connection());
    }

    if ($if("kant")) {
      $kant = new \cls\data\account\Account();
      $kant->name = "kant";
      $kant->password = password_hash("kant", PASSWORD_DEFAULT);
      $kant->email = "kant@kant.de";
      $kant->save(\App::get_connection());
    }

    if ($if("merkel")) {
      $merkel = new \cls\data\account\Account();
      $merkel->name = "merkel";
      $merkel->password = password_hash("merkel", PASSWORD_DEFAULT);
      $merkel->email = "merkel@merkel.de";
      $merkel->save(\App::get_connection());
    }

  }
}