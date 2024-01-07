<?php
declare(strict_types=1);

use cls\App;
use cls\HtmlUtils;

require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

try {
  
  App::init_context(basename_file: basename(path:__FILE__));
  $app = App::get();
  $app->init_database();
  [$log, $warn, $err, $todo]
    = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  if (!$app->somebody_logged_in()) {
    header("Location: /index.php");
  }

  $app->handle_action_requests();

  HtmlUtils::head();
  HtmlUtils::main_header();

  ?>
  <div class="info-card">
    <h3>Create Space</h3
    <p> Explain how a space works and bla ... </p>
  </div>

  <?php
  if (isset($_GET["parent_id"])){
    ## todo: load parent space and display it here and its rules for child rooms
  }

  #$all_spaces = Space::getAllSpaces($app);
  #foreach ($all_spaces as $space) {
  #  echo $space->getDisplayCard($app);
  #}

  ?>

  <form method="post" class="w3-card w3-margin w3-padding">
    <input type="hidden" name="action" value="create_new_space">
    <input type="hidden" name="parent_id" value="<?=$_GET['parent_id'] ?? 0?>">
    <?=
      HtmlUtils::get_markdown_editor_field_for_ajax(
        field_name: "content",
        ajax_end_point_path_from_root: "",
        init_text: "# SpaceName",
        extra_json_fields: []
      )
    ?>
    <div class="w3-margin">
      <button class="w3-button w3-green">Create Space</button>
    </div>
  </form>
<?php
  HtmlUtils::footer();
}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}