<?php

namespace src\core;

abstract class Component {

  abstract public function render(): void;

  final public function get_as_string(): string {
    ob_start();
    $this->render();
    return ob_get_clean();
  }

}