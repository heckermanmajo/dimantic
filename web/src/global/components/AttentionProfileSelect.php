<?php

namespace src\global\components;

use src\core\Component;

readonly class AttentionProfileSelect extends Component {

  public function render(): void {
    ?>
    <select
      onchange="window.location.href = `?p=${this.value}`">
      <option value="">Navigation</option>
      <option value="home"> 🏠 Home </option>
      <option value="explore"> 🔎🔭 Explore-Search </option>
      <option value="ap&id=1"> 👁Attention Profile 1 </option>
      <option value="ap&id=2"> 👁Attention Profile 2 </option>
      <option value="ap&id=3"> 👁Attention Profile 3 </option>

    </select>
    <?php
  }

}