<?php

namespace src\global\components;

use src\core\Component;
use src\global\compositions\GetEnvironmentMode;

# todo: improve json response
# todo: https://datatracker.ietf.org/doc/html/rfc7807

readonly class JsonError extends Component{

  function __construct(
    private string          $context_name,
    private string $error_message,
    private array           $additional_data,
    private array           $debug_logs,
    private array           $additional_debug_data,
  ) {
  }

  public function render(): void {

    $is_debug = GetEnvironmentMode::is_debug();

    echo json_encode(
      [
        "error" => true,
        "msg" => $this->error_message,
        "context" => $this->context_name,
        "data" => $this->additional_data,
        "debug_info" => $is_debug ? $this->debug_logs : [],
        "debug_data" => $this->additional_debug_data,
      ],
      JSON_PRETTY_PRINT
    );

  }

}