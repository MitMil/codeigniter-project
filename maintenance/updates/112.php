<?php
    
// Define this version number
$BBWEB_NEW_VERSION = 112;

$queries = array();

// change milking_rooms fields type
$queries[] = "UPDATE `bb_settings` SET `default_value` = 'https://ifc-server.myfarm.cloud' WHERE `var_code` = 'BBWEB_IFC_URL';";
