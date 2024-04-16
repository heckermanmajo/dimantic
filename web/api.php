<?php

use src\app\user\data\tables\account\Account;
use src\core\Component;
use src\core\Request;
use src\global\action\SaveRequestDataForNextRequestAction;
use src\global\components\JsonError;
use src\global\compositions\GetCurrentlyLoggedInAccount;


include $_SERVER["DOCUMENT_ROOT"] . "/src/core/App.php";

try {

  $class = Request::decrypt_class_name($_POST["__request_id"]);

  /** @var Request $request */
  $request = new $class(
    $_POST,
    GetCurrentlyLoggedInAccount::get_account(),
    is_proxy_request: false
  );

  if ($request->is_allowed()) {

    if($request->is_valid()) {

      $response = $request->execute();

      if ($response instanceof Component) {

        $response->render();

      } elseif (is_string($response)) {

        ob_get_clean();
        header("Location: $response");

      } elseif (is_array($response)) {

        echo json_encode($response, JSON_PRETTY_PRINT);

      } else {

        # it is null nothing to do...
        echo "null";

      }

      (new SaveRequestDataForNextRequestAction())->execute();

    }else{

        (new JsonError(
          context_name: "Request invalid",
          error_message: $request->why_invalid->getMessage(),
          additional_data: [],
          debug_logs: [],
          additional_debug_data: [
            "why_invalid" => $request->why_invalid->getTraceAsString(),
          ],
        ))->render();

    }

  } else {

    (new JsonError(
      context_name: "Request not allowed",
      error_message: $request->why_not_allowed,
      additional_data: [],
      debug_logs: [],
      additional_debug_data: [],
    ))->render();

  }


} catch (Throwable $t) {

  (new JsonError(
    context_name: "Internal server error",
    error_message: $t->getMessage(),
    additional_data: [],
    debug_logs: [],
    additional_debug_data: [],
  ))->render();

}
