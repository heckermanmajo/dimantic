<?php

namespace src\app\tree\components;

use src\core\Component;

readonly class DefaultTreeShitboardOverview extends Component {

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


      </div>
    <?php
  }

}