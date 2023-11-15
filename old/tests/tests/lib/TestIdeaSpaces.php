<?php

namespace tests\tests\lib;

class TestIdeaSpaces {
  static function create() {

    $idea_space = new \cls\data\idea_space\IdeaSpace();
    $idea_space->author_id = 1;
    $idea_space->description = "CDU/CSU Ideen";
    $idea_space->created_date = date("Y-m-d H:i:s");
    $idea_space->open = 1;
    $idea_space->save(\App::get_connection());

    $idea_space = new \cls\data\idea_space\IdeaSpace();
    $idea_space->author_id = 2;
    $idea_space->description = "Grüne Ideen";
    $idea_space->created_date = date("Y-m-d H:i:s");
    $idea_space->open = 1;
    $idea_space->save(\App::get_connection());

    $idea_space = new \cls\data\idea_space\IdeaSpace();
    $idea_space->author_id = 1;
    $idea_space->description = "SPD Ideen";
    $idea_space->created_date = date("Y-m-d H:i:s");
    $idea_space->open = 1;
    $idea_space->save(\App::get_connection());

    $idea_space = new \cls\data\idea_space\IdeaSpace();
    $idea_space->author_id = 1;
    $idea_space->description = "FDP Ideen";
    $idea_space->created_date = date("Y-m-d H:i:s");
    $idea_space->open = 1;
    $idea_space->save(\App::get_connection());

    $idea_space = new \cls\data\idea_space\IdeaSpace();
    $idea_space->author_id = 1;
    $idea_space->description = "Linke Ideen";
    $idea_space->created_date = date("Y-m-d H:i:s");;
    $idea_space->open = 1;
    $idea_space->save(\App::get_connection());

    $idea_space = new \cls\data\idea_space\IdeaSpace();
    $idea_space->author_id = 1;
    $idea_space->description = "AfD Ideen";
    $idea_space->created_date = date("Y-m-d H:i:s");;
    $idea_space->open = 1;
    $idea_space->save(\App::get_connection());

    $idea_space = new \cls\data\idea_space\IdeaSpace();
    $idea_space->author_id = 1;
    $idea_space->description = "Naturwissenschaft";
    $idea_space->created_date = date("Y-m-d H:i:s");
    $idea_space->open = 1;
    $idea_space->save(\App::get_connection());

    $idea_space = new \cls\data\idea_space\IdeaSpace();
    $idea_space->author_id = 1;
    $idea_space->description = "Geisteswissenschaft";
    $idea_space->created_date = date("Y-m-d H:i:s");
    $idea_space->open = 1;
    $idea_space->save(\App::get_connection());

    $idea_space = new \cls\data\idea_space\IdeaSpace();
    $idea_space->author_id = 1;
    $idea_space->description = "Philosophie";
    $idea_space->created_date = date("Y-m-d H:i:s");
    $idea_space->open = 1;
    $idea_space->save(\App::get_connection());

    $idea_space = new \cls\data\idea_space\IdeaSpace();
    $idea_space->author_id = 1;
    $idea_space->description = "Abrahamische Ideen";
    $idea_space->created_date = date("Y-m-d H:i:s");
    $idea_space->open = 1;
    $idea_space->save(\App::get_connection());

    /*$idea_space = new \cls\data\idea_space\IdeaSpace();
    $idea_space->author_id = 1;
    $idea_space->description = "Naturwissenschaft";
    $idea_space->created_date = date("Y-m-d H:i:s");
    $idea_space->open = 1;
    $idea_space->save(\App::get_connection());*/

  }
}