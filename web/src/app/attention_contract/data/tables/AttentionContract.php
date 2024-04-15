<?php


class AttentionContract extends Table {

  #[FieldCheckString(
    alphanumeric: false,
    minLength: 50
  )]
  #[UniqueCheckFunction(AttentionContract::class, "check_rule_format")]
  var string $rules = "";

  static function check_rule_format(){}

}