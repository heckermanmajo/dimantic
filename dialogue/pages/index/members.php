<?php

use cls\App;
use cls\data\account\Account;

return function (App $app){
  $all_members = Account::get_all_accounts(0, 50, $app);
  foreach ($all_members as $member) {
    echo $member->get_display_card($app);
  }
};