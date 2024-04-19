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
      <option value="ap&id=1"> 👁Unsorted 1 (123 ✉️) 🟢</option>
      <option value="ap&id=1"> 👁Fam & Friends (123 ✉️) 🟢</option>
      <option value="ap&id=1"> 👁Dimantic (123 ✉️) 🟢</option>
      <option value="ap&id=2"> 👁Gamedev ( 3 ✉️) 🟢</option>
      <option value="ap&id=3"> 👁Sociology 3 </option>
      <option value="ap&id=3"> 👁Chill Content 3 </option>
    </select>
    <?php
  }

}