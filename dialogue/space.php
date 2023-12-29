<?php
declare(strict_types=1);

use cls\App;
use cls\data\space\pageviews\SpacePageAgora;
use cls\data\space\pageviews\SpacePageEdit;
use cls\data\space\pageviews\SpacePageFilter;
use cls\data\space\pageviews\SpacePageInfo;
use cls\data\space\pageviews\SpacePageMembers;
use cls\data\space\pageviews\SpacePageMyMembershipSettings;
use cls\data\space\pageviews\SpacePageSubSpaces;
use cls\data\space\pageviews\SpacePageWiki;
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
    style:"
      .tab-button{
        text-decoration: none;
        color: black;
        padding: 10px;
        border: 1px solid black;
      }
      
      .tab-button:hover{
        background-color: #ddd;
      }
    "
  );

  $space = Space::get_by_id(pdo: $app->get_database(), id: (int)$_GET["id"]);

  ?>

  <a href="/index.php"> ◀️ Back </a>
  <h2><?= StringUtils::get_title_from_md_content($space->content)?></h2>
  <div style="display:inline-block">

    <a class="tab-button" href="/space.php?p=wiki&id=<?=$_GET["id"]?>"> Wiki📚 </a> <!--(documents & closed Conversations) -->
    <a class="tab-button" href="/space.php?p=agora&id=<?=$_GET["id"]?>"> Agora💬 </a>
    <a class="tab-button" href="/space.php?p=subspaces&id=<?=$_GET["id"]?>"> Rooms🌳 </a>

    <a class="tab-button" href="/space.php?p=filter&id=<?=$_GET["id"]?>"> by 👑 </a> <!--by Authority -->
    <a class="tab-button" href="/space.php?p=filter&id=<?=$_GET["id"]?>"> by 🧠 </a> <!--by Most Matching -->
    <a class="tab-button" href="/space.php?p=filter&id=<?=$_GET["id"]?>"> by ⏱️ </a> <!-- by Recency-->

  </div>

  <div class="w3-right"  style="display:inline-block">
    <a class="tab-button" href="/space.php?p=info&id=<?=$_GET["id"]?>"> ℹ️ </a>
    <a class="tab-button" href="/space.php?p=members&id=<?=$_GET["id"]?>"> 👥 </a>
    <a class="tab-button" href="/space.php?p=my_membership_settings&id=<?=$_GET["id"]?>"> ⚙️👤 </a> <!-- My settings -->
    <a class="tab-button" href="/space.php?p=edit&id=<?=$_GET["id"]?>"> Edit Space🛠️ </a>
  </div>

  <?php
  switch($_GET["p"] ?? "default"){
    case "agora":
      echo SpacePageAgora::display($space,$app);
      break;
    case "wiki":
      echo SpacePageWiki::display($space,$app);
      break;
    case "filter":
      echo SpacePageFilter::display($space,$app);
      break;
    case "subspaces":
      echo SpacePageSubSpaces::display($space,$app);
      break;
    case "edit":
      echo SpacePageEdit::display($space,$app);
      break;
    case "info":
      echo SpacePageInfo::display($space, $app);
      break;
    case "my_membership_settings":
      echo SpacePageMyMembershipSettings::display($space,$app);
      break;
    case "members":
      echo SpacePageMembers::display($space,$app);
      break;
  }



  ?>




  <?php
  HtmlUtils::footer($app);
}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}