<?php

namespace src\app\mailer\actions;

use src\core\Action;

final class SendAdminMail extends Action {

  function __construct(
    private string $head,
    private string $body,
  ) {

  }

  function is_allowed(): bool {
    // TODO: Implement is_allowed() method.
    return true;
  }

  function execute(): void {
    // TODO: Implement execute() method.
  }

}