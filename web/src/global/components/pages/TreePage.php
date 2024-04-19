<?php

namespace src\global\components\pages;

use src\core\Component;
use src\global\components\MainNavigationBar;

readonly class TreePage extends Component {

  public function render(): void {

    function node($indent) {
      $rand = rand(0, 100000);
      ?>
      <div
        style="margin-left: <?= $indent * 6 ?>px !important;"
        class="w3-card w3-margin w3-padding">
        <div>
          <small> 🟠 Header: Author, attention cap, is opened for subscribers, create date, number of answers, etc.</small>
          <div class="w3-right">
            <button><i class="far fa-dot-circle"></i></button>
          </div>
        </div>
        <h3
          onclick="
            if ($('#content_<?= $rand ?>').is(':visible')) {
              $('#down_<?= $rand ?>').show()
              $('#up_<?= $rand ?>').hide()
            } else {
              $('#down_<?= $rand ?>').hide()
              $('#up_<?= $rand ?>').show()
            }
            $('#content_<?= $rand ?>').toggle('fast')
            "
        >Tree node
          <i id="down_<?=$rand?>" class="fas fa-chevron-circle-down"></i>
          <i style="display:none" id="up_<?=$rand?>" class="fas fa-chevron-circle-up"></i>
        </h3>
        <p> Abstract: This post does describe whatever .... </p>
        <div id="content_<?= $rand ?>" style="display:none">
        <pre>
          Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy
          eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam
          voluptua. At vero eos et accusam et justo duo dolores et ea rebum.
          Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum
          dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing
          elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore
          magna aliquyam erat, sed diam voluptua. At vero eos et accusam et
          justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea
          takimata sanctus est Lorem ipsum dolor sit amet.
        </pre>
          <pre>
          Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy
          eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam
          voluptua. At vero eos et accusam et justo duo dolores et ea rebum.
          Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum
          dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing
          elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore
          magna aliquyam erat, sed diam voluptua. At vero eos et accusam et
          justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea
          takimata sanctus est Lorem ipsum dolor sit amet.
        </pre>
          <pre>
          Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy
          eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam
          voluptua. At vero eos et accusam et justo duo dolores et ea rebum.
          Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum
          dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing
          elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore
          magna aliquyam erat, sed diam voluptua. At vero eos et accusam et
          justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea
          takimata sanctus est Lorem ipsum dolor sit amet.
        </pre>
          <hr>
          <!-- Actions on the content -->
          <button> Give support on content </button>
          <button> Add content to attention profile </button>
          <button> Create new Conversation from this content </button>
          <button>  </button>
        </div>
      </div>
      <!-- Answers to this post -->
      <div style="margin-left: <?= $indent * 6 ?>px !important;">
        <button>Prediction-Markets: 3</button>
        <button>Comments 54 </button>
        <button>🟠 Polls: 2</button>
        <button>Attachments: 7</button>
        <button>Links: 3</button>
        <button> + </button>
      </div>
      <?php
    }

    $main_nav_bar = new MainNavigationBar(
      middle: (new readonly class extends Component {
        public function render(): void {
          ?>
          One Tree
          <?php
        }
      }),
      right: new readonly class extends Component {
        public function render(): void {
          # space settings -->
          ?>
          <!-- Members of this tree-conversation -->
          <a href="?p=tree&members"> <i class="fas fa-users"></i> Members </a> &nbsp;&nbsp;|&nbsp;&nbsp;
          <a href="?p=tree&settings&id=123"> <i class="fas fa-tools"></i> Settings </a>
          <?php
        }
      }
    );
    $main_nav_bar->render();
    ?>

    <div class="w3-row">

      <div class="w3-col l2 m2 s2">
        <div class="w3-card w3-padding w3-margin">
          <h3>SIDEBAR</h3>
        </div>
      </div>

      <div class="w3-rest">

        <div style="margin-top: 6px">
          <a href=""> Top-Level-Post</a> <i class="fas fa-angle-double-right"></i>
          <a href="">Sub level post</a> <i class="fas fa-angle-double-right"></i>
          <a href="">direct parent</a> <i class="fas fa-angle-double-right"></i>
        </div>

        <?php
        node(0);
        node(20);
        node(40);
        node(60);
        node(80);
        node(40);
        node(60);
        node(80);
        node(40);
        node(60);
        node(80);
        ?>
      </div>

    </div>
    <?php

  }

}