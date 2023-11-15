<?php
declare(strict_types=1);

namespace cls\data\dialoge;

use cls\App;
use cls\DataClass;

class  DialogueMessage extends DataClass {
  ###########################################################################
  #                                                                         #
  #  Properties & Property-functions                                        #
  #                                                                         #
  ###########################################################################

  var string $content = '';
  var int $dialogue_id = 0;
  var int $account_id = 0;
  var string $create_date = '';

  #################################
  ###### Joined Values      #######
  #################################

  #################################
  ###### Property-functions #######
  #################################

  function get_dialogue(App $app): Dialogue {
    return Dialogue::get_one(
      pdo: $app->get_database(),
      sql: "SELECT * FROM `Dialogue` WHERE `id` = ?",
      params: [$this->dialogue_id]
    );
  }

  function get_preview_of_content(): string {
    // todo:  use interpreter to return first 3 lines
    return $this->content;
  }

  ###########################################################################
  #                                                                         #
  #  Model-Queries                                                          #
  #                                                                         #
  ###########################################################################

  /**
   * @param App $app
   * @param int $dialogue_id
   * @return array<DialogueMessage>
   */
  static function get_all_messages_of_dialogue(App $app, int $dialogue_id): array {
    return DialogueMessage::get_array(
      pdo: $app->get_database(),
      sql: "SELECT * FROM `DialogueMessage` WHERE `dialogue_id` = ? ORDER BY id DESC",
      params: [$dialogue_id]
    );
  }

  ###########################################################################
  #                                                                         #
  #  Logic & Controller                                                     #
  #                                                                         #
  ###########################################################################

  ###########################################################################
  #                                                                         #
  #  Views                                                                  #
  #                                                                         #
  ###########################################################################

  function get_view_card(App $app): string {

    ob_start();

    $dialogue = $this->get_dialogue($app);

    $mem = $dialogue->get_membership_of_given_account($app, $app->get_currently_logged_in_account()->id);

    $save_instance = $this->get_escaped_copy_instance();

    # if i am member and active
    # i want my messages on the left whatever
    if ($mem) {
      if ($mem->state == DialogueMembership::STATE_ACTIVE) {
        if ($this->account_id == $app->get_currently_logged_in_account()->id) {
          ?>
          <div class="w3-card-4 w3-margin w3-padding" style="margin-right: 20% !important;">
            <pre><?= $save_instance->content ?></pre>
            <!--<pre><?=json_encode($this, JSON_PRETTY_PRINT)?></pre>-->
          </div>
          <?php
          goto end;
        }
        else {
          ?>
          <div class="w3-card-4 w3-margin w3-padding" style="margin-left: 20% !important; border-color: #69ff7a">
            <pre><?= $save_instance->content ?></pre>
            <!--<pre><?=json_encode($this, JSON_PRETTY_PRINT)?></pre>-->
          </div>
          <?php
          goto end;
        }
      }
    }

    # if I am not member, i wants the authors messages on the left
    if ($dialogue->author_id == $this->account_id) {
      ?>
      <div class="w3-card-4 w3-margin w3-padding" style="margin-right: 20% !important;">
        <pre><?= $save_instance->content ?></pre>
        <!--<pre><?=json_encode($this, JSON_PRETTY_PRINT)?></pre>-->
      </div>
      <?php
    }
    else {
      ?>
      <div class="w3-card-4 w3-margin w3-padding" style="margin-left: 20% !important; border-color: #69ff7a">
        <pre><?= $save_instance->content ?></pre>
        <!--<pre><?=json_encode($this, JSON_PRETTY_PRINT)?></pre>-->
      </div>
      <?php
    }
    end:
    return ob_get_clean();
  }
}