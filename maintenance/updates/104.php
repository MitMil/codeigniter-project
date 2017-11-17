<?php
    
// Define this version number
$BBWEB_NEW_VERSION = 104;

$queries = array();


// change pannello_status to CHAR(1)
$queries[] = "ALTER TABLE `milking_sessions` CHANGE `pannello_status` `pannello_status` CHAR(1) NOT NULL;";
