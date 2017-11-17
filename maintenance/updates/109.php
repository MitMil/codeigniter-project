<?php
    
// Define this version number
$BBWEB_NEW_VERSION = 109;

$queries = array();

// change dati_grafico field type
$queries[] = "ALTER TABLE `milking_data` CHANGE `dati_grafico` `dati_grafico` VARCHAR(1024) NOT NULL;";
$queries[] = "ALTER TABLE `milking_sessions` CHANGE `dati_grafico` `dati_grafico` VARCHAR(1024) NOT NULL;";

// add new fields to milking_sessions: alarms_washing, informazioni_washing
$queries[] = <<<EOF
ALTER TABLE `milking_sessions` 
    ADD `alarms_washing` CHAR(2) NOT NULL AFTER `alarms`, 
    ADD `informazioni_washing` TINYINT NOT NULL AFTER `alarms_washing`;
EOF;

// add new table milking_washing
$queries[] = <<<EOF
CREATE  TABLE IF NOT EXISTS `milking_washing` (
  `idfinlav` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `iddevice` TINYINT UNSIGNED NOT NULL ,
  `idpannello` SMALLINT UNSIGNED NOT NULL ,
  `kg` SMALLINT UNSIGNED NOT NULL ,
  `tempo` SMALLINT UNSIGNED NOT NULL ,
  `flusso_medio` SMALLINT UNSIGNED NOT NULL ,
  `flusso_massimo` SMALLINT UNSIGNED NOT NULL ,
  `temperatura_media` SMALLINT UNSIGNED NOT NULL ,
  `temperatura_massima` SMALLINT UNSIGNED NOT NULL ,
  `conducibilita_media` SMALLINT UNSIGNED NOT NULL ,
  `conducibilita_massima` SMALLINT UNSIGNED NOT NULL ,
  `informazioni` TINYINT UNSIGNED NOT NULL ,
  `allarmi` CHAR(2) NOT NULL ,
  `data_finlav` DATETIME NOT NULL ,
  PRIMARY KEY (`idfinlav`) ,
  INDEX `fk_milking_washing_milking_devices1_idx` (`iddevice` ASC) ,
  CONSTRAINT `fk_milking_washing_milking_devices1`
    FOREIGN KEY (`iddevice` )
    REFERENCES `milking_devices` (`iddevice` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
EOF;
