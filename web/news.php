<?php
declare(strict_types=1);

use cls\App;
use cls\data\account\news\InviteToLobbyNewsEntry;
use cls\GetDisplayCardInterface;
use cls\HtmlUtils;


require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

try {

  App::init_context(basename_file: basename(path: __FILE__));
  $app = App::get();
  $app->init_database();
  [$log, $warn, $err, $todo]
    = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  if (!$app->somebody_logged_in()) {
    # header("Location: /index.php");
  }

  $app->handle_action_requests();

  HtmlUtils::head();
  HtmlUtils::main_header();

  /**
   * @var $news array<GetDisplayCardInterface>
   */
  $news = [
    ...InviteToLobbyNewsEntry::get_news_for_account(
      account_id: $app->get_currently_logged_in_account()->id
    )
  ];


  ?>
  <h3>DEINE NEWS !!!!</h3>

  <?php
  foreach ($news as $n){
    echo $n->get_display_card();
  }


  HtmlUtils::footer();
}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}