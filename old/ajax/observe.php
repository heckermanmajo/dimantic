<?php

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

$id = (int)$_POST["id"] ?? throw new Exception("No id given.");
$type = $_POST["type"] ?? throw new Exception("No type given.");

# todo: check input values ...

$attention_path_id = App::$attention_profile->id;

$possible_observe_entry = \cls\data\post\ObservationEntry::get_one(
  pdo: App::get_connection(),
  sql: match ($type) {
    "post" => "SELECT * FROM `ObservationEntry` WHERE `attention_path_id` = :attention_path_id AND `post_id` = :post;",
    "tree" => "SELECT * FROM `ObservationEntry` WHERE `attention_path_id` = :attention_path_id AND `tree_id` = :tree;",
    default => throw new Exception("Unknown type: $type")
  },
  params: match ($type) {
    "post" => [
      "attention_path_id" => $attention_path_id,
      "post" => $id
    ],
    "tree" => [
      "attention_path_id" => $attention_path_id,
      "tree" => $id
    ]
  }
);

if ($possible_observe_entry === null) {
  $new_observe_entry = new \cls\data\post\ObservationEntry();
  $new_observe_entry->attention_path_id = $attention_path_id;
  if ($type === "post") {
    $new_observe_entry->post_id = $id;
  }
  else {
    $new_observe_entry->tree_id = $id;
  }
  $new_observe_entry->create_date = date("Y-m-d H:i:s");
  $new_observe_entry->save(App::get_connection());

  ?>
  <button class="unobserve-button" onclick="observe(id, '<?= $type ?>', this)"> unobserve ❌ </button>
  <?php

  exit();
}
else {
  // delete
  $possible_observe_entry->delete(App::get_connection());
  ?>
  <button class="observe-button" onclick="observe(id, '<?= $type ?>', this)"> Observe 🔭 </button>
  <?php

  exit();
}