<?php

namespace cls;

class Protocol {
  static function request(bool $is_called_directly, callable $function, App $app): callable {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    if ($is_called_directly) {
      $result = $function(
        app: $app,
        post_data: $_POST,
      );
      if ($result instanceof RequestError) {
        echo $result->return_json_protocol($app);
      }
      else {
        # todo add logs on debug mode
        echo json_encode([
          "status" => "ok",
          "data" => $result
        ]);
      }
      exit();
    }
    else {
      return $function;
    }
  }
}