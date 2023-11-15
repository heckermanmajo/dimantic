<?php

namespace cls\data\attention_profile;

use cls\DataClass;

/**
 * If a user is interested into an attention dimension, he creates
 * an entry for this dimension.
 */
class AttentionDimensionInterestEntry extends DataClass {
  var int $attention_profile_id = 0;
  var int $attention_dimension_id = 0;
  var string $created_at = "";
}