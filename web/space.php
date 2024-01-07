<?php
declare(strict_types=1);

use cls\App;
use cls\data\space\Space;
use cls\HtmlUtils;
use cls\StringUtils;


require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

try {

  App::init_context(basename_file: basename(path: __FILE__));
  $app = App::get();
  $app->init_database();
  [$log, $warn, $err, $todo]
    = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  if (!$app->somebody_logged_in()) {
    header("Location: /index.php");
  }

  $app->handle_action_requests();

  HtmlUtils::head(
    style: "
      .tab-button{
        text-decoration: none;
        color: black;
        padding: 10px;
        border: 1px solid black;
      }
      
      .tab-button:hover{
        background-color: #ddd;
      }
      .currently-open-tag{
        background-color: lightblue;
        color: white;
      }
    "
  );

  $space = Space::get_by_id(pdo: $app->get_database(), id: (int)$_GET["id"]);
  ?>
  <a href="/index.php" class="sketch-button " style="margin-top: 6px"> <img src="/res/back.svg" width="30px"></a>
  <?php
  if ($space->current_user_has_access()) {

    $is_selected = function ($p) {
      if (($_GET["p"] ?? "feed") == $p) echo "currently-open-tag";
    };

    ?>


    <h2><?= StringUtils::get_title_from_md_content($space->content) ?></h2>
    <div style="display:inline-block">

      <a
        class="sketch-button tab-button <?php $is_selected("feed") ?>"
        href="/space.php?p=feed&id=<?= $_GET["id"] ?>"
      > Mainfeed </a>

      <a
        class="sketch-button tab-button <?php $is_selected("create") ?>"
        href="/space.php?p=create&id=<?= $_GET["id"] ?>"
      > Create </a>

      <a class="sketch-button tab-button <?php $is_selected("filter") ?>"
         href="/space.php?p=filter&id=<?= $_GET["id"] ?>">
        Search </a>
      <a class="sketch-button tab-button <?php $is_selected("blueprints") ?>"
         href="/space.php?p=blueprints&id=<?= $_GET["id"] ?>"> My
        Blueprints </a>
      <a class="sketch-button tab-button <?php $is_selected("conversations") ?>"
         href="/space.php?p=conversations&id=<?= $_GET["id"] ?>"> My Conversations </a>
    </div>

    <div class="w3-right" style="display:inline-block">
      <a
        class="tab-button sketch-button "
        href="/space.php?p=info&id=<?= $_GET["id"] ?>">
        <img width="12px" src="/res/info.svg">
      </a>
      <a
        class="tab-button sketch-button "
        href="/space.php?p=members&id=<?= $_GET["id"] ?>">
        <img width="26px" src="/res/members.svg">
      </a>
      <a
        class="tab-button sketch-button "
        href="/space.php?p=my_membership_settings&id=<?= $_GET["id"] ?>">
        <img width="20px" src="/res/my_settings.svg">
      </a>
      <!-- My settings -->
      <a
        class="tab-button sketch-button "
        href="/space.php?p=edit&id=<?= $_GET["id"] ?>">
        <img width="40px" src="/res/space_settings.svg">
      </a>
    </div>
    <br><br>
    <?php
    $snippet_path = fn($file_name) => $_SERVER["DOCUMENT_ROOT"] . "/page_snippets/space/$file_name";

    echo match ($_GET["p"] ?? "default") {
      "edit"                   => (require($snippet_path(file_name: "edit.php")))(space: $space, app: $app),
      "info"                   => (require($snippet_path(file_name: "info.php")))(space: $space, app: $app),
      "blueprint_manager"      => (require($snippet_path(file_name: "blueprint_manager.php")))(space: $space, app: $app),
      "conversations"          => (require($snippet_path(file_name: "my_conversations.php")))(space: $space, app: $app),
      "create"                 => (require($snippet_path(file_name: "create_stuff_menu.php")))(space: $space, app: $app),
      "feed"                   => (require($snippet_path(file_name: "feed.php")))(space: $space, app: $app),
      "filter"                 => (require($snippet_path(file_name: "filter.php")))(space: $space, app: $app),
      "my_membership_settings" => (require($snippet_path(file_name: "my_membership_settings.php")))(space: $space, app: $app),
      "blueprints"             => (require($snippet_path(file_name: "blueprint_manager.php")))(space: $space, app: $app),
      "new_blueprint"          => (require($snippet_path(file_name: "new_blueprint.php")))(space: $space, app: $app),
      "new_document"           => (require($snippet_path(file_name: "new_document.php")))(space: $space, app: $app),
      "new_subspace"           => (require($snippet_path(file_name: "new_subspace.php")))(space: $space, app: $app),
      "members"                => (require($snippet_path(file_name: "members.php")))(space: $space, app: $app),
      default                  => (require($snippet_path(file_name: "feed.php")))(space: $space, app: $app),
    };
  }
  else {
    ?>
    <div class='info-card'>⚠️You are not allowed to access this space.
      <br>
      Since you are not a member.
      <br>
      Do you want to join?
    </div>
    <?php
    echo $app->markdown_to_html($space->content);
    ?>
    <form class="w3-card w3-margin w3-padding" method="post">
      <?php
      if ($app->executed_action == "create_space_membership") {
        echo $app->action_error?->get_error_card();
      }
      ?>
      <input type="hidden" name="action" value="create_space_membership">
      <input type="hidden" name="space_id" value="<?= $space->id ?>">
      <button class="w3-button w3-green">Join Space</button>
    </form>
    <?php
  }


  HtmlUtils::footer();
}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}