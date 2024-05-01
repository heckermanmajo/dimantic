<?php

namespace src\app\content_tree\components;

use src\core\Component;

readonly class DefaultTreeOverview extends Component {

  public function render(): void {
    $rand = rand(0, 100000);
    ?>
      <div class="w3-card w3-padding">

        <div>
          <small> 🟠 Header: Author, attention cap, etc.</small>

          <div class="w3-right"><a href="/?p=tree&id=123"> <small><i class="fas fa-link"></i> Go to tree  </small></a></div>
        </div>

        <h3
          style="cursor: pointer"
          onclick="$('#details_<?=$rand?>').toggle('fast')"
        > Tree title: Hey leute was haltet ihr von X?? Ich finde das toll. </h3>

        <div id="details_<?=$rand?>" style="display: none">
          <div> More infos </div>

          <a href="/?p=tree&id=123"> <i class="fas fa-link"></i> Go to tree  <i class="fas fa-arrow-circle-right"></i></a>
          <hr>
          <button> Fav </button>
          <button> Melden </button>
          <button> whatever </button>
        </div>

      </div>
    <?php
  }

}