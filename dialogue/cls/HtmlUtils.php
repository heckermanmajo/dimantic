<?php
declare(strict_types=1);

namespace cls;

class HtmlUtils {

  const NO_AJAX_ENDPOINT = "";
  static function head(string $language = "en", string $style = ""): void {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    #lang="<?= !$language ? "en" : $language #"
    ?>
    <!DOCTYPE html>
    <head>
      <meta charset="utf-8">
      <!-- jquery -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <!-- jstreehttps://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/jstree.min.js -->
      <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/jstree.min.js"></script>-->
      <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/themes/default/style.min.css"/>-->
      <!--<script src="https://kit.fontawesome.com/ac3fc65406.js" crossorigin="anonymous"></script>-->
      <link rel="stylesheet" href="/res/w3.css">
      <!--<link rel="stylesheet" href="/res/main.css">-->
      <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css"
      />
      <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
      <?php
      # import all js files from /js/
      # one file per function
      $js_folder = $_SERVER["DOCUMENT_ROOT"] . "/js/";
      foreach (scandir($js_folder) as $file) {
        if (is_file($js_folder . $file)) {
          ?>
          <script src="/js/<?= $file ?>"></script>
          <?php
        }
      }
      ?>
      <style>
          body {
              background-color: #ffffff;
              color: #1f1f1f;
              overflow: hidden;

          <?php if(!FN_IS_MOBILE()): ?> margin-left: 20%;
              margin-right: 20%;
          <?php endif; ?>
          }

          <?php include($_SERVER["DOCUMENT_ROOT"] . "/res/main.css.php");?>

          <?=$style?>
      </style>

      <title>Dimantic</title>
      <!-- Import Trumbowyg plugins... -->
      <!--<script src="trumbowyg/dist/plugins/upload/trumbowyg.cleanpaste.min.js"></script>
      <script src="trumbowyg/dist/plugins/upload/trumbowyg.pasteimage.min.js"></script>-->
      <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.27.3/ui/trumbowyg.min.css">-->
    </head>
    <?php
  }

  static function footer(App $app): void {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    ?>
    <br><br><br>    <br><br><br>    <br><br><br>    <br><br><br>    <br><br><br>
    <br><br><br>    <br><br><br>    <br><br><br>    <br><br><br>
    <footer>
      IMPRESSUM
    </footer>
    <?php
    App::dump_logs();
    $app->page_clean_up();
    ?>
    </body>
    </html>
    <?php
  }

  /**
   * The main header of the website - used on all main sides.
   * @return void
   */
  static function main_header() {
    ?>
    <nav class="w3-margin">
      <a class=" sketch-button " href="/feed.php">Feed</a>
      <a class=" sketch-button " href="/home.php">Home</a>
      <a class=" sketch-button " href="/search.php">Search</a>
      <a class=" sketch-button " href="/marked.php">Marked</a>
      <!--<a class="button" href="/my_news.php">News</a>-->
      <!--<a class="button" href="/members.php">Members</a>-->

      <div class="w3-right">
        <a class="sketch-button " href="/create_space.php"> ➕ Create Space </a>
        &nbsp;
        <a class="sketch-button " style="border-color: #8bc34a; color: #8bc34a" href="/project.php">
          <img width="12px" src="/res/info.svg">
        </a>
        <a class="sketch-button " href="/account_settings.php">⚙️</a>
        <form style="display: inline" method="post">
          <input type="hidden" name="action" value="logout">
          <button class="sketch-button " style="border-color: mediumvioletred; color: mediumvioletred">🔚</button>
        </form>

      </div>
    </nav>
    <?php
  }

  /**
   * @return string[] all important emojis as array
   * @see get_markdown_editor_field_for_ajax()
   */
  static function get_important_emojis(): array {
    return [
      "😀", "😂", "🤣", "🥲", "😇", "🙂", "🥰", "😘", "🤪", "🤨", "🧐", "🤓", "🤠", "👿", "🤡", "💩",
      "💀", "☠️", "👽", "🤖", "🤐", "🤬", "🤯", "❤️", "🆗", "🆒", "⚠️", "☢️",
      "ᕕ( ᐛ )ᕗ", "(ノಠ益ಠ)ノ彡┻━┻"
    ];
  }

