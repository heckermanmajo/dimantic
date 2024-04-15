<?php

use src\app\user\data\tables\account\Account;
use src\app\user\enums\AccountState;
use src\core\App;
use src\core\Component;
use src\global\components\HtmlHead;
use src\global\components\InlineJavascript;
use src\global\components\MainNavigationBar;
use src\global\components\Page;
use src\global\compositions\GetCurrentlyLoggedInAccount;
use src\global\requests\pages\LandingPageOffline;

include $_SERVER["DOCUMENT_ROOT"] . "/src/core/App.php";


/*$db = App::get_database();
Account::create_table();
$a = new Account();
$a->state = AccountState::PLATFORM_ADMIN;
$a->save();
$aa = Account::get_by_id(1);
echo $aa->state->name;

echo "<hr>";*/


#echo in_array(\src\core\DBSaveEnum::class, class_implements(\src\app\user\enums\AccountState::class)) ? "true" : "false";
class EmptyPage extends Component {
  public function render(): void {
    $main_nav = new MainNavigationBar();
    ?>

    <?php $main_nav->render() ?>

    <h1>Empty Page</h1>

    <button onclick="showOverlay('index.php')"> SHOW INDEX IN OVERLAY</button>
    <?php
  }
}


(new Page(
  head_components: [
    new HtmlHead(),
    new InlineJavascript()
  ],
  body: GetCurrentlyLoggedInAccount::somebody_is_logged_in() ? (new EmptyPage()) : (new LandingPageOffline()),
  title: "Empty Page",
  lang: "en"
))->render();


?>

dimantic.com?user=1&settings
dimantic.com?postcontainer=2