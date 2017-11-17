<?php
    
// Define this version number
$BBWEB_NEW_VERSION = 108;

$queries = array();

// new table devices_firmware
$queries[] = <<<EOF
CREATE  TABLE IF NOT EXISTS `devices_firmware` (
  `iddf` INT NOT NULL AUTO_INCREMENT ,
  `iddevice` TINYINT UNSIGNED NOT NULL ,
  `filename` VARCHAR(50) NOT NULL ,
  `firmware_tipo` VARCHAR(30) NOT NULL ,
  `filesize` INT UNSIGNED NOT NULL ,
  `num_lines` SMALLINT UNSIGNED NOT NULL DEFAULT '0' ,
  `num_lines_sent` SMALLINT UNSIGNED NOT NULL DEFAULT '0' ,
  `error` VARCHAR(50) NOT NULL ,
  `checksum` CHAR(8) NOT NULL ,
  `in_progress` TINYINT NOT NULL DEFAULT '0' ,
  `data_upload` DATETIME NOT NULL ,
  `data_completed` DATETIME NULL ,
  PRIMARY KEY (`iddf`) ,
  INDEX `fk_devices_firmware_milking_devices1_idx` (`iddevice` ASC) ,
  CONSTRAINT `fk_devices_firmware_milking_devices1`
    FOREIGN KEY (`iddevice` )
    REFERENCES `milking_devices` (`iddevice` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
EOF;