  /**
   * This function returns a markdown editor field that sends ajax requests to the given endpoint
   * when the content of the editor changes.
   *
   * However, the ajax request can be disabled by setting the $ajax_end_point_path_from_root to an empty string.
   *
   * The input is not sent immediately, but only after the user stops typing for a few hundered miliseconds.
   * @param string $field_name the name of the fieldm this is important if the editor is
   *        inside a conventional post form
   * @param string $ajax_end_point_path_from_root the path to the endpoint that should
   *        be called when the content of the editor changes - can be empty string;
   *        relative path from root of the website
   * @param string $init_text the initial text of the editor, should be set to the
   *        current value of the field
   * @param array $extra_json_fields extra fields that should be sent with the ajax
   *        request, should be empty if $ajax_end_point_path_from_root is empty
   *
   * @return string the html of the editor
   *
   * @throws \Exception
   * @todo: this function is not finished yet
   *
   * It can also be used as a part of a conventional post form.
   *
   * EasyMDE is used as the markdown editor.
   * @see get_important_emojis()
   * @see https://github.com/Ionaru/easy-markdown-editor
   *
   */
  static function get_markdown_editor_field_for_ajax(
    string $field_name,
    string $ajax_end_point_path_from_root,
    string $init_text,
    array  $extra_json_fields
  ): false|string {

    ob_start();

    $debug_feedback_space_css_id = "debug_feedback_space_" . rand(0, 1000000);
    $random_css_id = "id_" . rand(0, 1000000);
    $editor_name = "editor_" . rand(0, 1000000);

    ?>
    <!-- This textarea is hidden and used as base for the markdown editor -->
    <textarea
      name="<?= $field_name ?>"
      id="<?= $random_css_id ?>"
      onchange="console.log(this.value)"
    ><?= $init_text ?></textarea>

    <script>

      let <?=$editor_name?> = null;

      // todo: this is the only jquery code so far
      // todo: maybe we can remove jquery and use vanilla js instead
      $(document).ready(function () {
        <?=$editor_name?> = new EasyMDE({
          element: document.getElementById('<?=$random_css_id?>'),
          spellChecker: false,
          // log the content of the textarea to the console when changed
          <?php if (FN_IS_LOCAL_HOST()): ?>
          onchange: function () {
            console.log(<?=$editor_name?>.value());
            alert("lol")
          },
          <?php endif; ?>
        });


        <?=$editor_name?>.codemirror.on("change", () => {
          // todo: make it, so that the textarea sends ajax request for update and waits for response
          // todo: if a new input since then wait til response is back and update again.
          // todo: also wait a few hundred miliseconds until  the next Response.
          console.log(<?=$editor_name?>.value());

          if ("<?=$ajax_end_point_path_from_root?>" == "") {
            return;
          }

          // request/update_message_draft/update_message_draft.php

          let form_data = new FormData();
          let data = {
            <?php
            #note: making this with the template engine looks like a mess
            echo "\"$field_name\": $editor_name.value(),";
            foreach ($extra_json_fields as $key => $value) {
              echo "\"$key\": \"$value\",";
            }
            ?>
          };

          for (let key in data) {
            form_data.append(key, data[key]);
          }


          fetch(
            '<?=$ajax_end_point_path_from_root?>',
            {
              method: 'POST',
              mode: 'no-cors',
              headers: {
                'Content-Type': 'application/json',
              },
              body: form_data,
            })
            .then(response => response.json())
            .then(data => {
              if (data.status) {
                console.log("successfull call to " + "<?=$ajax_end_point_path_from_root?>");
                console.log(data);
              } else {
                document.getElementById("<?=$debug_feedback_space_css_id?>")
                  .innerHTML = FN_RETURN_ERROR_CARD(data);
                console.log(data);
              }

            })
            .catch((error) => {
              console.error('Error:', error);
            });

          // send an ajax request to the endpoint
        });

      });
    </script>
    <div id="<?= $debug_feedback_space_css_id ?>">
    </div>
    <?php
    # The emoji buttons beneath the editor
    foreach (HtmlUtils::get_important_emojis() as $emoji) {
      ?>
      <button
        style="
          background-color: inherit;
          border: solid black 1px;
          margin: 3px;
          padding-left: 5px;
          padding-right: 5px;
          cursor: pointer;
          font-size: 130%
        "
        onclick="
          const text = <?= $editor_name ?>.value();
          let cursorPosition = <?= $editor_name ?>.codemirror.getCursor();
          // todo: error: is the emoji is inserted a lot of times, it jumps spaces
          //        and the cursor is not at the right position
        <?= $editor_name ?>.codemirror.replaceRange('<?= $emoji ?>', cursorPosition);
          // set cursor one character after the pasted emoji
        <?= $editor_name ?>.codemirror.setCursor(cursorPosition.line, cursorPosition.ch + <?= strlen($emoji) + 1 ?>);
          // important so we don't send a potential form, in which this editor is embedded
          event.preventDefault();
          event.stopPropagation();
          return false;
        "
      >
        <?= $emoji ?>
      </button>
      <?php
    }

    $ret = ob_get_clean();
    if ($ret === false) {
      throw new \Exception("ob_get_clean() failed");
    }
    return $ret;

  }

}