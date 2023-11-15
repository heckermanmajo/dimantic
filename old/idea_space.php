<?php

use cls\data\idea_space\IdeaSpace;
use cls\data\post\Post;

include $_SERVER['DOCUMENT_ROOT'] . "/cls/App.php";

if (!App::somebody_is_logged_in()) {
  header("Location: /index.php");
  exit();
}

App::head_html();

$id = $_GET["id"] ?? exit("id missing");

$idea_space = IdeaSpace::get_by_id(App::get_connection(), $id);

if (isset($_POST["action"])) {

  if ($_POST["action"] == "join_idea_space") {
    $result = \cls\controller\request\idea_space\ApplyForSpace::execute();
    if ($result instanceof \cls\RequestError) {
      echo $result->dev_message;
      # todo: handle error message
      exit();
    }
    else {
      header("Location: /idea_space.php?id=$id&tab=members");
      exit();
    }
  }

  if ($_POST["action"] == "leave_idea_space") {
    $result = \cls\controller\request\idea_space\LeaveIdeaSpace::execute();
    if ($result instanceof \cls\RequestError) {
      echo $result->dev_message;
      # todo: handle error message
      exit();
    }
    else {
      header("Location: /idea_space.php?id=$id&tab=members");
      exit();
    }
  }

  if ($_POST["action"] == "support_post") {
    $result = \cls\controller\request\idea_space\SupportPostInIdeaSpace::execute();
    if ($result instanceof \cls\RequestError) {
      echo $result->dev_message;
      # todo: handle error message
      exit();
    }
    else {
      header("Location: /idea_space.php?id=$id&tab=current_league_competition_posts&league_id=" . $_GET["league_id"]);
      exit();
    }
  }

  if ($_POST["action"] == "un_support_post") {
    $result = \cls\controller\request\idea_space\UnSupportPostIdeaSpace::execute();
    if ($result instanceof \cls\RequestError) {
      echo $result->dev_message;
      # todo: handle error message
      exit();
    }
    else {
      header("Location: /idea_space.php?id=$id&tab=current_league_competition_posts&league_id=" . $_GET["league_id"]);
      exit();
    }
  }


}


?>
  <header class="w3-margin">
    <a class='button' href='/index.php'> Zurück </a>
  </header>

  <div class="w3-card w3-margin w3-padding">
    Idea-Space: <b><?= $idea_space->get_title() ?></b>
    <br>
    <?= $idea_space->description ?>
  </div>

  <nav class="w3-padding">
    <a class="button" href="/idea_space.php?id=<?= $id ?>&tab=posts">Posts</a> &nbsp;
    <a class="button" href="/idea_space.php?id=<?= $id ?>&tab=members">Members</a>&nbsp;
    <!--<a class="button" href="/idea_space.php?id=<?= $id ?>&tab=open_ratings">Rating-Matches</a>&nbsp;-->
    <a class="button" href="/idea_space.php?id=<?= $id ?>&tab=current_league_competition_posts"> Current League
      Competition Posts </a>&nbsp;
  </nav>


