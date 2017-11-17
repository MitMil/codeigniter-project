<?php
    
// Define this version number
$BBWEB_NEW_VERSION = 105;

$queries = array();

// change fields produzione and milking_time TO UNSIGNED
$queries[] = "ALTER TABLE `milking_sessions` CHANGE `produzione` `produzione` SMALLINT(6) UNSIGNED NOT NULL, CHANGE `milking_time` `milking_time` SMALLINT(6) UNSIGNED NOT NULL;";

// add fields to milking_sessions
$queries[] = <<<EOF
ALTER TABLE `milking_sessions` 
    ADD `flusso_attuale` SMALLINT UNSIGNED NOT NULL AFTER `alarms`, 
    ADD `flusso_massimo` SMALLINT UNSIGNED NOT NULL AFTER `flusso_attuale`, 
    ADD `temperatura_attuale` SMALLINT UNSIGNED NOT NULL AFTER `flusso_massimo`, 
    ADD `temperatura_massima` SMALLINT UNSIGNED NOT NULL AFTER `temperatura_attuale`, 
    ADD `conducibilita_attuale` SMALLINT UNSIGNED NOT NULL AFTER `temperatura_massima`, 
    ADD `conducibilita_massima` SMALLINT UNSIGNED NOT NULL AFTER `conducibilita_attuale`, 
    ADD `dati_grafico` VARCHAR(55) NOT NULL AFTER `conducibilita_massima`;
EOF;

// add fields to milking_data
$queries[] = <<<EOF
ALTER TABLE `milking_data` 
    ADD `produzione_2minuti` SMALLINT UNSIGNED NOT NULL AFTER `allarmi`, 
    ADD `tempo_flusso_basso` SMALLINT UNSIGNED NOT NULL AFTER `produzione_2minuti`, 
    ADD `dati_grafico` VARCHAR(55) NOT NULL AFTER `tempo_flusso_basso`;
EOF;
