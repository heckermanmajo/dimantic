<?php

use src\core\Component;
use src\core\Request;
use src\global\action\SaveRequestDataForNextRequestAction;
use src\global\components\ErrorCard;
use src\global\components\JsonError;
use src\global\compositions\GetCurrentlyLoggedInAccount;

# todo: if you want json feedback, you need to add a send me json field ...

include $_SERVER["DOCUMENT_ROOT"] . "/src/core/App.php";

try {

  $referring_page = $_SERVER["HTTP_REFERER"] ?? "";

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

      $request->is_done = true;

      (new SaveRequestDataForNextRequestAction(
        last_request: $request
      ))->execute();

    }else{

      $err = new ErrorCard(
        error_message: $request->why_invalid->getMessage(),
        context_name: "Request invalid",
        additional_data: [],
        debug_logs: [],
        additional_debug_data: [
          "why_invalid" => $request->why_invalid->getTraceAsString(),
        ],
      );

      $save_action = new SaveRequestDataForNextRequestAction(
        error_card: $err,
        last_request: $request
      );

      $save_action->execute();
      # todo: maybe also put the output into then session for debugging
      ob_get_clean();
      header("Location: $referring_page");
      die();



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
    $err = new ErrorCard(
      error_message: $request->why_not_allowed,
      context_name: "Request is not allowed for your account",
      additional_data: [],
      debug_logs: [],
      additional_debug_data: [

      ],
    );

    $save_action = new SaveRequestDataForNextRequestAction(
      error_card: $err,
      last_request: $request
    );

    $save_action->execute();
    # todo: maybe also put the output into then session for debugging
    ob_get_clean();
    header("Location: $referring_page");
    die();
    (new JsonError(
      context_name: "Request not allowed",
      error_message: $request->why_not_allowed,
      additional_data: [],
      debug_logs: [],
      additional_debug_data: [],
    ))->render();

  }


} catch (Throwable $t) {
  $err = new ErrorCard(
    error_message: $t->getMessage(),
    context_name: "Request invalid",
    additional_data: [],
    debug_logs: [],
    additional_debug_data: [
      "why_invalid" => $t->getTraceAsString(),
    ],
  );

  $save_action = new SaveRequestDataForNextRequestAction(
    error_card: $err,
    last_request: $request
  );

  $save_action->execute();
  # todo: maybe also put the output into then session for debugging
  ob_get_clean();
  header("Location: $referring_page");
  die();
  (new JsonError(
    context_name: "Internal server error",
    error_message: $t->getMessage(),
    additional_data: [],
    debug_logs: [],
    additional_debug_data: [],
  ))->render();

}
