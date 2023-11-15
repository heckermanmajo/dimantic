<?php

namespace cls\data\tree;

use App;
use cls\RequestError;

trait CreateTreeRequest {
  static function create_tree_request(): Tree|RequestError {
    try {
      $tree = new \cls\data\tree\Tree();
      $tree->author_id = App::get_current_account()->id;
      $given_type = $_GET["type"] ?? "content";
      # todo: check correct type ...
      $tree->type = $given_type;
      $given_name = $_GET["name"] ?? "[no title]";
      $tree->name = $given_name;
      $tree->save(App::get_connection());
    }

    catch (\PDOException $t) {
      return new RequestError(
        "Error while logging in: PDOException.",
        RequestError::SYSTEM_ERROR,
        e: $t
      );
    }

    catch (\Throwable $t) {
      return new RequestError(
        "Error while logging in.",
        RequestError::SYSTEM_ERROR,
        e: $t
      );
    }

    return $tree;
  }
}