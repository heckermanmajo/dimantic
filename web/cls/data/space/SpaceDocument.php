<?php

namespace cls\data\space;

use cls\App;
use cls\DataClass;

/**
 * Space Document 🚀
 *
 * A space document is a representation of a piece of information.
 * It is a wrapper for a piece of content, so we can create algorithms
 * that order and sort them.
 *
 * It can have different types, but always the same structure.
 *
 * - can have a file associated
 * - can have a link associated
 * - can have a tree of documents and conversations
 * - can have a conversation associated
 * - can have a date associated (deadline, event, etc.)
 * - can have a member associated
 * - can have a space/subspace associated
 *
 * So a document is a piece of content, created by
 * a user.
 *
 * Other users can clone it and edit it, the other
 * versions will be linked to the original and
 * vice versa.
 *
 * Later also bots can auto generate documents.
 *
 * They can be referenced like this:
 * www.dimantic.com?space=1&document=2
 *
 */
class SpaceDocument extends DataClass {

  /** The id of the space this document belongs to.*/
  var int $space_id = 0;

  /** The id of the original author of the document.*/
  var int $author_id = 0;

  /** If the author leaves a space, the document is now owned by a space admin. */
  var int $owner_id = 0;

  /** The id of the document this document is a clone of.*/
  var int $clone_parent_id = 0;

  /** The type of association this document has. */
  var string $association_type = "";

  /**
   * The association this document has.
   * uses the default association format:
   *
   * - space=id&conversation=id f.e. "space=45&conversation=34"
   * - space=id&document=id f.e. "space=344&document=346"
   * - space=id&member=id f.e. "space=4&member=745"
   * - space=id&space=id f.e. "space=5&space=3564"
   *
   * This can directly be appended to the url.
   * f.e. "www.dimantic.com?space=1&document=2"
   *
   * @note It can happen that a document contains data, that belongs to another
   *       space where not all members have access to. In this case the document
   *       is only shown to the members that have access to the other space.
   *
   */
  var string $association = "";

  /** Description of the document in markdown.*/
  var string $content = "";

  /** Used for example for a js-tree, canvas, etc. */
  var string $json_content = "";

  function get_document_card(): string {
    ob_start();
    ?>
    <div class="w3-card w3-margin w3-padding">
      <pre><?= json_encode(value: $this, flags: JSON_PRETTY_PRINT) ?></pre>
    </div>
    <?php
    return ob_get_clean();
  }

  /**
   * @throws \Exception
   */
  static function get_all_documents_of_space(int $space_id): array {
    return static::get_array(
      pdo: App::get()->get_database(),
      sql: "SELECT * FROM space_document WHERE space_id = :space_id",
      params: ["space_id" => $space_id],
    );
  }

}