<?php

require_once(__DIR__.'/redmine/class_Issue.php');

$bot->hear("/Привет/iu", function($bot, $msg) {
   $msg->body = "Привет.";
   $bot->send($msg);
});

/*hear("/На '(?P<tpl>.*?)' надо отвечать '(?P<response>.*?)'/ui", function($bot, $msg, $matches) {
   $resp = $matches['response'];
   hear("/{$matches['tpl']}/ui", function($b, $m) use ($resp) {
      $m->body = $resp;
      $b->send($m);
   });
   $msg->body = "Запомнил.";
   $bot->send($msg);
});*/

$bot->hear("/(Хватит музыки|останови музыку|mpc pause)/iu", function($bot, $msg) {
   exec("mpc pause");
   $msg->body = "Сделано.";
   $bot->send($msg);
});

$bot->hear("/(Включи музыку|mpc play)/iu", function($bot, $msg) {
   exec("mpc play");
   $msg->body = "Готово!";
   $bot->send($msg);
});

$bot->hear("/(Что сейчас играет|Что за песня|mpc current)/iu", function($bot, $msg) {
   $r = exec("mpc current");
   $msg->body = $r ? :  "Не могу сказать точно.";
   $bot->send($msg);
});

$bot->hear("/(close|закр(ыть|ой)( задачу)?) #?(?P<id>\d+)/iu", function($bot, $msg, $matches) {
   $issue = new \Redmine\Issue();
   $issue = $issue->find($matches['id']);
   if ($issue->error) {
      $msg->body = "Задачи #{$matches['id']} не существует или она была удалена.";
   }
   else {
      $msg->body = "Вы про задачу \"{$issue->subject}\"?";
      $bot->prompt($pattern = "/Да(, я про нее)?/iu", function($bot, $msg, $matches) use ($pattern, $issue) {
         // закрываю задачу
         $issue->set('status_id', \Redmine\Issue::STATUS_RESOLVED);
         //$issue->save();

         $msg->body = "Закрыл.";
         $bot->send($msg);

      });
   }
   $bot->send($msg);
});
