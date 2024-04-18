<?php

namespace src\global\components;


use src\core\Component;

readonly class HomeSideBar extends Component {

  public function render(): void {

    # home sidebar: chats? ongoing dialogues?, my spaces

    ?>
    <div
      class="w3-card w3-margin w3-padding"
    >

      <script>
          // create a css class via jquery
          // display: inline-block; margin-bottom: 7px; border-bottom: 1px solid #cfcfcf
          $(document).ready(function () {
              $(".sidebar-link")
                  .css("display", "inline-block")
                  .css("margin-bottom", "7px")
                  .css("border-bottom", "1px solid #cfcfcf")
          });
      </script>
      <?php

      # we can use the 3 cubes for a super space...
      $cubes = '<i  style="color:#639bff" class="fas fa-project-diagram"></i>';
      $cube = '<i style="color:blue" class="fas fa-cube"></i>';
      $super_space = '<i style="color:#7d00ff" class="fas fa-cubes"></i>';
      $contract = '<i style="color:#ff00a6" class="fas fa-file-contract"></i>';
      ?>

      <p> Most Urgent Stuff 🟣 ​🟪​  🔴 🟥  </p>

      <hr>

      <!--<a class="sidebar-link" href="?p=space"> <?= $contract ?> Ein Contract den ich erfüllen muss </a><br>
      <a class="sidebar-link" href="?p=space"> <?= $contract ?> Ein Contract den ich erfüllen muss </a><br>
      <a class="sidebar-link" href="?p=space"> <?= $contract ?> Ein Contract den ich erfüllen muss </a><br>
      <a class="sidebar-link" href="?p=space"> <?= $contract ?> Ein Contract den ich erfüllen muss </a><br>-->

      <hr>

     <!-- <a class="sidebar-link" href="?p=space"> <?= $cube ?> Ein space der mir gehört </a><br>
      <a class="sidebar-link" href="?p=space"> <?= $cube ?> Ein space der mir gehört </a><br>
      <a class="sidebar-link" href="?p=space"> <?= $cube ?> Ein space der mir gehört </a><br>
      <a class="sidebar-link" href="?p=space"> <?= $cube ?> Ein space der mir gehört </a><br>-->


    </div>
    <?php
  }
}