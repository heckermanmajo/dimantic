<?php

namespace cls\controller\request\idea_space;

use App;
use cls\data\idea_space\IdeaSpace;
use cls\RequestError;

class CreateIdeaSpace {

  static function execute(): RequestError|IdeaSpace {

    if(!isset($_POST["content"])){
      return new RequestError(
        "Error while creating idea space: 'content' not set.",
        RequestError::BAD_REQUEST
      );
    }

    $content = $_POST["content"];
    # todo: check fo correctness
    $idea_space = new IdeaSpace();
    $idea_space->author_id = App::get_current_account()->id;
    # todo: does not work yet
    # $idea_space->description = Interpreter::execute_always_commands($content);
    $idea_space->description = $content;
    $idea_space->created_date = date("Y-m-d H:i:s");
    $idea_space->save(App::get_connection());

    return $idea_space;

  }

}