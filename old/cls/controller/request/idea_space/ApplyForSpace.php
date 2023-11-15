<?php

namespace cls\controller\request\idea_space;

use App;
use cls\data\idea_space\IdeaSpace;
use cls\data\idea_space\IdeaSpaceMembership;
use cls\RequestError;

class ApplyForSpace {
  static function execute(): IdeaSpaceMembership|RequestError {

    if (!isset($_POST["space_id"])) {
      return new RequestError(
        "Error while applying for space: Space id is missing.",
        RequestError::BAD_REQUEST
      );
    }
    $space_id = $_POST["space_id"];

    $space = IdeaSpace::get_by_id(App::get_connection(), $space_id);
    # todo: check access rights and if exists ...

    # todo: check if membership already exists ...
    $possible_previous_membership = IdeaSpaceMembership::get_one(
      App::get_connection(),
      "SELECT * FROM IdeaSpaceMembership WHERE idea_space_id = :idea_space_id AND account_id = :account_id;",
      [
        "idea_space_id" => $space_id,
        "account_id" => App::get_current_account()->id
      ]
    );

    if($possible_previous_membership !== null){
      # todo:: add is user beeing kicked out of space/blocked ...
      $possible_previous_membership->left_idea_space = 0;
      $possible_previous_membership->save(App::get_connection());
      return $possible_previous_membership;
    }

    $membership = new IdeaSpaceMembership();
    $membership->idea_space_id = $space_id;
    $membership->account_id = App::get_current_account()->id;
    # todo: set the rest correctlsy ...

    $membership->save(App::get_connection());

    return $membership;

  }
}