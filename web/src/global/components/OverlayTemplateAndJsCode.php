<?php

namespace src\global\components;

use src\app\user\data\compositions\GetDarkmodeActive;
use src\core\Component;

class OverlayTemplateAndJsCode extends Component {


  public function render(): void {
    ?>

    <script>

        function showOverlay(
            content_load_url,
            arguments = {},
            success_callback = () => {
            }
        ) {
            document.getElementById("overlay").style.display = "block";
            $.post(content_load_url, arguments, function (data) {
                console.log(data)
                document.getElementById("overlay_content").innerHTML = data;
                executeScriptElements(document.getElementById("overlay_content"));
                const close_button = document.createElement("button");
                close_button.innerHTML = "<i class='fas fa-times'></i> Close Overlay";
                close_button.style = "position: absolute; top: 0; right: 0; padding: 5px";
                close_button.className = "red-button";
                close_button.onclick = function () {
                    document.getElementById("overlay").style.display = "none";
                    document.getElementById("overlay_content").innerHTML = "Loading ...";
                };
                document.getElementById("overlay_content").appendChild(close_button);
                success_callback();
            });
        }

        function executeScriptElements(containerElement) {
            const scriptElements = containerElement.querySelectorAll("script");

            Array.from(scriptElements).forEach((scriptElement) => {
                const clonedElement = document.createElement("script");

                Array.from(scriptElement.attributes).forEach((attribute) => {
                    clonedElement.setAttribute(attribute.name, attribute.value);
                });

                clonedElement.text = scriptElement.text;

                scriptElement.parentNode.replaceChild(clonedElement, scriptElement);
            });
        }

        // on escape key press, close the overlay
        document.onkeydown = function (evt) {
            evt = evt || window.event;
            if (evt.keyCode === 27) {
                document.getElementById("overlay").style.display = "none";
                document.getElementById("overlay_content").innerHTML = "Loading ...";
            }
        };

    </script>

    <div
      id="overlay"
      style="
         position: fixed; /* Sit on top of the page content */
         display: none; /* Hidden by default */
         width: 100%; /* Full width (cover the whole page) */
         height: 100%; /* Full height (cover the whole page) */
         top: 0;
         left: 0;
         right: 0;
         bottom: 0;
         background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
         z-index: 2; /* Specify a stack order in case you're using a different order for other elements */
         cursor: pointer; /* Add a pointer on hover */
      ">
      <div
        id="overlay_content"
        style="
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
        <?php if (GetDarkmodeActive::is_active()): ?>
          background-color: #3a3a3a;
          color: white;
        <?php else: ?>
          background-color: #fefefe;
          color: black;
        <?php endif; ?>
          text-align: center;
          padding: 20px;
          z-index: 3;
          border-radius: 5px;
          box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);
          "
      >Overlay Text
      </div>
    </div>


    <?php
  }
}