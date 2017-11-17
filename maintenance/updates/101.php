<?php
    
// Define this version number
$BBWEB_NEW_VERSION = 101;

$queries = array();

// UPDATE default VELOS_URL and VELOS_URL_REMOTE
$queries[] = "UPDATE bb_settings SET default_value='http://192.168.0.157' WHERE var_code='VELOS_URL'";
$queries[] = "UPDATE bb_settings SET default_value='http://192.168.0.157' WHERE var_code='VELOS_URL_REMOTE'";

// UPDATE default IFC_URL, now use HTTPS to connect to ifc-server
$queries[] = "UPDATE bb_settings SET default_value='https://ifc-server.logic.farm' WHERE var_code='BBWEB_IFC_URL'";

// INSERT new BBWEB_HC_DIM value
$queries[] = <<<EOF
    INSERT INTO `bb_settings` (`var_code`, `env`, `type_values`, `default_value`, `value`, `date_update`) VALUES
    ('BBWEB_HC_DIM', 'BBWEB', 'STRING', 'a:11:{i:0;a:1:{s:4:"SORT";s:2:"10";}i:1;s:1:"0";i:2;s:7:"#00EFFD";i:3;s:3:"VWP";i:4;s:7:"#FFFFFF";i:5;a:1:{s:1:"V";s:3:"120";}i:6;s:7:"#E5E5E5";i:7;a:1:{s:1:"V";s:3:"200";}i:8;s:7:"#9F9F9F";i:9;a:1:{s:1:"V";s:3:"305";}i:10;s:7:"#818181";}', '', NULL)
EOF;

// DELETE old BBWEB_VWP and INSERT new BBWEB_VWP1, BBWEB_VWP2, BBWEB_VWP3
$queries[] = "DELETE FROM bb_settings WHERE var_code='BBWEB_VWP'";
$queries[] = <<<EOF
    INSERT INTO `bb_settings` (`var_code`, `env`, `type_values`, `default_value`, `value`, `date_update`) VALUES
    ('BBWEB_VWP1', 'BBWEB', 'INT', '60', '', NULL),
    ('BBWEB_VWP2', 'BBWEB', 'INT', '45', '', NULL),
    ('BBWEB_VWP3', 'BBWEB', 'INT', '45', '', NULL)
EOF;
