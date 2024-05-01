<?php

namespace src\app\attention_contract\enums;

enum ContractType : int {


  case PlatformBehavior = 0;


  case SpaceBehavior = 1;


  case ConversationBehavior = 2;


  case PostBehavior = 3;


  case SpaceRoleBehavior = 4;


  case Arbitration = 5;


  # how does a member need to behave
  case SpaceMembership = 6;


}