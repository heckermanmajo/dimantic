<?php

include $_SERVER['DOCUMENT_ROOT'] . '/cls/App.php';

$post_id = $_POST["id"] ?? throw new Exception("No post id given.");

$possible_post = \cls\data\post\Post::get_by_id(App::get_connection(), $post_id);

if ($possible_post == null) {
  throw new Exception("No post found.");
}

?>
<script>
  window.update_post_on_the_spot = function (id, content) {
    $.ajax({
      type: "POST",
      url: "/ajax/update_post_on_the_spot.php",
      data: {
        id: id,
        content: content
      },
      success: function (data) {
        console.log("success");
        $('#update_on_the_spot').val(data);
      },
      error: function (data) {
        console.log("error");
      }
    });
  }
</script>
<textarea style="width: 100%" rows="10" id="update_on_the_spot"><?= $possible_post->content ?></textarea>
<br>
<button class="button" onclick="update_post_on_the_spot(<?= $possible_post->id ?>, $('#update_on_the_spot').val())">Update</button>
