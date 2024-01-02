<?php

namespace cls\data\space\pageviews;

use cls\App;
use cls\data\space\Space;

class SpacePageFilter {
  static function display(Space $space, App $app): string {
    ob_start();
    ?>
    <h3> Filter </h3>
    <input type="text" placeholder="Search">
    <button> All </button>
    <button> Subspaces </button>
    <button> Conversations </button>
    <button> Conversation-Offerings </button>
    <button> Documents </button>
    <br>

    <label>
      <input type="radio" name="order" value="by_relevance">
      by personal relevance
    </label>
    &nbsp;&nbsp;&nbsp;
    <label>
      <input type="radio" name="order" value="by_authority">
      by authority
    </label>
    &nbsp;&nbsp;&nbsp;
    <label>
      <input type="radio" name="order" value="by_recency">
      by recency
    </label>

    <pre>
      Here you can filter all stuff
    </pre>
    <?php
    return ob_get_clean();
  }
}