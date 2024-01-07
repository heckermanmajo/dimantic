<?php

use cls\App;
use cls\data\space\Space;
use cls\GetDisplayCardInterface;

return function (Space $space, App $app): string {
  ob_start();
  ?>
  <form method="post">
    <input type="hidden" name="action" value="space_text_search">
    <input type="hidden" name="space_id" value="<?= $space->id ?>">
    <input type="text" name="search_string" placeholder="Search">
    <button class="sketch-button"> <img src="/res/search.svg" width="20"> </button>
  </form>

  <?php
  if($app->executed_action == "space_text_search"){
    if($app->action_error != null){
      echo $app->action_error->get_error_card($app);
    }
    else{
      /**
       * @var $array_of_search_results GetDisplayCardInterface[]
       */
      $array_of_search_results = $app->success_result;
      foreach($array_of_search_results as $search_result){
        echo $search_result->get_display_card($app);
      }
    }
  }


  ?>
  <!--
  <button> All </button>
  <button> Subspaces </button>
  <button> Conversations </button>
  <button> Conversation-Offerings </button>
  <button> Documents </button>

  <br>
-->
  <!--<label>
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
    -->
  <?php
  return ob_get_clean();
};