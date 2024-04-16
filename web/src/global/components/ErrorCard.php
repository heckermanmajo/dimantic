<?php

namespace src\global\components;

use src\core\Component;

readonly class ErrorCard extends Component {
  function __construct(
    private string $error_message,
    private string $context_name,
    private array $additional_data,
    private array $debug_logs,
    private array $additional_debug_data,
  ) {
  }

  function render(): void {
    echo "<div class='card'>";
    echo "<div class='card-header'>";
    echo "<h5 class='card-title'>$this->context_name</h5>";
    echo "</div>";
    echo "<div class='card-body'>";
    echo "<p class='card-text'>$this->error_message</p>";
    echo "<pre>";
    print_r($this->additional_data);
    echo "</pre>";
    echo "<pre>";
    print_r($this->debug_logs);
    echo "</pre>";
    echo "<pre>";
    print_r($this->additional_debug_data);
    echo "</pre>";
    echo "</div>";
    echo "</div>";
  }
}