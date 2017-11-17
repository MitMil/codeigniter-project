<?php

// Define this version number
$BBWEB_NEW_VERSION = 115;

$queries = array();
$queries[] = "SET FOREIGN_KEY_CHECKS=0;";

// Only remove report option for other user except SUI.
$queries[] = "DELETE FROM module_assign where role_id IN(2,3,4) AND module_id=2";

$queries[] = "ALTER TABLE `animals` ADD `lastupdate_attention` DATETIME NOT NULL AFTER `lastupdate_activity`;";
$queries[] = "ALTER TABLE `animals_attentions` CHANGE `ts_expire` `ts_expire` DATETIME NULL;";

$queries[] = <<<EOF
CREATE  TABLE IF NOT EXISTS `animals_feedings` (
`idfeeding` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `idanimal` INT UNSIGNED NOT NULL ,
  `feed_type_id` INT NOT NULL ,
  `feeding_location_category` VARCHAR(45) NOT NULL ,
  `cycle_balance` INT NOT NULL ,
  `period_balance` INT NOT NULL ,
  `feeding_speed` INT NOT NULL ,
  `phase` INT NOT NULL ,
  `current_period_start_time` DATETIME NULL ,
  `last_calculation_time` DATETIME NULL ,
  `last_transaction_time` DATETIME NULL ,
  `unreported_amount` INT NOT NULL ,
  PRIMARY KEY (`idfeeding`) ,
  INDEX `fk_feeding_animals1_idx` (`idanimal` ASC) ,
  CONSTRAINT `fk_feeding_animals1`
    FOREIGN KEY (`idanimal` )
    REFERENCES `animals` (`idanimal` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
EOF;

$queries[] = "SET FOREIGN_KEY_CHECKS=1;";
