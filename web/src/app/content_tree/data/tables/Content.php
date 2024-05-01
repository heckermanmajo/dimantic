<?php

namespace src\app\content_tree\data\tables;

use src\core\table\Table;

/**
 *  - question
 *  - discussion
 *  - ratings
 *  - pred-market
 *  - definitions
 *  - subtrees
 *  - rules
 *  - contracts
 */
class Content extends Table {
  function __construct(
    public bool   $frozen = false,# in archive means frozen
    public string $content = "", # the md content, can be empty
    public string $link = "", # link to som external site

    array         $data_from_db = []) {
    parent::__construct($data_from_db);
  }
}