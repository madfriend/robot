<?php

namespace Bot {

   require_once(__DIR__.'/../dbus/class_DBus_Service.php');

   class Robot extends DBus_Service {

      /**
       * Constant key for actions hash,
       * means that rules for that key apply to every user
       */
      const EVERYONE = 'all';

      /**
       * jid => list of pattern-action pairs.
       * $_actions[Robot::EVERYONE] => array(...) rules applied to everyone,
       * $_actions['some@jabber.id'] => array(...) personal rules
       * @var array
       */
      protected $_actions = array();

      /**
       * Blocking pattern-action pairs. Only one for each user
       * jid => array($regex, $callback)
       * @var array
       */
      protected $_blocking = array();

      /**
       * Instance of JAXL client to send messages through
       * @var JAXL object
       */
      protected $_client = NULL;
      static $client = NULL;

      /**
       * This is a jabber id of a person
       * who send last message
       * @var string
       */
      protected $_jid = NULL;

      /**
       * Constructor
       * @param JAXL $client JAXL instance
       */
      public function __construct(\JAXL $client) {
         $this->_client = $client;
         Robot::$client = $client;
      }

      /**
       * Set receiver of my next action(message)
       * @param string $jid Jabber ID
       */
      public function setReceiver($jid) {
         $this->_jid = $jid;
      }

      /**
       * Send a message
       * @param  XMPPStanza $msg message object
       * @return void
       */
      public function send(\XMPPStanza $msg) {
         usleep(10 * mb_strlen($msg->body));
         $this->_client->send($msg);
      }

      /**
       * Store a pattern-callback pair for given jabber id.
       * When robot receives a message from a user with that jabber id,
       * message is matched against all patterns and first callback is taken.
       * @param  string $regex    message pattern to search for
       * @param  callable $callback action to take in a case of successful match
       * @param  string $jid      Jabber ID
       * @return void
       */
      public function hear($regex, $callback, $jid = Robot::EVERYONE) {
         if (!is_null($this->_jid)) $jid = $this->_jid;
         $this->_actions[$jid][$regex] = $callback;
      }

      /**
       * Forget pattern-callback pair for a given user
       * @param  string $regex pattern to erase
       * @param  string $jid   Jabber ID
       * @return void
       */
      public function forget($regex, $jid = Robot::EVERYONE) {
         if (!is_null($this->_jid)) $jid = $this->_jid;
         unset($this->_actions[$jid][$regex]);
      }


      /**
       * Ask for user confirmation that would match certain pattern
       * @param  string $regex    regular expression to match messages against
       * @param  callable $callback action to take
       * @return void
       */
      public function prompt($regex, $callback) {
         if (!is_null($this->_jid)) $jid = $this->_jid;
         else {
            throw new Exception('Jabber ID is not set at that point! \bot\Robot::prompt');
         }
         $this->_blocking[$jid] = array($regex => $callback);
      }

      /**
       * Take incoming message and process it with respect to stored
       * rules (pattern-action pairs)
       * @param  XMPPStanza $message message object
       * @return void
       */
      public function process(\XMPPStanza $message) {

         // Is there blocking rule? If so, check it first.
         if (isset($this->_blocking[$this->_jid])) {
            list($regex, $action) = each($this->_blocking[$this->_jid]);
            $matches = array();
            if (preg_match($regex, $message->body, $matches)) {
               call_user_func($action, $this, $message, $matches);
            }
            unset($this->_blocking[$this->_jid]);
         }

         $rules = $this->getUserRules();

         foreach($rules as $regex => $action) {
            $matches = array();
            if (preg_match($regex, $message->body, $matches)) {
               call_user_func($action, $this, $message, $matches);
               // Once we took an action, return to listening state
               break;
            }
         }
      }

      /**
       * Get current user rules
       * @return array rules to be applied
       */
      private function getUserRules() {
         // Rules for everybody
         $all = isset($this->_actions[Robot::EVERYONE]) ?
                  $this->_actions[Robot::EVERYONE] : array();

         // Rules for current user
         $current = isset($this->_actions[$this->_jid]) ?
                  $this->_actions[$this->_jid]: array();

         return array_merge($all, $current);
      }

      static function test() {
         $msg = new \XMPPMsg(array(
            'to' => 'alexandr@jabber.artfactor',
            'from' => 'redmine@jabber.artfactor'),
         "Wow, DBus worked."
         );
         self::$client->send($msg);
         return "";
      }

   }

}