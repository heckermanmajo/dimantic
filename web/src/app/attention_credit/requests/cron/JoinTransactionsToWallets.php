<?php

namespace src\app\attention_credit\requests\cron;

use src\core\Component;
use src\core\Request;

/**
 * This cron job joins transaction-entries to the wallet table
 * and deletes the transaction-entries afterward.
 */
class JoinTransactionsToWallets extends Request{

  function is_allowed(): bool {
    // TODO: Implement is_allowed() method.
  }

  function _is_valid(): void {
    // TODO: Implement _is_valid() method.
  }

  function execute(): Component|string|array|null {
    // TODO: Implement execute() method.
  }
}