<?php

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

$attention_dimensions = \cls\data\league\AttentionDimension::get_array(
  App::get_connection(),
  "SELECT * FROM AttentionDimension WHERE AttentionDimension.id NOT IN (
        SELECT AttentionDimensionInterestEntry.attention_dimension_id 
        FROM AttentionDimensionInterestEntry WHERE AttentionDimensionInterestEntry.attention_profile_id = ?);",
  [
    App::$attention_profile->id
  ]
);

foreach ($attention_dimensions as $dimension) {
  ?>
  <div class="w3-card-4 w3-margin w3-padding" id="attention_dimension_select_card_<?=$dimension->id?>">
    <h3><?= $dimension->title ?></h3>
    <p><?= $dimension->description ?></p>
    <button
      onclick="
        $.post(
          '/ajax/create_attention_dimension_interest_entry.php',
          {
            attention_dimension_id: <?= $dimension->id ?>
          }
        ).done(function (data) {
          $('#attention_dimension_select_card_<?=$dimension->id?>').html(data);
        });
      "
      class="button"> Add to this Attention Profile </button>
  </div>
  <?php
}