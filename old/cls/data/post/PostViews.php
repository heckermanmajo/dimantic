<?php

namespace cls\data\post;

use App;
use cls\Interpreter;

trait PostViews {

  function echo_meta_info_top_bar(): void {
    $idea_space = null;
    if ($this->idea_space_id != 0) {
      $idea_space = \cls\data\idea_space\IdeaSpace::get_one(
        App::get_connection(),
        "SELECT * FROM IdeaSpace WHERE id = :id;",
        ["id" => $this->idea_space_id]
      );
    }
    $league = null;
    $season = null;
    if($this->liga_season_id != 0) {
      $league = $this->get_league();
      $season = $this->get_season();
    }

    $support_of_post  = self::get_count(
      App::get_connection(),
      "SELECT COUNT(*) FROM PostSupportEntry WHERE post_id = ?;",
      [$this->id]
    );

    /** @var $this Post */
    ?>
    <div>
      <?= App::get_correct_time($this->created_at, "distance") ?>
      [[ <b><?= $this->id ?></b> ]] by <b><?= $this->_author_name ?></b>
      (💬 <b><?= $this->_number_of_direct_children ?></b>)
      <!--(👥 <b><?= $this->_number_of_members ?></b>)-->
      (💪 <b><?= $support_of_post ?></b>)
      <div style="display: inline">
        <?php
        if ($this->_is_observed): ?>
          <button class="unobserve-button" onclick="observe(<?= $this->id ?>, 'post', this)"> unobserve ❌
          </button>
        <?php
        else:
          ?>
          <button
            class="observe-button"
            onclick="observe(<?= $this->id ?>, 'post', this)"> Observe 🔭
          </button>
        <?php endif; ?>
      </div>
      <?php
      if ($idea_space == null) {
        // pass
      }
      else {
        ?>
        <p>Belongs to IdeaSpace:
            <a style="text-decoration: none; color: #ffd205" href="/idea_space.php?id=<?=$idea_space->id?>">
              <b><?= $idea_space->get_title() ?></b>
          </a></p>
        <?php
      }

      if($league != null){
        ?>
        <p>This post is competing in
          <a style="text-decoration: none; color: #ffd205"
          href="/league.php?id=<?=$league->id?>">
            <b><?=$league->get_title()?></b>
          </a>
        </p>
        <p>In <?=$season->season_description?></p>
        <?php
      }

      ?>
    </div>
    <?php
  }



  function echo_overview_display_card(): void {
    /** @var $this Post */
    ?>
    <div class="w3-card w3-padding w3-margin">
      <?php $this->echo_meta_info_top_bar() ?>
      <a href="/post.php?id=<?= $this->id ?>" style="text-decoration: none">
        <h3><?= $this->get_title() ?></h3>
        <div style="font-size: 80%"><?= $this->get_short_desc() ?></div>
      </a>
      <br>
      <?php
      /*
      $dimensions_you_can_rate_on = \cls\controller\algo\RatingController::get_attention_dimension_entries_the_current_user_can_rate_on($this);
      if (count($dimensions_you_can_rate_on) > 0) {
        ?>
        <a class="button" href="/rate.php?id=<?= $this->id ?>">You can rate this post
          in <?= count($dimensions_you_can_rate_on) ?> Dimensions</a>
        <?php
      }*/
      ?>
    </div>
    <?php
  }

  function echo_read_all_display_card(): void {
    /** @var $this Post */
    ?>
    <script>
      window.read_all_post_view_<?=$this->id?>_edit_on_spot_button = function () {
        $.post(
          '/ajax/get_edit_on_the_spot_view.php',
          {
            id: <?=$this->id?>
          },
          function (data) {
            $('#read_all_post_view_' + <?=$this->id?>).html(data);
          }
        );
      }
    </script>
    <div class="w3-card w3-padding w3-margin" id="read_all_post_view_<?= $this->id ?>">
      <?php $this->echo_meta_info_top_bar() ?>
      <a href="/post.php?id=<?= $this->id ?>" style="text-decoration: none">
        <h3><?= $this->get_title() ?></h3>
        <div>
          <?php
          $content = Interpreter::execute_always_commands($this);
          if (trim($content) == "" || trim($content) == "<br>") {
            echo "No content in post with id ($this->id) ...";
          }
          else {
            echo $content;
          }
          ?>
        </div>
      </a>
      <?php
      if (App::get_current_account()->id === $this->author_id) {
        ?>
        <br>
        <div>
          <button class="button" onclick="read_all_post_view_<?= $this->id ?>_edit_on_spot_button()">Edit on the spot
          </button>
        </div>
        <?php
      }
      ?>
    </div>
    <?php
  }


}