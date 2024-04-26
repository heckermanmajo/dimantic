<?php

namespace src\app\attention_tree\data\enums;

enum AttentionNodeType: int {
  case SpaceNode = 1;
  case PostContentNode = 2;
  case ConversationNode = 3;
  case AttentionOpportunityNode = 4;
  case ProfileNode = 5;
  case TextNode = 6;

  # ...
}