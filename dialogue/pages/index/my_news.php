<?php

use cls\App;
use cls\data\account\NewsEntry;

return function(App $app){
  $my_news = NewsEntry::get_my_news($app);
  ?>
  <div class="w3-margin">
    <?php
    foreach ($my_news as $news_entry) {
      echo $news_entry->get_news_card($app);
    }
    ?>
  </div>
  <?php
};