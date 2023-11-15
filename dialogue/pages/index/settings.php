<?php

use cls\App;

return function (App $app){
  ?>
  <button
    class="button w3-margin"
    onclick="FN_TOGGLE('settings_info')"
  >
    <img alt="help button" src="/res/info.png" width="30">
  </button>
  <div
    class="info-card"
    id="settings_info"
    style="display: none">

    <p><img alt="help button" src="/res/info.png" width="60"> Your Profile </p>
    <p>Write some text about you:</p>
    <ul>
      <li>What topics dou you want to talk about?</li>
      <li>What is your intellectual mission?</li>
      <li>What is your expertise?</li>
    </ul>
    <p> For your <b>Profile-Image</b> use Gravatar: <a style="color: dodgerblue" href="https://de.gravatar.com/">https://de.gravatar.com/</a>
    </p>
    <p> There you can provide a profile image and associate it with your email. </p>
    <p> Wordpress and GitHub use it too. </p>
  </div>

  <form method="post" class="w3-card w3-margin w3-padding">
    <input type="hidden" name="action" value="edit_profile">
    <label>
            <span style="margin-bottom: 10px;display: inline-block">
              <i>Profile Description (can be read by everybody) - don't forget to click save: </i></span>
      <br>
      <textarea rows="10" cols="100"
                name="content"><?= $app->get_currently_logged_in_account()->content ?></textarea>
    </label>
    <br>
    <button class="button" type="submit"> Save ✅</button>
  </form>
  <?php
};