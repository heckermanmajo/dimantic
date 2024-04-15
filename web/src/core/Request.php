<?php

namespace src\core;

abstract class Request {
  function __construct(
    protected array $post,
    protected Account $user
  ){}

  abstract function is_allowed();

  abstract function handle(): Component|string|array|null;
}