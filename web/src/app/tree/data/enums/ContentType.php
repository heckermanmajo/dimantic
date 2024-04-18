<?php

namespace src\app\tree\data\enums;

enum ContentType: int{
  case POST = 1;
  case COMMENT = 2;
  case PREDICTION_MARKET = 3;
  case DEFINITION = 4;
  case PAPER = 5;
  case SUBTREE = 6;

  case NEWS_ENTRY = 8;
  case RULE = 9;
  case CONTRACT = 10;
  case RATING = 11;
  /**
   * A space can define labels.
   */
  case LABEL = 12;
  case MEMBER_ATTACHMENT = 13; # adding a member as an attachment
  case SPACE_SPACE = 14; # adding a space as an attachment
  case CONTENT_ATTACHMENT = 15;# adding some platform intern content
  case WEBREF_ATTACHMENT = 16;#  adding some external content
  case MINI_TREE = 17; # a tree that is assembled by only on person and
  # tries to structure a topic
  # implement this using js-tree
  # every node can then be a child node of the "real tree"

  case PROJECT = 18; # a project is a kinda task
}