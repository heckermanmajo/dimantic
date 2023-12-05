<?php
declare(strict_types=1);

namespace cls;

class HtmlUtils {
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

  static function main_header() {
    ?>
    <nav class="w3-margin">
      <a class="button" href="/home.php">Home</a>
      <a class="button" href="/my_news.php">News</a>
      <a class="button" href="/members.php">Members</a>
      <a class="button" href="/account_settings.php">Account-Settings</a>

      <!-- TODO: logout does not work -->
      <div class="w3-right">
        <form style="display: inline" method="post">
          <input type="hidden" name="action" value="logout">
          <button class="button" style="border-color: mediumvioletred; color: mediumvioletred">Logout</button>
        </form>
        <a class="button" style="border-color: #8bc34a; color: #8bc34a" href="/project.php"> Project-Info </a>
      </div>
    </nav>
    <?php
  }

  static function get_important_emojis(): array {
    return [
      "😀", "😂", "🤣", "🥲", "😇", "🙂", "🥰", "😘", "🤪", "🤨", "🧐", "🤓", "🤠", "👿", "🤡", "💩",
      "💀", "☠️", "👽", "🤖", "🤐", "🤬", "🤯", "❤️", "🆗", "🆒", "⚠️", "☢️",
      "ᕕ( ᐛ )ᕗ", "(ノಠ益ಠ)ノ彡┻━┻"
    ];
  }

