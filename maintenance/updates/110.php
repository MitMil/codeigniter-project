<?php
    
// Define this version number
$BBWEB_NEW_VERSION = 110;

$queries = array();

// change milking_rooms fields type
$queries[] = <<<EOF
    ALTER TABLE `milking_rooms` 
        CHANGE `top_left` `top_left` TINYINT(4) UNSIGNED NOT NULL, 
        CHANGE `bottom_left` `bottom_left` TINYINT(4) UNSIGNED NOT NULL, 
        CHANGE `top_right` `top_right` TINYINT(4) UNSIGNED NOT NULL, 
        CHANGE `bottom_right` `bottom_right` TINYINT(4) UNSIGNED NOT NULL;
EOF;
