<?php
declare(strict_types=1);

use cls\App;
use cls\data\space\pageviews\SpacePageBlueprints;
use cls\data\space\pageviews\SpacePageConversations;
use cls\data\space\pageviews\SpacePageCreate;
use cls\data\space\pageviews\SpacePageEdit;
use cls\data\space\pageviews\SpacePageFeed;
use cls\data\space\pageviews\SpacePageFilter;
use cls\data\space\pageviews\SpacePageInfo;
use cls\data\space\pageviews\SpacePageMembers;
use cls\data\space\pageviews\SpacePageMyMembershipSettings;
use cls\data\space\pageviews\SpacePageNewBlueprint;
use cls\data\space\pageviews\SpacePageNewDocument;
use cls\data\space\pageviews\SpacePageNewSubspace;
use cls\data\space\Space;
use cls\HtmlUtils;
use cls\StringUtils;


include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

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
  if ($space->current_user_has_access($app)) {

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

      <a class="sketch-button tab-button <?php $is_selected("filter") ?>" href="/space.php?p=filter&id=<?= $_GET["id"] ?>">
        Search </a>
      <a class="sketch-button tab-button <?php $is_selected("blueprints") ?>" href="/space.php?p=blueprints&id=<?= $_GET["id"] ?>"> My
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
    # todo: empty ond default??
    switch ($_GET["p"] ?? "default") {

      case "filter":
        echo SpacePageFilter::display($space, $app);
        break;
      case "blueprints":
        echo SpacePageBluePrints::display($space, $app);
        break;
      case "conversations":
        echo SpacePageConversations::display($space, $app);
        break;

      case "new_subspace":
        echo SpacePageNewSubspace::display($space, $app);
        break;

      case "new_blueprint":
        echo SpacePageNewBlueprint::display($space, $app);
        break;

      case "new_document":
        echo SpacePageNewDocument::display($space, $app);
        break;

      case "feed":
        echo SpacePageFeed::display($space, $app);
        break;

      case "create":
        echo SpacePageCreate::display($space, $app);
        break;


      case "edit":
        echo SpacePageEdit::display($space, $app);
        break;
      case "info":
        echo SpacePageInfo::display($space, $app);
        break;
      case "my_membership_settings":
        echo SpacePageMyMembershipSettings::display($space, $app);
        break;
      case "members":
        echo SpacePageMembers::display($space, $app);
        break;
    }


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


  HtmlUtils::footer($app);
}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}