<?php

namespace src\app\space\data\tables;

use src\app\space\data\enums\SpaceType;
use src\core\table\Table;

/**
 * A space is a context of communication of a collective
 * this collective can be comprised of only one person.
 *
 *
 */
final class Space extends Table {

  function __construct(

    public string        $title = "",

    public int $author_id = 0,

    public SpaceType     $type = SpaceType::DEFAULT_SPACE,

    array                $data_from_db = []

  ) {
    parent::__construct($data_from_db);
  }

  function given_account_is_author(int $account_id): bool {
    return $this->author_id === $account_id;
  }

}