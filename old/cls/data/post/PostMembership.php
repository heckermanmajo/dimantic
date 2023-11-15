<?php

namespace cls\data\post;

use cls\DataClass;

class PostMembership extends DataClass {
  var int $post_id = 0;
  var int $member_id = 0;
  var string $status = "";# invitation, member, application
  var string $_member_name = "";
}