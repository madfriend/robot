<?php

namespace wtf;

class testClass {

	static function echoOne( $a ) {
		return $a;
	}

	static function echoTwo( $a, $b ) {
		return new \DbusSet( $a, $b );
	}

}

$dbus = new \DBus(\DBus::BUS_SESSION, true);
$dbus->requestName('ru.bot.test');
$dbus->registerObject('/ru/bot/test', 'ru.bot.test', 'wtf\testClass' );

do {

	$s = $dbus->waitLoop(1000);

} while ( true );
