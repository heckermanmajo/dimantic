<?php

use src\core\Component;
use src\global\components\HtmlHead;
use src\global\components\InlineJavascript;
use src\global\components\MainNavigationBar;
use src\global\components\Page;

include $_SERVER["DOCUMENT_ROOT"] . "/src/core/App.php";

class EmptyPage extends Component {
  public function render(): void {
    $main_nav = new MainNavigationBar();
    ?>

    <?php $main_nav->render() ?>

    <h1>Empty Page</h1>
    <?php
  }
}


(new Page(
  head_components: [
    new HtmlHead(),
    new InlineJavascript()
  ],
  body: new EmptyPage(),
  title: "Empty Page",
  lang: "en"
))->render();


?>

dimantic.com?user=1&settings
dimantic.com?postcontainer=2