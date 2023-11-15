<?php
declare(strict_types=1);
namespace cls;

class RequestError {
  const USER_INPUT_ERROR = "USER_INPUT_ERROR";
  const SYSTEM_ERROR = "SYSTEM_ERROR";
  const BAD_REQUEST = "BAD_REQUEST";
  const NOT_FOUND = "NOT_FOUND";
  const RULE_ERROR = "RULE_ERROR";

  function __construct(
    public string      $dev_message,
    public string      $code,
    public string      $user_message = "",
    public array      $extra_data = [],
    public ?\Throwable $e = null
  ) {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  }
    // ...
  function return_json_protocol(): string {
    # in debug mode add logs
    return json_encode([
      "success" => false,
      "error" => [
        "code" => $this->code,
        "dev_message" => $this->dev_message,
        "user_message" => $this->user_message,
        "extra_data" => $this->extra_data,
      ],
      "logs" => FN_IS_DEBUG() ? App::get_logs() : [],
    ]);
  }

  function get_error_card(): string {
    ob_start();
    ?>
    <div class="w3-card-4 w3-margin w3-padding" style="border-color: orangered !important;">
      <h3 class="w3-text-red">Error</h3>
      <p class="w3-text-red"><?= $this->dev_message ?></p>
      <p class="w3-text-red"><?= $this->user_message ?></p>
      <p class="w3-text-red"><?= $this->code ?></p>
      <p class="w3-text-red"><?= $this->e ?></p>
    </div>
    <?php
    return ob_get_clean();
  }
}