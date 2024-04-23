<?php

namespace src\app\attention_tree\data\dataclass;

readonly class JSTreeNode {

  function __construct(
    public string $text,
    public string $icon,
    public array $extra_data,
    public string $children_ajax_url,
  ){}

  function get_array(): array {
    return [
      "text" => $this->text,
      "icon" => $this->icon,
      "extra_data" => $this->extra_data,
      "children_ajax_url" => $this->children_ajax_url,
    ];
  }

  function get_json(): string {
    return json_encode($this->get_array());
  }

}