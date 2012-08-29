<?php

namespace Bot {

   class DBus_Service {

      static $dbus = NULL;

      static public function register($name, $interface, $class = __CLASS__) {
         if (!class_exists('\DBus')) {
           trigger_error('You don\t have php-dbus extension \
                          installed. If you don\'t want to use \
                          bot as a dbus service, comment out bot::register call.');
           die();
         }
         $dbus =  new \DBus(\DBus::BUS_SESSION, TRUE);
         $dbus->requestName($name);
         $dbus->registerObject($interface, $name, $class);
         // print $class;
         self::$dbus = $dbus;
      }

   }

}
