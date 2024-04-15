<?php
namespace src\core;

use Error;
use ReflectionClass;

abstract class Action {

  abstract function is_allowed(): bool;

  abstract function execute(): void;

  function get_action_name (): string {
    return (new ReflectionClass($this))->getShortName();
  }

  function throw_on_not_allowed(){

  }

  function rollback(){
    throw new Error("rollback not implemented");
  }

}
