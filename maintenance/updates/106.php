<?php
    
// Define this version number
$BBWEB_NEW_VERSION = 106;

$queries = array();

// DROP field status
$queries[] = "ALTER TABLE `milking_sessions` DROP `status`;";

// add fields to milking_sessions
$queries[] = <<<EOF
ALTER TABLE `milking_sessions` 
    ADD `anomalie` CHAR(2) NOT NULL AFTER `idanimal`, 
    ADD `informazioni` CHAR(2) NOT NULL AFTER `anomalie`, 
    ADD `attenzioni` CHAR(8) NOT NULL AFTER `informazioni`, 
    ADD `blocco_mungitura` CHAR(2) NOT NULL AFTER `attenzioni`;
EOF;

// change Primary Key in milking_sessions
$queries[] = "ALTER TABLE `milking_sessions` DROP `idms`;";
$queries[] = "ALTER TABLE `milking_sessions` CHANGE `idpannello` `idpannello` SMALLINT(6) UNSIGNED NOT NULL;";
$queries[] = "ALTER TABLE `milking_sessions` ADD PRIMARY KEY(`idpannello`);";