<?php
    
// Define this version number
$BBWEB_NEW_VERSION = 103;

$queries = array();


/********************************************
 * UPDATE milking_devices
 */
// ALTER FK idmilk -> iddevice
$queries[] = "ALTER TABLE `milking_devices` CHANGE `idimilk` `iddevice` TINYINT(3) UNSIGNED NOT NULL AUTO_INCREMENT;";
// MOVE column positions_available after IP
$queries[] = "ALTER TABLE `milking_devices` DROP `positions_available`;";
// ADD field: firmware_version
$queries[] = "ALTER TABLE `milking_devices` ADD `firmware_version` VARCHAR(45) NOT NULL AFTER `IP`;";

// create milking_rooms
$queries[] =  <<<EOF
CREATE  TABLE IF NOT EXISTS `milking_rooms` (
  `idroom` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `iddevice` TINYINT UNSIGNED NOT NULL ,
  `positions_available` TINYINT NOT NULL ,
  `tipo_sala` VARCHAR(25) NOT NULL ,
  `top_left` TINYINT NOT NULL ,
  `bottom_left` TINYINT NOT NULL ,
  `top_right` TINYINT NOT NULL ,
  `bottom_right` TINYINT NOT NULL ,
  `init` VARCHAR(5) NULL ,
  `offset` TINYINT NOT NULL ,
  PRIMARY KEY (`idroom`) ,
  INDEX `fk_milking rooms_milking_devices1_idx` (`iddevice` ASC) ,
  CONSTRAINT `fk_milking rooms_milking_devices1`
    FOREIGN KEY (`iddevice` )
    REFERENCES `milking_devices` (`iddevice` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
EOF;

// create device
$queries[] = "INSERT INTO `milking_devices` SET iddevice=1, model='iMilkNET', IP=(
                SELECT IF(s.value != '', s.value, s.default_value) AS IP 
                  FROM bb_settings AS s WHERE s.var_code='IMILK_SOCKET_IP' 
              );";

// insert a sample room
$queries[] = "INSERT INTO `milking_rooms` (`idroom`, `iddevice`, `positions_available`, `tipo_sala`, `top_left`, `bottom_left`, `top_right`, `bottom_right`, `init`, `offset`) 
              VALUES
              (1, 1, 0, 'sala_p_intera', 1, 2, 3, 4, 'left', 0);";


/********************************************
 * UPDATE milking_data
 */

// SET DEFAULT idimilk -> iddevice
$queries[] = "UPDATE `milking_data` SET idimilk=1;";
// ALTER FK idmilk -> iddevice
$queries[] = <<<EOF
    ALTER TABLE `milking_data`
        DROP FOREIGN KEY `fk_milking_data_imilk600_devices1`,
        CHANGE COLUMN `idimilk` `iddevice` TINYINT(3) UNSIGNED NOT NULL,
        ADD CONSTRAINT `fk_milking_data_milking_devices` FOREIGN KEY (`iddevice`) REFERENCES `milking_devices` (`iddevice`);
EOF;

/********************************************
 * UPDATE milking_sessions
 */
// ADD field: ts
$queries[] = "ALTER TABLE `milking_sessions` ADD `ts` DOUBLE NOT NULL";
// ADD fields: pannello_status, produzione, milking_time, alarms
$queries[] = "ALTER TABLE `milking_sessions` ADD COLUMN `pannello_status` TINYINT(4) NOT NULL AFTER `data_enter` , ADD COLUMN `produzione` SMALLINT(6) NOT NULL AFTER `pannello_status` , ADD COLUMN `milking_time` SMALLINT(6) NOT NULL AFTER `produzione` , ADD COLUMN `alarms` CHAR(2) NOT NULL AFTER `milking_time`";

// SET DEFAULT idimilk -> iddevice
$queries[] = "UPDATE `milking_sessions` SET idimilk=1;";

// ALTER FK idmilk -> iddevice
$queries[] = <<<EOF
    ALTER TABLE `milking_sessions`
        DROP FOREIGN KEY `fk_milking_sessions_milking_devices1`,
        CHANGE COLUMN `idimilk` `iddevice` TINYINT(3) UNSIGNED NOT NULL,
        ADD CONSTRAINT `fk_milking_sessions_milking_devices` FOREIGN KEY (`iddevice`) REFERENCES `milking_devices` (`iddevice`);
EOF;
