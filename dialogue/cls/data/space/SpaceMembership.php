<?php

namespace cls\data\space;

use cls\App;
use cls\DataClass;
use Exception;

/**
 * Membership of a person in a space.
 */
class SpaceMembership extends DataClass {
  /**
   * Consul are the owner of the space.
   * The can do everything and remove and create other roles.
   */
  const ROLE_CONSUL = 1;
  /**
   * Tribun are the moderators of the space selected by their
   * contribution to the space.
   * They can be dismissed by the consul. (maybe more complex process)
   */
  const ROLE_TRIBUN = 2;
  /**
   * Ministers are just selected by the consul.
   * BUT the consul has direct responsibility for the ministers.
   */
  const ROLE_MINISTER = 3;
  /**
   * Members are just members.
   * They can be promoted to ministers by the consul.
   */
  const ROLE_MEMBER = 4;
  /**
   * Guests are just guests.
   * They can be promoted to members by the consul or
   * increase the rank if they contribute to the space.
   */
  const ROLE_GUEST = 5;

  var int $member_id = 0;
  var int $space_id = 0;
  var int $role = 0;
  var int $created_at = 0;

  /**
   * @param App $app
   * @param int $space_id
   * @return array<int,SpaceMembership>
   * @throws Exception
   */
  static function get_all_memberships_of_space(
    App $app,
    int $space_id
  ): array {
    return static::get_array(
      pdo: $app->get_database(),
      sql: "SELECT * FROM SpaceMembership WHERE space_id = ?",
      params: [$space_id],
    );
  }

  /**
   * @return string
   */
  function get_card(App $app): string {
    ob_start();
    ?>
    <div class="w3-card w3-margin w3-padding">
      <pre><?= json_encode($this, JSON_PRETTY_PRINT) ?></pre>
      <?php
      if ($this->member_id == $app->get_currently_logged_in_account()->id) {
        ?>
        <form class="w3-card w3-margin w3-padding" method="post">
          <?php
          if (
            $app->executed_action == "delete_space_membership"
            && ($_POST["space_membership_id"] == 0) == $this->id
          ) {
            echo $app->action_error?->get_error_card();
          }
          ?>
          <input type="hidden" name="action" value="delete_space_membership">
          <input type="hidden" name="space_id" value="<?= $this->space_id ?>">
          <input type="hidden" name="space_membership_id" value="<?= $this->id ?>">
          <button class="w3-button w3-red">Leave Space</button>
        </form>
        <?php
      }
      ?>
    </div>
    <?php
    return ob_get_clean();
  }
}