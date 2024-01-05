<?php

namespace cls\data\space;

use cls\DataClass;

/**
 * If you want your document to bes displayed on the attention-board of a space,
 * you create such an entry, that links the document to the wanted place
 * on the attention-board.
 */
class AttentionBoardPostEntry extends DataClass {
  var int $attention_board_category_id = 0;
  var int $document_id = 0;
  var int $author_id = 0;
  var int $created_at = 0;
  var int $support = 0;
}