<?php
    
// Define this version number
$BBWEB_NEW_VERSION = 111;

$queries = array();

// change milking_rooms fields type
$queries[] = <<<EOF
CREATE  TABLE IF NOT EXISTS `milking_sessions_newanm` (
  `ids_newanm` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `iddevice` TINYINT UNSIGNED NOT NULL ,
  `idpannello` SMALLINT UNSIGNED NOT NULL ,
  `idpacchetto` SMALLINT UNSIGNED NOT NULL ,
  `data_enter` DATETIME NOT NULL ,
  `idanimal` INT UNSIGNED NULL ,
  `number_animal` INT UNSIGNED NULL ,
  `pedometro` VARCHAR(16) NULL ,
  `anomalie` CHAR(2) NOT NULL DEFAULT '00' ,
  `blocco_mungitura` CHAR(2) NOT NULL DEFAULT '00' ,  
  PRIMARY KEY (`ids_newanm`) ,
  INDEX `fk_milking_sessions_newanm_milking_devices1_idx` (`iddevice` ASC) ,
  INDEX `INDEX_idpacchetto` (`idpacchetto` ASC) ,  
  CONSTRAINT `fk_milking_sessions_newanm_milking_devices1`
    FOREIGN KEY (`iddevice` )
    REFERENCES `milking_devices` (`iddevice` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
EOF;
