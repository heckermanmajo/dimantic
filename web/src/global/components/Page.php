<?php

namespace src\global\components;

use src\core\Component;

/**
 * Assembles the HTML page.
 */
class Page extends Component {

  function __construct(
    private readonly array $head_components,
    private readonly Component $body,
    private readonly string $title = "Dimantic",
    private readonly string $lang = "en"
  ) {
  }

  public function render(): void {

    ?>
    <!DOCTYPE html>
    <html lang="<?=$this->lang?>">
    <head>
      <title><?=$this->title?></title>
      <?php
      foreach ($this->head_components as $child) {
        $child->render();
      }
      ?>
    </head>
    <body>
    <?php (new OverlayTemplateAndJsCode())->render(); ?>
    <?php $this->body->render(); ?>
    </body>
    </html>
    <?php
  }

}