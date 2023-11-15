<?php
declare(strict_types=1);

use cls\App;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueMembership;

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

App::init_context(basename(__FILE__));

$app = App::get();

$majo2 = \cls\data\account\Account::get_by_id(
  $app->get_database(), 3
);
$app->login($majo2);

$dialogue = new Dialogue();
$dialogue->content = "Hello World";
$dialogue->state = Dialogue::STATE_NOT_YET_STARTED;
$dialogue->save($app->get_database());

$membership = new DialogueMembership();
$membership->account_id = $app->get_currently_logged_in_account()->id;
$membership->dialogue_id = $dialogue->id;
$membership->type = DialogueMembership::TYPE_CREATOR;
$membership->state = DialogueMembership::STATE_ACTIVE;
$membership->save($app->get_database());


$membership = new DialogueMembership();
$membership->account_id = 2;
$membership->dialogue_id = $dialogue->id;
$membership->type = DialogueMembership::TYPE_JOIN_REQUEST;
$membership->state = DialogueMembership::STATE_ACTIVE;
$membership->save($app->get_database());



$dialogue = new Dialogue();
$dialogue->content = "Invite member 3";
$dialogue->author_id = 2;
$dialogue->state = Dialogue::STATE_NOT_YET_STARTED;
$dialogue->save($app->get_database());

$membership = new DialogueMembership();
$membership->account_id = 2;
$membership->dialogue_id = $dialogue->id;
$membership->type = DialogueMembership::TYPE_CREATOR;
$membership->state = DialogueMembership::STATE_ACTIVE;
$membership->save($app->get_database());

$membership = new DialogueMembership();
$membership->account_id = 3;
$membership->dialogue_id = $dialogue->id;
$membership->type = DialogueMembership::TYPE_JOIN_REQUEST;
$membership->state = DialogueMembership::STATE_PENDING;
$membership->save($app->get_database());