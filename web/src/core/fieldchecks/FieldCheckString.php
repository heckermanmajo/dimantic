<?php

#[Attribute(Attribute::TARGET_PROPERTY)]
class FieldCheckString implements \src\core\IFieldChecker {
  public function __construct(
    public bool   $alphanumeric = false,
    public int    $minLength = 0,
    public int    $maxLength = 255,
    public bool   $allowEmpty = false,
    public string $errorMessage = 'Invalid string'
  ) {
  }
}