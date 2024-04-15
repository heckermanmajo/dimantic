<?php
#[Attribute(Attribute::TARGET_PROPERTY)]
class UniqueCheckFunction implements \src\core\IFieldChecker {
  function __construct(
    public string $className,
    public string $functionName
  ) {
  }
}