  static function get_emojis() {
    return [
      "😀", "😃", "😄", "😁", "😆", "😅", "😂", "🤣", "🥲", "🥹", "☺️", "😊", "😇", "🙂", "🙃", "😉", "😌", "😍", "🥰", "😘", "😗",
      "😙", "😚", "😋", "😛", "😝", "😜", "🤪", "🤨", "🧐", "🤓", "😎", "🥸", "🤩", "🥳", "😏", "😒", "😞", "😔", "😟", "😕", "🙁",
      "☹️", "😣", "😖", "😫", "😩", "🥺", "😢", "😭", "😮‍💨", "😤", "😠", "😡", "🤬", "🤯", "😳", "🥵", "🥶", "😱", "😨", "😰", "😥",
      "😓", "🫣", "🤗", "🫡", "🤔", "🫢", "🤭", "🤫", "🤥", "😶", "😶‍🌫️", "😐", "😑", "😬", "🫨", "🫠", "🙄", "😯", "😦", "😧", "😮",
      "😲", "🥱", "😴", "🤤", "😪", "😵", "😵‍💫", "🫥", "🤐", "🥴", "🤢", "🤮", "🤧", "😷", "🤒", "🤕", "🤑", "🤠", "😈", "👿", "👹",
      "👺", "🤡", "💩", "👻", "💀", "☠️", "👽", "👾", "🤖", "🎃", "😺", "😸", "😹", "😻", "😼", "😽", "🙀", "😿", "😾", "👋", "🤚",
      "🖐", "✋", "🖖", "👌", "🤌", "🤏", "✌️", "🤞", "🫰", "🤟", "🤘", "🤙", "🫵", "🫱", "🫲", "🫸", "🫷", "🫳", "🫴", "👈", "👉",
      "👆", "🖕", "👇", "☝️", "👍", "👎", "✊", "👊", "🤛", "🤜", "👏", "🫶", "🙌", "👐", "🤲", "🤝", "🙏", "✍️", "💅", "🤳", "💪",
      "🦾", "🦵", "🦿", "🦶", "👣", "👂", "🦻", "👃", "🫀", "🫁", "🧠", "🦷", "🦴", "👀", "👁", "👅", "👄", "🫦", "💋", "🩸🗣",
      "👤", "👥", "🫂", "⌚️", "📱", "📲", "💻", "⌨️", "🖥", "🖨", "🖱", "🖲", "🕹", "🗜", "💽", "💾", "💿", "📀", "📼", "📷", "📸",
      "📹", "🎥", "📽", "🎞", "📞", "☎️", "📟", "📠", "📺", "📻", "🎙", "🎚", "🎛", "🧭", "⏱", "⏲", "⏰", "🕰", "⌛️", "⏳", "📡",
      "🔋", "🪫", "🔌", "💡", "🔦", "🕯", "🪔", "🧯", "🛢", "🛍️", "💸", "💵", "💴", "💶", "💷", "🪙", "💰", "💳", "💎", "⚖️", "🪮",
      "🪜", "🧰", "🪛", "🔧", "🔨", "⚒", "🛠", "⛏", "🪚", "🔩", "⚙️", "🪤", "🧱", "⛓", "🧲", "🔫", "💣", "🧨", "🪓", "🔪", "🗡",
      "⚔️", "🛡", "🚬", "⚰️", "🪦", "⚱️", "🏺", "🔮", "📿", "🧿", "🪬", "💈", "⚗️", "🔭", "🔬", "🕳", "🩹", "🩺", "🩻", "🩼", "💊",
      "💉", "🩸", "🧬", "🦠", "🧫", "🧪", "🌡", "🧹", "🪠", "🧺", "🧻", "🚽", "🚰", "🚿", "🛁", "🛀", "🧼", "🪥", "🪒", "🧽", "🪣",
      "🧴", "🛎", "🔑", "🗝", "🚪", "🪑", "🛋", "🛏", "🛌", "🧸", "🪆", "🖼", "🪞", "🪟", "🛍", "🛒", "🎁", "🎈", "🎏", "🎀", "🪄",
      "🪅", "🎊", "🎉", "🪩", "🎎", "🏮", "🎐", "🧧", "✉️", "📩", "📨", "📧", "💌", "📥", "📤", "📦", "🏷", "🪧", "📪", "📫", "📬",
      "📭", "📮", "📯", "📜", "📃", "📄", "📑", "🧾", "📊", "📈", "📉", "🗒", "🗓", "📆", "📅", "🗑", "🪪", "📇", "🗃", "🗳", "🗄",
      "📋", "📁", "📂", "🗂", "🗞", "📰", "📓", "📔", "📒", "📕", "📗", "📘", "📙", "📚", "📖", "🔖", "🧷", "🔗", "📎", "🖇", "📐",
      "📏", "🧮", "📌", "📍", "✂️", "🖊", "🖋", "✒️", "🖌", "🖍", "📝", "✏️", "🔍", "🔎", "🔏", "🔐", "🔒", "🔓", "❤️", "🩷", "🧡",
      "💛", "💚", "💙", "🩵", "💜", "🖤", "🩶", "🤍", "🤎", "❤️‍🔥", "❤️‍🩹", "💔", "❣️", "💕", "💞", "💓", "💗", "💖", "💘", "💝", "💟",
      "☮️", "✝️", "☪️", "🪯", "🕉", "☸️", "✡️", "🔯", "🕎", "☯️", "☦️", "🛐", "⛎", "♈️", "♉️", "♊️", "♋️", "♌️", "♍️", "♎️", "♏️",
      "♐️", "♑️", "♒️", "♓️", "🆔", "⚛️", "🉑", "☢️", "☣️", "📴", "📳", "🈶", "🈚️", "🈸", "🈺", "🈷️", "✴️", "🆚", "💮", "🉐", "㊙️", "㊗️",
      "🈴", "🈵", "🈹", "🈲", "🅰️", "🅱️", "🆎", "🆑", "🅾️", "🆘", "❌", "⭕️", "🛑", "⛔️", "📛", "🚫", "💯", "💢", "♨️", "🚷", "🚯", "🚳",
      "🚱", "🔞", "📵", "🚭", "❗️", "❕", "❓", "❔", "‼️", "⁉️", "🔅", "🔆", "〽️", "⚠️", "🚸", "🔱", "⚜️", "🔰", "♻️", "✅", "🈯️", "💹",
      "❇️", "✳️", "❎", "🌐", "💠", "Ⓜ️", "🌀", "💤", "🏧", "🚾", "♿️", "🅿️", "🛗", "🈳", "🈂️", "🛂", "🛃", "🛄", "🛅", "🚹", "🚺", "🚼",
      "⚧", "🚻", "🚮", "🎦", "🛜", "📶", "🈁", "🔣", "ℹ️", "🔤", "🔡", "🔠", "🆖", "🆗", "🆙", "🆒", "🆕", "🆓", "0️⃣", "1️⃣", "2️⃣", "3️⃣",
      "4️⃣", "5️⃣", "6️⃣", "7️⃣", "8️⃣", "9️⃣", "🔟", "🔢", "#️⃣", "*️⃣", "⏏️", "▶️", "⏸", "⏯", "⏹", "⏺", "⏭", "⏮", "⏩", "⏪", "⏫",
      "⏬", "◀️", "🔼", "🔽", "➡️", "⬅️", "⬆️", "⬇️", "↗️", "↘️", "↙️", "↖️", "↕️", "↔️", "↪️", "↩️", "⤴️", "⤵️", "🔀", "🔁", "🔂",
      "🔄", "🔃", "🎵", "🎶", "➕", "➖", "➗", "✖️", "🟰", "♾", "💲", "💱", "™️", "©️", "®️", "〰️", "➰", "➿", "🔚", "🔙", "🔛", "🔝",
      "🔜", "✔️", "☑️", "🔘", "🔴", "🟠", "🟡", "🟢", "🔵", "🟣", "⚫️", "⚪️", "🟤", "🔺", "🔻", "🔸", "🔹", "🔶", "🔷", "🔳", "🔲", "▪️"
      , "▫️", "◾️", "◽️", "◼️", "◻️", "🟥", "🟧", "🟨", "🟩", "🟦", "🟪", "⬛️", "⬜️", "🟫", "🔈", "🔇", "🔉", "🔊", "🔔", "🔕", "📣",
      "📢", "👁‍🗨", "💬", "💭", "🗯", "♠️", "♣️", "♥️", "♦️", "🃏", "🎴🚗", "🚕", "🚙", "🚌", "🚎", "🏎", "🚓", "🚑", "🚒", "🚐", "🛻",
      "🚚", "🚛", "🚜", "🦯", "🦽", "🦼", "🛴", "🚲", "🛵", "🏍", "🛺", "🚨", "🚔", "🚍", "🚘", "🚖", "🛞", "🚡", "🚠", "🚟", "🚃",
      "🚋", "🚞", "🚝", "🚄", "🚅", "🚈", "🚂", "🚆", "🚇", "🚊", "🚉", "✈️", "🛫", "🛬", "🛩", "💺", "🛰", "🚀", "🛸", "🚁", "🛶",
      "⛵️", "🚤", "🛥", "🛳", "⛴", "🚢", "⚓️", "🛟", "🪝", "⛽️", "🚧", "🚦", "🚥", "🚏", "🗺", "🗿", "🗽", "🗼", "🏰", "🏯", "🏟",
      "🎡", "🎢", "🛝", "🎠", "⛲️", "⛱", "🏖", "🏝", "🏜", "🌋", "⛰", "🏔", "🗻", "🏕", "⛺️", "🛖", "🏠", "🏡", "🏘", "🏚", "🏗",
      "🏭", "🏢", "🏬", "🏣", "🏤", "🏥", "🏦", "🏨", "🏪", "🏫", "🏩", "💒", "🏛", "⛪️", "🕌", "🕍", "🛕", "🕋", "⛩", "🛤", "🛣",
      "🗾", "🎑", "🏞", "🌅", "🌄", "🌠", "🎇", "🎆", "🌇", "🌆", "🏙", "🌃", "🌌", "🌉", "🌁",
      "(ノಠ益ಠ)ノ彡┻━┻"
    ];
  }

