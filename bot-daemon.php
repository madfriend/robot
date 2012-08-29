<?php

require_once(__DIR__.'/JAXL/jaxl.php');
require_once(__DIR__.'/lib/class_Robot.php');

$client = new JAXL(array(
    'jid' => 'redmine@jabber.artfactor',
    'pass' => '123456',
    'host' => 'localhost',
    'port' => '5222',
    'loop_callbacks' => array(
        function() {
            if (!is_object(Bot\Robot::$dbus)) return;
            Bot\Robot::$dbus->waitLoop(1);
        }
    )
));

$bot = new Bot\Robot($client);

$client->add_cb('on_auth_success', $onauth = function() use (&$bot, &$client) {
    //_debug("Authorized");
    $client->set_status("I can't wait to hear something from you.", TRUE, 1);  // set your status
    $client->get_vcard();               // fetch your vcard
    $client->get_roster();              // fetch your roster list
    $msg = new XMPPMsg(array(
       'to' => "alexandr@jabber.artfactor",
       'from' => "redmine@jabber.artfactor"),
    "Hi there!");
    $bot->send($msg);
    require(__DIR__.'/bot-rules.php');
    $bot::register('ru.bot.jabber', '/ru/bot/jabber', '\TestClass'); // Register robot as DBus service
});

$client->add_cb('on_chat_message', function($msg) use (&$bot, &$client) {
    $bot->setReceiver(bot\Robot::EVERYONE);
    require(__DIR__.'/bot-rules.php');
    // echo back
    $msg->to = $msg->from;
    $msg->from = $client->full_jid->to_string();
    $bot->setReceiver($msg->to);
    $bot->process($msg);
});

$client->add_cb('on_disconnect', function() use (&$bot) {
   print "Disconnected.\n";
});

$client->add_cb('on_auth_failure', function() {
    print "Auth failure.\n";
});

$client->add_cb('on_connect_error', function() {
    print "Connection error.\n";
});

class TestClass {

    static function test() {
        return \Bot\Robot::test();
    }
}

$client->start();

