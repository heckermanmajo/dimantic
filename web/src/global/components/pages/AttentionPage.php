<?php

namespace src\global\components\pages;

use src\core\Component;
use src\global\components\AttentionBreadCrumbs;
use src\global\components\AttentionProfileSidebar;
use src\global\components\MainNavigationBar;

readonly class AttentionPage extends Component {

  public function render(): void {
    $main_nav_bar = new MainNavigationBar(
      middle: new readonly class extends Component {
        public function render(): void {
          ?>
          <!--<a href="?p=ap"><i class="fas fa-eye"></i><i class="fas fa-project-diagram"></i> <i class="fas fa-mountain"></i> Overview </a> &nbsp; &nbsp; | &nbsp; &nbsp;
          <a href="?p=ap&board"> <i class="fas fa-object-group"></i> Whiteboard </a>  &nbsp; &nbsp; | &nbsp; &nbsp;
          <a href="?p=ap&explore"> <i class="fas fa-binoculars"></i> Explore </a>&nbsp; &nbsp; | &nbsp; &nbsp;
          <a href="?p=ap&history"> <i class="fas fa-user-clock"></i>History </a>-->
          <?php
        }
      },
      right: new readonly class extends Component {
        public function render(): void {
          ?>
          <a href="?p=ap&config"> <i class="fas fa-filter"></i> Configuration </a>
          <?php
        }
      }
    );
    #$main_nav_bar->render();

    $space_sidebar = new AttentionProfileSidebar();
    $bread_crumbs = new AttentionBreadCrumbs();
    $bread_crumbs->render();
    ?>
    <div class="w3-row">

      <div class="w3-half">
        <div id="jstree_demo_div"></div>
        <script>

            $(function () {
                $('#jstree_demo_div').jstree(
                    // some example data
                    {
                        "core": {
                            "data": [
                                {
                                    "text": "Root node",
                                    "state": {"opened": true},
                                    "children": [
                                        {
                                            "text": "Soziologie",
                                            "a_attr": {"href": "/?p=space"},
                                            "children": [
                                                {"text": "Kultur"},
                                                {"text": "Politik"},
                                                {"text": "Wirtschaft"}
                                            ]
                                        },
                                        {
                                            "text": "Gamedev",
                                            "children": [
                                                {"text": "Space for game dev 1"},
                                                {"text": "Very interesting post"},
                                                {
                                                    "text": "Godot",


                                                    "children": [
                                                        {"text": "Godot Space 1 "},
                                                        {"text": "Godot SPace 2"},
                                                        {
                                                            "text": "Godot Tutorial Tree",

                                                            "children": [
                                                                {"text": "Godot Tutorial 1"},
                                                                {"text": "Godot Tutorial 2"},
                                                                {"text": "Godot Tutorial 3"}
                                                            ]

                                                        }
                                                    ]

                                                }
                                            ]
                                        }
                                    ]
                                }
                            ]
                        }
                    }
                );

                $("#jstree_demo_div").on("select_node.jstree", function (evt, data) {
                    console.log(data.node);
                    window.location.href = data.node.a_attr.href;
                    //do more stuff!
                });

            })


        </script>
      </div>

      <div class="w3-half">
        <h4>Details of selected node</h4>
      </div>

    </div>
    <?php
  }


}