  static function get_markdown_editor_field_for_ajax(
    string $field_name,
    string $ajax_end_point_path_from_root,
    string $init_text,
    array  $extra_json_fields
  ) {

    ob_start();

    $debug_feedback_space_css_id = "debug_feedback_space_" . rand(0, 1000000);
    $random_css_id = "id_" . rand(0, 1000000);
    $editor_name = "editor_" . rand(0, 1000000);
    ?>
    <textarea
      name="<?= $field_name ?>"
      id="<?= $random_css_id ?>"
      onchange="console.log(this.value)"
    ><?= $init_text ?></textarea>
    <script>
      let <?=$editor_name?> = null;
      $(document).ready(function () {
        <?=$editor_name?> = new EasyMDE({
          element: document.getElementById('<?=$random_css_id?>'),
          // german spellchecker
          spellChecker: false,
          // log the content of the textarea to the console when changed
          onchange: function () {
            console.log(<?=$editor_name?>.value());
            alert("lol")
          },
        });


        <?=$editor_name?>.codemirror.on("change", () => {
          // todo: make it, so that the textarea sends ajax request for update and waits for response
          // if a new input since then wait til response is back and update again.
          // also wait a few hundered miliseconds until  the next Response.
          console.log(<?=$editor_name?>.value());

          if("<?=$ajax_end_point_path_from_root?>" == ""){
            return;
          }

          // request/update_message_draft/update_message_draft.php

          let form_data = new FormData();
          let data = {
            "<?= $field_name ?>": <?=$editor_name?>.value(),
            <?php foreach ($extra_json_fields as $key => $value) { ?>
            "<?= $key ?>": "<?= $value ?>",
            <?php } ?>
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
                //alert("error");
                document.getElementById("<?=$debug_feedback_space_css_id?>").innerHTML = FN_RETURN_ERROR_CARD(data)
                // document.getElementById("<?=$debug_feedback_space_css_id?>").innerHTML = data.logs;
                console.log(data);
                //alert(data.error_message);
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
    <!--<button
      onclick="
        const text = easymde.value();
        alert(text);
        e.preventDefault();
        return false;
      "
    >
      alert content
    </button>-->

    <!--
      button that adds a smiley to the textarea
      TODO: add all emojis to this button -> with a nice emoji selection of 20 best onces
     -->
    <?php
    foreach (HtmlUtils::get_important_emojis() as $emoji) {
      ?>
      <button
        style="background-color: inherit; border: solid black 1px; margin: 3px; padding-left: 5px;padding-right: 5px; cursor: pointer; font-size: 130%"
        onclick="
          const text = <?= $editor_name ?>.value();
          let cursorPosition = <?= $editor_name ?>.codemirror.getCursor();
        <?= $editor_name ?>.codemirror.replaceRange('<?= $emoji ?>', cursorPosition);
          // set cursor one character after the pasted emoji
        <?= $editor_name ?>.codemirror.setCursor(cursorPosition.line, cursorPosition.ch + <?= strlen($emoji) + 1 ?>);
          event.preventDefault();
          event.stopPropagation();
          return false;
          "
      >
        <?= $emoji ?>
      </button>
      <?php
    }

    return ob_get_clean();

  }

}