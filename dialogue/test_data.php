<?php
declare(strict_types=1);

use cls\App;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueMembership;

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

if (!\cls\FN_IS_LOCAL_HOST()){
  die("This script can only be executed on local host");
}

$path_to_sqlite = $_SERVER ["DOCUMENT_ROOT"] . "/../dimantic.sqlite";
if (file_exists($path_to_sqlite)) {
  unlink($_SERVER ["DOCUMENT_ROOT"] . "/../dimantic.sqlite");
}


App::init_context(basename(__FILE__));

$app = App::get();

[$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);




$majo2 = new \cls\data\account\Account();
$majo2->name = "majo2";
$majo2->email = "kek@kek.de";
$majo2->content = "Majo2";
$majo2->password = password_hash("123", PASSWORD_DEFAULT);
$majo2->save($app->get_database());

$majo = new \cls\data\account\Account();
$majo->name = "majo";
$majo->email = "majo@kek.de";
$majo->content = "Majo";
$majo->password = password_hash("123", PASSWORD_DEFAULT);
$majo->save($app->get_database());


$majo2 = new \cls\data\account\Account();
$majo2->name = "majo44";
$majo2->email = "kek@k44ek.de";
$majo2->content = "Majo44";
$majo2->password = password_hash("123", PASSWORD_DEFAULT);
$majo2->save($app->get_database());

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

// create started dialogue with some messages
$dialogue = new Dialogue();
$dialogue->content = "Already started dialogue";
$dialogue->author_id = 1;
$dialogue->state = Dialogue::STATE_OPEN;
$dialogue->save($app->get_database());

$membership = new DialogueMembership();
$membership->account_id = 1;
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

$message = new \cls\data\dialoge\DialogueMessage();
$message->account_id = 1;
$message->dialogue_id = $dialogue->id;
$message->content = "Hello World";
$message->create_date = date("Y-m-d H:i:s");
$message->save($app->get_database());

$message = new \cls\data\dialoge\DialogueMessage();
$message->account_id = 2;
$message->dialogue_id = $dialogue->id;
$message->content = "Hello World";
$message->create_date = date("Y-m-d H:i:s");
$message->save($app->get_database());


App::dump_logs();