<?php
declare(strict_types=1);

namespace cls;

class HtmlUtils {
  static function head(string $language = "en", string $style = ""): void {
    ?>
    <!DOCTYPE html>
    <head lang="<?= !$language ? "en" : $language ?>">

      <!-- jquery -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <!-- jstreehttps://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/jstree.min.js -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/jstree.min.js"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/themes/default/style.min.css"/>
      <script src="https://kit.fontawesome.com/ac3fc65406.js" crossorigin="anonymous"></script>
      <link rel="stylesheet" href="/res/w3.css">
      <link rel="stylesheet" href="/res/main.css">
      <style>
          body {
              background-color: #1d1e20;
              color: whitesmoke;
              overflow: hidden;
          }

          <?=$style?>
      </style>
      <title>Dimantic</title>

    </head>
    <?php
  }

  static function main_header(string $current_page = "") {

  }

  static function footer(App $app): void {
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
}