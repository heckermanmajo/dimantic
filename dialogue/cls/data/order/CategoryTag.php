<?php

namespace cls\data\order;

use cls\DataClass;

class CategoryTag extends DataClass {
  const DIMENSION_TIME = "time";
  const DIMENSION_CONCEPT = "space";
  const DIMENSION_EXISTENCE = "existence";
  var string $value = "";
  var string $dimension = "";
}