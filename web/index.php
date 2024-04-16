<?php

use src\app\user\data\tables\account\Account;
use src\global\components\HtmlHead;
use src\global\components\InlineJavascript;
use src\global\components\Page;
use src\global\components\pages\LandingPageOffline;
use src\global\components\pages\LandingPageOnline;
use src\global\compositions\GetCurrentlyLoggedInAccount;

include $_SERVER["DOCUMENT_ROOT"] . "/src/core/App.php";

Account::create_table();

$body = GetCurrentlyLoggedInAccount::somebody_is_logged_in()
  ? (new LandingPageOnline()) : (new LandingPageOffline());

(new Page(
  head_components: [
    new HtmlHead(),
    new InlineJavascript()
  ],
  body: $body,
  title: "Empty Page",
  lang: "en"
))->render();
