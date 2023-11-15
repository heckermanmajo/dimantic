<?php

use cls\controller\request\news\DeleteNewsEntry;
use cls\data\attention_profile\NewsEntry;
use cls\data\post\Post;
use cls\data\tree\Tree;
use cls\RequestError;


include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";


if (isset($_GET["tab"])) {
  $_SESSION["uri_history"][] = "/index.php?tab=" . $_GET["tab"];
}
else {
  $_SESSION["uri_history"][] = "/index.php";
}

App::create_tables();


if (isset($_POST["action"])) {

  if ($_POST["action"] == "register") {
    $result = \cls\controller\request\member\HandleRegistration::execute();
    if ($result instanceof RequestError) {
      echo $result->user_message;
      # todo: handle error message
      exit();
    }
    else {
      // pass ...
    }
  }

  if ($_POST["action"] == "login") {
    $result = \cls\controller\request\member\HandleLogin::execute();
    if ($result instanceof RequestError) {
      $_SESSION["login_api_err"] = $result->user_message;
    }
    else {
      App::login($result);
    }
  }

  if ($_POST["action"] == "logout") {
    $result = \cls\controller\request\member\HandleLogout::execute();
    if ($result instanceof RequestError) {
      echo $result->user_message;
      # todo: better error handling
    }
    else {
      header("Location: /index.php");
      exit();
    }
  }

  if ($_POST["action"] == "delete_news_entry") {
    $result = DeleteNewsEntry::execute();
    if ($result instanceof RequestError) {
      echo $result->user_message;
      # todo: better error handling
    }
    else {
      header("Location: /index.php");
      exit();
    }
  }


}


App::head_html();


