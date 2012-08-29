<?php

namespace Bot {

   class DBus_Service {

      static $dbus = NULL;

      static public function register($name, $interface, $class = __CLASS__) {
         $dbus =  new \DBus(\DBus::BUS_SESSION, TRUE);
         $dbus->requestName($name);
         $dbus->registerObject($interface, $name, $class);
         // print $class;
         self::$dbus = $dbus;
      }

   }

}