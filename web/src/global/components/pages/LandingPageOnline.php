<?php

namespace src\global\components\pages;

use src\core\Component;

readonly class LandingPageOnline extends Component {

  public function render(): void {

    # this component needs to be the navigation component

    switch($_GET["p"]){

      case "balance":
        $page = new BalancePage();
        break;

      case "space":
        $page = new SpacePage();
        break;

      case "ap":
        $page = new AttentionProfilePage();
        break;

      case "explore":
        $page = new ExplorePage();
        break;

      default:
        $page = new HomePage();
    }

    $page->render();

    # todo: footer??

  }
}