<?php
switch ($_GET["tab"] ?? "") {


  case "posts":
    ?>
    <div class="w3-margin">
      <a class="button" href="/post.php?idea_space=<?= $id ?>">
        Create Post
      </a>
    </div>
    <?php

    $posts = \cls\data\post\Post::get_array(
      App::get_connection(),
      "SELECT * FROM `Post` WHERE idea_space_id = :idea_space_id;",
      ["idea_space_id" => $id]
    );

    foreach ($posts as $post) {
      $post->echo_overview_display_card();
    }

    break;


  case "members":
    # todo: check membership state
    # todo: make this a function in the model ...
    $is_member = \cls\data\idea_space\IdeaSpaceMembership::get_one(
        App::get_connection(),
        "SELECT * FROM IdeaSpaceMembership 
         WHERE account_id = :account_id 
           AND idea_space_id = :idea_space_id
           AND left_idea_space = 0;",
        [
          "account_id" => App::get_current_account()->id,
          "idea_space_id" => $id
        ]
      ) !== null;

    if (!$is_member):
      ?>
      <form method="post" class="w3-margin w3-card w3-padding">
        <input type="hidden" name="action" value="join_idea_space">
        <input type="hidden" name="space_id" value="<?= $_GET["id"] ?>">
        <button class="button"> Join Idea Space</button>
      </form>
    <?php
    else:
      ?>
      <form method="post" class="w3-margin w3-card w3-padding">
        <p> You are a member of this idea space </p>
        <input type="hidden" name="action" value="leave_idea_space">
        <input type="hidden" name="space_id" value="<?= $_GET["id"] ?>">
        <button class="button"> Leave Idea Space</button>
      </form>
    <?php
    endif;


    $members_of_ideaspace = \cls\data\account\Account::get_array(
      App::get_connection(),
      "SELECT * FROM `Account` WHERE id IN (
        SELECT account_id FROM IdeaSpaceMembership WHERE idea_space_id = :idea_space_id
      );",
      ["idea_space_id" => $id]
    );

    foreach ($members_of_ideaspace as $member) {
      $member->put_display_card();
    }


    break;


  case "one_member":
    ?>
    <h2> One Member of this idea space </h2>
    <p> All posts within this idea space an account has created</p>
    <?php
    break;


  case "open_ratings":
    ?>
    <h2> Open Ratings in this idea space </h2>
    <p> All posts within this idea space an account has created</p>
    <?php
    break;


  case "current_league_competition_posts":

    ?>
    <div class="w3-row">
      <div class="w3-half">
        <?php

        $all_leagues_competitions_exist = \cls\data\league\AttentionLeague::get_array(
          App::get_connection(),
          "SELECT * FROM `AttentionLeague` WHERE id IN
            (SELECT AttentionLeagueSeason.attention_league_id FROM AttentionLeagueSeason 
                WHERE AttentionLeagueSeason.id IN 
                    (SELECT Post.liga_season_id FROM Post WHERE Post.idea_space_id = :this_idea_space_id)
                AND (AttentionLeagueSeason.state = 'open' OR AttentionLeagueSeason.state = 'rating')
            )
            ;",
          ["this_idea_space_id" => $id]
        );

        foreach ($all_leagues_competitions_exist as $league) {
          $post_number = Post::get_count(
            App::get_connection(),
            "SELECT COUNT(*) FROM Post WHERE Post.liga_season_id = :season_id AND Post.idea_space_id = :idea_space_id;",
            [
              "season_id" => $league->get_latest_season()->id,
              "idea_space_id" => $id
            ]
          );
          ?>
          <div class="w3-card-4 w3-margin w3-padding">
            <b><?= $league->get_title() ?> </b><br>
            <p><?= $post_number ?> posts are competing to represent this idea space in league</p>
            <a class="button"
               href="/idea_space.php?id=<?= $id ?>&tab=current_league_competition_posts&league_id=<?= $league->id ?>">View
              Posts</a>
          </div>
          <?php
        }

        ?>
        <!--<h2> All Leagues for that posts exist within this idea space </h2>
        <p>A list of all leagues where posts from this idea space compete in</p>
        <p>if clicked pon one, display all the matches at the right and also all the posts on the right</p>
        -->
      </div>
      <div class="w3-half">
        <?php
        if (isset($_GET["league_id"])):

          $league = \cls\data\league\AttentionLeague::get_by_id(
            App::get_connection(),
            $_GET["league_id"]
          );

          $season = $league->get_latest_season();

          $all_posts_of_this_space_for_season = Post::get_array(
            App::get_connection(),
            "SELECT * FROM Post WHERE Post.liga_season_id = :season_id AND Post.idea_space_id = :idea_space_id;",
            [
              "season_id" => $season->id,
              "idea_space_id" => $id
            ]
          );

          $support_entry = \cls\data\post\PostSupportEntry::get_one(
            App::get_connection(),
            "SELECT * FROM PostSupportEntry WHERE account_id = :account_id AND season_id = :season_id;",
            [
              "account_id" => App::get_current_account()->id,
              "season_id" => $season->id
            ]
          );

          foreach ($all_posts_of_this_space_for_season as $post) {
            $post->echo_overview_display_card();
            if ($support_entry !== null) {
              if ($support_entry->post_id == $post->id) {
                ?>
                <p class="w3-margin" style="color: greenyellow"> You support this post </p>
                <form class="w3-margin" method="post">
                  <input type="hidden" name="action" value="un_support_post">
                  <input type="hidden" name="post_id" value="<?= $post->id ?>">
                  <button class="delete-button" style="color: greenyellow !important;"> Unsupport </button>
                </form>
                <?php
              }
            }
            else {
              ?>
              <form class="w3-margin" method="post">
                <input type="hidden" name="action" value="support_post">
                <input type="hidden" name="post_id" value="<?= $post->id ?>">
                <button class="button" style="color: greenyellow !important;"> I support this Post</button>
              </form>
              <?php
            }
          }

        endif;
        ?>
      </div>
    </div>
    <?php
    break;


  default:
    ?>
    <h2> Best Posts of this idea space </h2>
    <p> All posts in order of new-ness that have won in leagues, then in order
      have participated at least</p>
  <?php
}
?>