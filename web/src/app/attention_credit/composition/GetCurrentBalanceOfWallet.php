<?php

namespace src\app\attention_credit\composition;

use src\core\Composition;

/**
 * This composition is used to get the current balance of a wallet.
 * We cannot just request the balance from the wallet table,
 * because the balance is the sum of all transactions, which
 * might not yet be added to the wallet table.
 * -> this is done once a day by a cron job
 */
class GetCurrentBalanceOfWallet extends Composition {
  static function get_balance(int $wallet_id): int {
    # todo: implement get_balance_of_wallet composition
  }
}