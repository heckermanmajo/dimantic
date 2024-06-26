<?php

namespace src\global\components;


use src\core\Component;

readonly class AttentionProfileSidebar extends Component {

  public function render(): void {

    # Render sub-spaces of the currently selected space -> if one is selected

    ?>
    <div
      class="w3-card w3-margin w3-padding"
    >
      <p> <i style="color: #009688" class="fas fa-eye"></i> Currently Selected AP </p>

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
      $private_space = '<i class="fas fa-key"></i><i style="color:blue" class="fas fa-cube"></i>';
      $my_space = '<i class="fas fa-crown"></i><i style="color:blue" class="fas fa-cube"></i>';
      ?>
      <hr>


      <a class="sidebar-link" href="?p=space"> <?= $my_space ?> Mein space </a><br>
      <a class="sidebar-link" href="?p=space"> <?= $my_space ?> Mein space </a><br>
      <a class="sidebar-link" href="?p=space"> <?= $my_space ?> Mein space </a><br>

      <hr>
      <!--

      The green dot is a signt that something relevant is new -> f.e. an interaction withou your content

       -->
      <a class="sidebar-link" href="?p=space"> <?= $private_space ?> Ein privater space </a><br>
      <a class="sidebar-link" href="?p=space"> <?= $private_space ?> Ein privater space 🟢 </a><br>
      <a class="sidebar-link" href="?p=space"> <?= $private_space ?> Ein privater space </a><br>
      <a class="sidebar-link" href="?p=space"> <?= $private_space ?> Ein privater space 🟢 </a><br>
      <a class="sidebar-link" href="?p=space"> <?= $private_space ?> Ein privater space </a><br>

      <hr>

      <a class="sidebar-link" href="?p=space">  <?= $super_space ?> Super Space </a><br>
      <a class="sidebar-link" href="?p=space">  <?= $super_space ?> Super Space </a><br>
      <a class="sidebar-link" href="?p=space">  <?= $super_space ?> Super Space </a><br>
      <a class="sidebar-link" href="?p=space">  <?= $super_space ?> Super Space </a><br>

      <hr>
      <!-- same here, but all non relavent interactiosn do NOT result in a green dot  -->
      <a class="sidebar-link" href="?p=space"> <?= $cube ?> Ein space dieses APs </a><br>
      <a class="sidebar-link" href="?p=space"> <?= $cube ?> Ein space dieses APs</a><br>
      <a class="sidebar-link" href="?p=space">  <?= $cube ?> Ein space dieses APs</a><br>
      <a class="sidebar-link" href="?p=space">  <?= $cube ?> Ein space dieses APs 🟢 </a><br>
      <a class="sidebar-link" href="?p=space">  <?= $cube ?> Ein space dieses APs</a><br>
      <a class="sidebar-link" href="?p=space">  <?= $cube ?> Ein space dieses APs </a>

      <hr>



    </div>
    <?php
  }
}