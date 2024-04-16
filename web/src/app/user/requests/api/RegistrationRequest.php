<?php

namespace src\app\user\requests\api;

use ReflectionException;
use src\app\user\data\tables\account\Account;
use src\app\user\data\tables\account\AccountValidator;
use src\app\user\enums\AccountState;
use src\core\Component;
use src\core\exceptions\BadRequest;
use src\core\exceptions\BadValue;
use src\core\exceptions\FieldNotFound;
use src\core\exceptions\RequestException;
use src\core\Request;
use Throwable;

class RegistrationRequest extends Request {

  protected Account $new_account;

  function is_allowed(): bool {
    if ($this->user !== null) {
      $this->why_not_allowed = "Already logged in, but tried login";
      return false;
    }
    return true;
  }

  /**
   * @return bool
   * @throws ReflectionException
   * @throws Throwable
   */
  function is_valid(): bool {
    try {

      if ($this->user !== null) {
        throw new BadRequest("Already logged in, but tried login");
      }


        $this->post["privacy_policy"] ?? throw new FieldNotFound("You need to agree.");

        $this->post["agb"] ?? throw new FieldNotFound("You need to agree.");


      $password = $this->post["password"]
        ?? throw new FieldNotFound("No password provided.");

      $password_repeat = $this->post["password_repeat"]
        ?? throw new FieldNotFound("No password repeat provided.");

      if ($password !== $password_repeat) {
        throw new BadValue("Passwords do not match.");
      }

      $password_hash = password_hash($password, PASSWORD_DEFAULT);

      $a = new Account(
        username: $this->post["username"] ?? throw new FieldNotFound("No username provided."),
        email: $this->post["email"] ?? throw new FieldNotFound("No email provided."),
        password: $password_hash,
        state: AccountState::NEW_USER
      );

      $this->new_account = $a;

      throw new BadValue("WE WANT TO SEE AN ERROR CARD");


      # is this is a proxy request we need to validate the account
      # and its context - otherwise we can skip this step
      if (!$this->is_proxy_request) {

        $av = new AccountValidator($a);
        $_ = $av->validate(throw: true, in_request: true);
        $_ = $av->validateContext(throw: true, in_request: true);

      }

      return true;

    } catch (RequestException $re) {

      $this->why_invalid = $re;
      $re->set_request($this);

      return false;

    }
  }

  /**
   * @throws ReflectionException
   */
  function execute(): Component|string|array|null {

    $this->new_account->save();

    # todo: create initial attention profiles
    # todo: create initial transaction

    #App::login(account: $account);

    return "index.php?new_user";

  }


}