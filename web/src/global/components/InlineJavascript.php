<?php

namespace src\global\components;

use src\core\Component;

class InlineJavascript extends Component {

  public function render(): void {
    ?>
    <script>
      console.log("Hello from InlineJavascript!");
    </script>
    <?php
  }

}