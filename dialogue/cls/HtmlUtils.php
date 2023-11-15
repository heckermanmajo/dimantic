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
      <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>-->
      <!-- jstreehttps://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/jstree.min.js -->
      <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/jstree.min.js"></script>-->
      <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/themes/default/style.min.css"/>-->
      <!--<script src="https://kit.fontawesome.com/ac3fc65406.js" crossorigin="anonymous"></script>-->
      <link rel="stylesheet" href="/res/w3.css">
      <link rel="stylesheet" href="/res/main.css">

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
              background-color: #1d1e20;
              color: whitesmoke;
              overflow: hidden;

            <?php if(!FN_IS_MOBILE()): ?>
              margin-left: 20%;
              margin-right: 20%;
            <?php endif; ?>
          }


          <?=$style?>
      </style>

      <title>Dimantic</title>

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
}