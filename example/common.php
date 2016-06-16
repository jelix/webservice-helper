<?php

error_reporting(E_ALL);
ob_start('ob_gzhandler');

require_once __DIR__.'/config.php';

if (!extension_loaded('soap')) {
    die('Soap extension not loaded!');
}

session_start();

/** autoload functie voor PHP5 */
function __autoload($classname)
{
    if (file_exists(__DIR__."/data_objects/$classname.class.php")) {
        include __DIR__."/data_objects/$classname.class.php";
    } elseif (file_exists(__DIR__."/../lib/soap/$classname.class.php")) {
        include __DIR__."/../lib/soap/$classname.class.php";
    } elseif (file_exists(__DIR__."/../lib/$classname.class.php")) {
        include __DIR__."/../lib/$classname.class.php";
    }
}

/** Schrijft de gegeven tekst naar de debug file */
function debug($txt, $file = 'debug.log')
{
    error_log($txt."\n", 3, $file);
}

/** Schrijft het gegeven object weg in de debug log */
function debugObject($txt, $obj)
{
    debug($txt."\n".var_export($obj, true)."\n");
}
