<?php

namespace cls;

use Exception;

/**
 * This class manages the direct access of requests by directly calling
 * request files as json endpoints.
 */
class Protocol {
  /**
   * Performs a request.
   *
   * @param bool $is_called_directly Specifies if the method is called directly or not.
   * @param callable $function The function to be executed.
   *
   * @return callable If the method is not called directly, it returns the passed function.
   *
   * @throws Exception If an error occurs during the execution of the function.
   */
  static function request(bool $is_called_directly, callable $function): callable {
    
    [$log, $warn, $err, $todo]
      = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    
    if ($is_called_directly) {
      
      $result = $function(
        post_data: $_POST,
      );
      
      if ($result instanceof RequestError) {
        echo $result->return_json_protocol();
      }
      else {
        # todo add logs on debug mode
        echo json_encode([
          "status" => "ok",
          "data" => $result,
          "logs" => FN_IS_DEBUG() ? App::get_logs() : [],
        ]);
      }
      
      exit();
    }
    
    else { # not called directly -> return the function for use within the php page logic
      
      return $function;
      
    }
  }
}