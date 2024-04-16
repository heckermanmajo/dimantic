<?php

namespace src\global\components;

use src\core\Component;

readonly class InlineJavascript extends Component {

  public function render(): void {
    ?>
    <script>
      console.log("Hello from InlineJavascript!");
    </script>
    <?php
  }

}