<?php

namespace src\app\space\components;

use src\core\Component;

readonly class SuperAndSubSpaceSelect extends Component {

  public function render(): void {
    ?>
    <select
      onchange="">
      <option value=""> 🔲 Current Space </option>
      <option value=""> ⬛️ Super-Space 1 or parent space with long name lol dies das  </option>
      <option value=""> ⬛️ Super-Space 2 (not on sub space)</option>
      <option value=""> ⬛️ Super-Space 3 (not on sub space)</option>
      <option value="">▪️ Sub-Space 1 </option>
      <option value="">▪️ Sub-Space 2 </option>
      <option value="">▪️ Sub-Space 3 </option>
      <option value="">▪️ Sub-Space 4 </option>
      <option value="">▪️ Sub-Space 5 </option>
    </select>
    <?php
  }

}