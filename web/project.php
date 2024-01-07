<?php

use cls\App;
use cls\HtmlUtils;

require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
try {
  App::init_context(basename(__FILE__));
  $app = App::get();
  $app->handle_action_requests();
  HtmlUtils::head();
  HtmlUtils::main_header();
  ?>
  <br>
  <a class="button w3-margin w3-padding" href="/index.php"> ZURÜCK </a>
  <div class="w3-margin">

    <h2>Project information </h2>
    <pre>

    - how to participate in the project (open source coding)
    - how to participate in the project (conceptually / feedback)
    - how to participate in the project (spreading the word)

    The mission of the project.

    Some of the base content to understand the basic mind space we are in.
    -> Video of explanation of the project.

    ---

    Current code explanation-videos of basic code structure and workings (links to YT)



  </pre>
    <h4>Current Roadmap</h4>
    <h6> Dimantic 0.1: Spaces & Agora </h6>
    <pre>
      - Create and update spaces
      - create conversations
      - like 3% of the conversation
      - create discussion-blueprints
      - create conversations from blueprints
      - conversation rules
      - conversation summaries
      - multiple person discussions (set in blueprints)
      - free join spaces (no closed spaces yet)
    </pre>

    <h6> Dimantic 0.2: Simple Documents & side-conversations + backups </h6>
    <pre>
      -
    </pre>

    <h6> Dimantic 0.3: Rooms (sub-spaces) </h6>
    <pre>
      -
    </pre>

    <h6> Dimantic 0.4: Spaces (governance & closed spaces) </h6>
    <pre>
      -
    </pre>

    <h6> Dimantic 0.5: Questions (Preisfragen), Geistmark-System, Idea-Tasks </h6>
    <pre>
      -
    </pre>

    <h6> Dimantic 0.6: Attention-Profiles & embeddings </h6>
    <pre>
      -
    </pre>

    <h6> Dimantic 0.7: Semantic Explorer & Semantic Matching </h6>
    <pre>
      -
    </pre>

    <h6> Dimantic 0.8: Document </h6>
    <pre>
      - tree-documents
      - clone documents
      - pdf documents
    </pre>

    <h6> Dimantic 0.9: Canvas </h6>
    <pre>
      - adds simple canvases
    </pre>

    <h6> Dimantic 1.0: Recommendation </h6>
    <pre>
      - adds simple canvases
    </pre>

    <h6> Dimantic X.0: Payment Update </h6>
    <pre>
      - adds payment-options for the service
    </pre>

    <h6> Dimantic X.0: API  </h6>
    <pre>
      - adds payment-options for the service
    </pre>
  </div>
  <?php
  HtmlUtils::footer();
}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}