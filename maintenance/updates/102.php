<?php
    
// Define this version number
$BBWEB_NEW_VERSION = 102;

$queries = array();

// CREATE new table iMilk_Cmd_Log
$queries[] = <<<EOF
    CREATE TABLE `iMilk_Cmd_Log` (
      `id` smallint(6) NOT NULL AUTO_INCREMENT,
      `direction` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
      `cmd` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
      `cmd_string` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
      `ts` datetime NOT NULL,
      PRIMARY KEY (`id`) ) 
    ENGINE=MEMORY 
    DEFAULT CHARSET=utf8 
    COLLATE=utf8_unicode_ci;
EOF;
