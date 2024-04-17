<?php

namespace src\global\components;

use src\core\Component;

readonly class View extends Component{
  function __construct(private string $html) { }

  public function render(): void {
    echo $this->html;
  }

}