<?php

namespace src\core;

use src\app\user\data\tables\account\Account;
use src\core\exceptions\RequestException;
use src\core\settings\Settings;
use src\global\components\JsonError;

abstract class Request {

  /**
   * @var RequestException|null If the request is invalid, this is the reason.
   */
  public ?RequestException $why_invalid = null;

  /**
   * @var string If the request is not allowed, this is the reason.
   */
  public string $why_not_allowed = "";

  /**
   * @param array $post
   * @param Account|null $user
   * @param bool $is_proxy_request if true the request only exists to check if such a request can be done, to
   *        inform the frontend rendering. This allows for not having to implement the same access-logic twice.
   */
  function __construct(
    protected array $post,
    protected ?Account $user,
    protected bool $is_proxy_request
  ){}

  /**
   * @return bool True if the given user can do this request - no matter the
   * actual input.
   */
  abstract function is_allowed(): bool;

  /**
   * Assumed this request is allowed, this function checks if the input is valid.
   * And the request can be executed.
   *
   * No database or session change is allowed in this function.
   */
  abstract function is_valid(): bool;

  /**
   * This function actually executes the request.
   *
   * Usually changes session state or database state.
   *
   * @return Component|string|array|null Can return a Component to render,
   *         a string to redirect to, an array to return as json or null.
   *
   * Errors are returned as a JSON-ErrorComponent.
   * @see JsonError
   */
  abstract function execute(): Component|string|array|null;

  /**
   * This function places a hidden input field in the form to identify the request.
   * The api.php file is then able to identify the request and execute it.
   *
   * The request path is encrypted to prevent any class from being inserted.
   */
  static function put_hidden_request_class_name_input_field(): void {
    ?>
    <input type="hidden" name="__request_id" value="<?= self::encrypt_class_name() ?>">
    <?php
  }

  private static function encrypt_class_name(): string {
    $string = static::class;
    return $string; # todo: does not work ...
    $cipher = "aes-128-cbc";
    $key = Settings::get_instance()->request_password;
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $encrypted = openssl_encrypt($string, $cipher, $key, 0, $iv);
    return $encrypted;
  }

  final static function decrypt_class_name(string $encrypted_class_name): string {
    return $encrypted_class_name;# todo: does not work ...
    $cipher = "aes-128-cbc";
    $key = Settings::get_instance()->request_password;
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    return openssl_decrypt($encrypted_class_name, $cipher, $key, 0, $iv);
  }

}