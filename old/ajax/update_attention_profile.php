<?php

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

$id = $_POST["id"] ?? throw new Exception("No id given.");
$attention_profile = \cls\data\attention_profile\AttentionProfile::get_one(
  App::get_connection(),
  "SELECT * FROM `AttentionProfile` WHERE `id` = ?;",
  [$id]
);

if($attention_profile == null){
  throw new Exception("Attention path not found.");
}
if(isset($_POST["title"])){
  $attention_profile->title = $_POST["title"];
}
if (isset($_POST["description"])) {
  $attention_profile->description = $_POST["description"];
}

$attention_profile->save(App::get_connection());

App::set_attention_profile($attention_profile);