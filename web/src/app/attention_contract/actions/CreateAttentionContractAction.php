<?php

namespace src\app\attention_contract\actions;

use src\core\Action;

class CreateAttentionContractAction extends Action {

  function __construct(
    private readonly string $contract_text,
  ) { }

  function is_allowed(): bool {
    // TODO: Implement is_allowed() method.
  }

  function execute(): void {
    // TODO: Implement execute() method.
  }

}