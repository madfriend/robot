<?php

namespace Redmine;

require_once(__DIR__.'/../phpactiveresource/ActiveResource.php');

class Issue extends \ActiveResource {

   public $site = "http://b1162c19ecb959be6bb1ea8fde7181a74f5d184b:X@localhost:3000/";
   public $request_format = "xml";

   const STATUS_NEW         = 1,
         STATUS_IN_PROGRESS = 2,
         STATUS_PENDING     = 3,
         STATUS_FEEDBACK    = 4,
         STATUS_RESOLVED    = 5,
         STATUS_CLOSED      = 6,
         STATUS_TESTING     = 7,
         STATUS_AS_DESIGNED = 8;





}