if (App::somebody_is_logged_in()) {
  ?>

  <header class="w3-row w3-padding">
    <div class="w3-col l7 m7 s7">

      <a
        class="w3-button" style="<?= (!isset($_GET["tab"])) ? "color: yellow" : "" ?>"
        href="/index.php"> Feed </a>
      <a class="w3-button" style=" <?= ($_GET["tab"]??"") == "leagues" ? "color: yellow": "" ?>"
         href="/index.php?tab=leagues"> Leagues </a>
      <a class="w3-button" style=" <?= ($_GET["tab"]??"") == "idea_spaces" ? "color: yellow" : "" ?>"
         href="/index.php?tab=idea_spaces"> Idea-Spaces </a>
      <a class="w3-button" style=" <?= ($_GET["tab"]??"") == "news" ? "color: yellow" : "" ?>"
         href="/index.php?tab=news"> News </a>
      <a class="w3-button" style="<?= ($_GET["tab"]??"") == "trees" ? "color: yellow" : "" ?>"
         href="/index.php?tab=trees"> Trees </a>
      <a class="w3-button" style=" <?= ($_GET["tab"]??"") ==  "posts" ? "color: yellow" : "" ?>"
         href="/index.php?tab=posts"> Posts </a>
    </div>
    <div class="w3-rest" style="text-align: right">
      <a href="/attention_profile.php" class="button">
        <i style="color: white"><?= App::$attention_profile->title ?></i>
      </a> &nbsp;
      <?php App::echo_nav_bar(); ?>
      <form method="post" style="display: inline-block">
        <input type="hidden" name="action" value="logout">
        <button class="delete-button"> Logout</button>
      </form>
    </div>
  </header>

  <?php
  switch ($_GET["tab"] ?? ""):

    #################################################################################
    case "news":
      ?>
      <div class="w3-row">
        <div class="w3-half">
          <?php
          $news = NewsEntry::get_array(
            App::get_connection(),
            "SELECT * FROM `NewsEntry` WHERE `target_member_id` = ? ORDER BY `id` DESC;",
            [App::get_current_account()->id]
          );
          foreach ($news as $news_entry) {
            ?>
            <div class="w3-card-4 w3-margin w3-padding">
              <div><b><?= $news_entry->attention_source ?></b></div>
              <?php if ($news_entry->news_type == "answer_to_my_post" || $news_entry->news_type == "answer_to_observed_post"): ?>
                <a href="/index.php?tab=news&post_id=<?= $news_entry->post_id ?>" style="text-decoration: none">
                  <h3><?= $news_entry->title ?></h3>
                </a>
              <?php else: ?>
                <h3><?= $news_entry->title ?></h3>
              <?php endif; ?>
              <div><?= $news_entry->content ?></div>
              <form method="post" style="display: inline">
                <input type="hidden" value="<?= $news_entry->id ?>" name="news_entry_id">
                <input type="hidden" value="delete_news_entry" name="action">
                <button class="delete-button">Delete</button>
              </form>
              <pre><?= json_encode($news_entry, JSON_PRETTY_PRINT) ?></pre>
            </div>
            <?php
          }
          ?>
        </div>
        <div class="w3-half">
          <?php
          if (isset($_GET["post_id"])) {
            $post = Post::get_one(
              App::get_connection(),
              "SELECT * FROM `Post` WHERE `id` = ?;",
              [$_GET["post_id"]]
            );
            if ($post !== null) {
              $post->echo_overview_display_card();
            }
            else {
              echo "Post not found.";
            }

            echo "<h2>Antwort auf:</h2>";

            $parent_post = Post::get_one(
              App::get_connection(),
              "SELECT * FROM `Post` WHERE `id` = ?;",
              [$post->parent_post_id]
            );

            if ($parent_post !== null) {
              $parent_post->echo_overview_display_card();
            }
            else {
              echo "Parent post not found.";
            }

          }
          ?>
        </div>
      </div>
      <?php
      break;

    #################################################################################
    case "trees":
      ?>
      <div class="w3-row">
        <div class="w3-half">
          <a class="button w3-margin" href="/tree.php"> Create Tree + </a>
          <a class="button w3-margin" href="/index.php?tab=trees&filter=observed"> Observed Trees </a>
          <a class="button w3-margin" href="/index.php?tab=trees"> My Trees </a>
          <?php
          $trees = Tree::get_array(
            App::get_connection(),
            "SELECT * FROM `Tree` WHERE `author_id` = ?;",
            [App::get_current_account()->id]
          );

          foreach ($trees as $tree) {
            $tree->echo_short_display_view();
          }

          ?>
        </div>
        <div class="w3-half">

        </div>
      </div>
      <?php
      break;

    #################################################################################
    case "posts":
      ?>
      <div class="w3-row">
        <div class="w3-half">
          <!--<a href="/post.php" class="button w3-margin"> Create new Post + </a>-->
          <a href="/index.php?tab=posts&filter=observed" class="button w3-margin"> Observed Posts </a>
          <a href="/index.php?tab=posts" class="button w3-margin"> My Posts </a>
          <!--<h5> Filter .... </h5>-->
          <?php
          foreach (Post::get_my_posts() as $child) $child->echo_overview_display_card();
          ?>
        </div>
        <div class="w3-half">
          <h3>post read or simple text edit</h3>
        </div>
      </div>
      <?php
      break;

    #################################################################################
    case "leagues":
      ?>
      <div class="w3-row">
        <div class="w3-half">
          <p class="w3-margin"> TODO: Leagues my post participate in ... </p>
          <?php
          $leagues = \cls\data\league\AttentionLeague::get_array(
            App::get_connection(),
            "SELECT * FROM AttentionLeague",
            []
          );
          foreach ($leagues as $league) {
            $this_season = $league->get_latest_season();
            $post_number_this_season = Post::get_count(
              App::get_connection(),
              "SELECT COUNT(*) FROM Post WHERE Post.liga_season_id = :season_id;",
              [
                "season_id" => $this_season->id,
              ]
            );
            ?>
            <div class="w3-card-4 w3-margin w3-padding">
              <a
                style="text-decoration: none"
                href="/league.php?id=<?= $league->id ?>">
                <h3><?= $league->get_title() ?></h3>
                <pre><?= json_encode($league, JSON_PRETTY_PRINT) ?></pre>
              </a>
              <p>Current Season: <?= $this_season->season_description ?></p>
              <p>Number of Posts: <?= $post_number_this_season ?></p>
            </div>
            <a class="button" href="/index.php?tab=leagues&id=<?= $league->id ?>"> Display Posts </a>
            <?php
          }
          ?>
        </div>
        <div class="w3-half">
          <h3>post read or simple text edit</h3>
          <?php
          if (isset($_GET["id"])) {
            $league = \cls\data\league\AttentionLeague::get_by_id(
              App::get_connection(),
              $_GET["id"]
            );
            $season = $league->get_latest_season();
            $posts = Post::get_array(
              App::get_connection(),
              "SELECT * FROM Post WHERE Post.liga_season_id = :season_id;",
              [
                "season_id" => $season->id,
              ]
            );
            foreach ($posts as $post) {
              $post->echo_overview_display_card();
            }
          }
          ?>
        </div>
      </div>
      <?php
      break;


    #################################################################################
    case "idea_spaces":
      ?>
      <div class="w3-row">
        <div class="w3-half">
          <?php
          $idea_spaces = \cls\data\idea_space\IdeaSpace::get_array(
            App::get_connection(),
            "SELECT * FROM `IdeaSpace` WHERE id IN 
                                (SELECT idea_space_id FROM IdeaSpaceMembership 
                                                      WHERE IdeaSpaceMembership.account_id=:account_id 
                                                        AND IdeaSpaceMembership.left_idea_space = 0);",
            ["account_id" => App::get_current_account()->id]
          );

          foreach ($idea_spaces as $space) {
            $space->put_display_card();
          }
          ?>
          <hr>
          <hr>
          <?php
          $idea_spaces = \cls\data\idea_space\IdeaSpace::get_array(
            App::get_connection(),
            "SELECT * FROM `IdeaSpace` WHERE id NOT IN 
                                (SELECT idea_space_id FROM IdeaSpaceMembership 
                                                      WHERE IdeaSpaceMembership.account_id=:account_id 
                                                        AND IdeaSpaceMembership.left_idea_space = 0);",
            ["account_id" => App::get_current_account()->id]
          );

          foreach ($idea_spaces as $space) {
            $space->put_display_card();
          }
          ?>
        </div>
        <div class="w3-half">
          <h3>post read or simple text edit</h3>
        </div>
      </div>
      <?php
      break;

    #################################################################################
    default:
      /**
       * @see \cls\data\attention_profile\AttentionProfile::$feed_ids
       */
      ?>
      <!--<h3> Feed von vorgeschlagenem Content, basierend auf dem Attention Profile </h3>
      <pre>
        Jedes mal wenn ein ost hier vorgeschlagen wird, dann wird eine Impression erstellt
        für diesen Post.
      </pre>-->
      <?php
      $tmp_feed_posts = Post::get_array(
        App::get_connection(),
        "SELECT * FROM Post ORDER BY RANDOM() LIMIT 6;",
        []
      );
      ?>
      <div class="w3-row">
        <?php
        for ($i = 0; $i < 3; $i++) {
          echo "<div class='w3-third'>";
          $tmp_feed_posts[$i]->echo_overview_display_card();
          echo "</div>";
        }
        ?>
      </div>
      <div class="w3-row">
        <?php
        for ($i = 3; $i < 6; $i++) {
          echo "<div class='w3-third'>";
          $tmp_feed_posts[$i]->echo_overview_display_card();
          echo "</div>";
        }
        ?>
      </div>
    <?php

  endswitch;

}
else { # nobody is logged in
  ?>
  <div class="w3-row">

    <div class="w3-col l9 s9 m9">
      <h1 style="text-align: center; color: #ffd205"> ᛞᛁᛗᚨᚾᛏᛁᚲ </h1>
      <div style="text-align: center; color: #ffd205; font-size: 60px">
        <i class="fas fa-gem"></i>
      </div>
    </div>

    <div class="w3-rest">

      <form method="post" class="w3-card w3-margin w3-padding">
        <input type="hidden" name="action" value="login">
        <div class="w3-red"><?= $_SESSION["login_api_err"] ?? "" ?></div>
        <label>
          <i style="color: #bfc0c0">Email or Name</i><br>
          <input type="text" name="email_or_name">
        </label>
        <br>
        <label>
          Password<br>
          <input type="password" name="password">
        </label>
        <br><br>
        <button class="button">Login</button>
      </form>

      <hr>

      <form method="post" class="w3-card  w3-margin w3-padding">
        <input type="hidden" name="action" value="register">
        <label>
          Name<br>
          <input type="text" name="name">
        </label>
        <br>
        <label>
          Email<br>
          <input type="text" name="email">
        </label>
        <br>
        <label>
          Password<br>
          <input type="password" name="password">
        </label>
        <br>
        <br>
        <button class="button">Register</button>
      </form>
    </div>

  </div>
  <?php
}

App::footer();