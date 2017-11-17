<?php
    
// Define this version number
$BBWEB_NEW_VERSION = 107;

$queries = array();

// chenged PRIMARY KEY in milking_sessions
$queries[] = "ALTER TABLE `milking_sessions` DROP PRIMARY KEY, ADD PRIMARY KEY( `idpannello`, `iddevice` );";
