<?php

use src\core\App;
use src\core\table\Table;

const TEST = true;
include __DIR__ . "/../core/App.php";


enum MyEnum:String {
  case A = "a";
  case B = "b";
}

class SomeJSONObject implements JsonSerializable {
  public function jsonSerialize() {
    return [
      "serialized" => true,
      "a" => 1,
      "b" => 2,
      "c" => 3,
    ];
  }
}

class SomeClass extends Table {
  function __construct(
    public string $name = "",
    public int $age = 0,
    public MyEnum $enum = MyEnum::A,
    public float $float = 0.0,
    public array $array = ["a", "b", "c"],
    public ?SomeJSONObject $object = null,
    array $data_from_db = []
  ) {
    parent::__construct($data_from_db);
  }
}

$object = new SomeClass();
$object->name = "John";
$object->age = 30;
$object->enum = MyEnum::B;
$object->float = 3.14;
$object->array = ["x", "y", "z"];
$object->object = new SomeJSONObject();
$object->save();