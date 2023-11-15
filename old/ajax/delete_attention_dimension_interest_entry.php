<?php

use cls\controller\request\attention_dimension_interest_entry\DeleteAttentionDimensionInterestEntry;
use cls\RequestError;

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

$result = DeleteAttentionDimensionInterestEntry::execute();

if($result instanceof RequestError){
  echo $result->user_message;
  exit();
}
else{
  echo "Attention dimension interest was deleted.";
}