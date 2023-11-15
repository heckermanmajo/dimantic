<?php
include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

if (!App::somebody_is_logged_in()) {
  header("Location: /index.php");
  exit();
}

$id = $_GET["id"] ?? 0;

$account = \cls\data\account\Account::get_one(
  App::get_connection(),
  "SELECT * FROM `Account` WHERE `id` = ?;",
  [$id]
);

?>
<br>
<a class='button w3-margin' href='/index.php'> Zurück </a> <br>
<?php
App::head_html();

$account->put_display_card();

