<?php

namespace tests\tests\lib;

class TestContent {
  static function create_10_default_posts(): void {

    $if = function (string $content) {
      $post = \cls\data\post\Post::get_one(
        \App::get_connection(),
        "SELECT * FROM Post WHERE `content` = ?",
        [$content],
      );
      return $post == null;
    };

    if ($if("foo")) {
      $foo = new \cls\data\post\Post();
      $foo->content = "foo";
      $foo->save(\App::get_connection());
    }

    if ($if("bar")) {
      $bar = new \cls\data\post\Post();
      $bar->content = "bar";
      $bar->save(\App::get_connection());
    }

    if ($if("baz")) {
      $baz = new \cls\data\post\Post();
      $baz->content = "baz";
      $baz->save(\App::get_connection());
    }

    if ($if("qux")) {
      $qux = new \cls\data\post\Post();
      $qux->content = "qux";
      $qux->save(\App::get_connection());
    }

    if ($if("quux")) {
      $quux = new \cls\data\post\Post();
      $quux->content = "quux";
      $quux->save(\App::get_connection());
    }

    if ($if("corge")) {
      $corge = new \cls\data\post\Post();
      $corge->content = "corge";
      $corge->save(\App::get_connection());
    }

    if ($if("grault")) {
      $grault = new \cls\data\post\Post();
      $grault->content = "grault";
      $grault->save(\App::get_connection());
    }

    if ($if("garply")) {
      $garply = new \cls\data\post\Post();
      $garply->content = "garply";
      $garply->save(\App::get_connection());
    }

    if ($if("waldo")) {
      $waldo = new \cls\data\post\Post();
      $waldo->content = "waldo";
      $waldo->save(\App::get_connection());
    }

    if ($if("fred")) {
      $fred = new \cls\data\post\Post();
      $fred->content = "fred";
      $fred->save(\App::get_connection());
    }
  }
}