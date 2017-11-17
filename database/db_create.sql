SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `colors`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `colors` (
  `idcolor` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `description` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`idcolor`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `breeds`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `breeds` (
  `idbreed` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `idcolor` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`idbreed`) ,
  INDEX `fk_breed_color1_idx` (`idcolor` ASC) ,
  CONSTRAINT `fk_breed_color1`
    FOREIGN KEY (`idcolor` )
    REFERENCES `colors` (`idcolor` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `locations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `locations` (
  `idlocation` INT NOT NULL ,
  `num` INT NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `capacity` INT NOT NULL COMMENT '	' ,
  `color` INT NOT NULL ,
  `idlocation_parent` INT NOT NULL ,
  PRIMARY KEY (`idlocation`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `users` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(50) NOT NULL ,
  `password` VARCHAR(32) NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `profile` VARCHAR(255) NULL DEFAULT 'fa fa-user' ,
  `address` VARCHAR(255) NULL DEFAULT NULL ,
  `cap` VARCHAR(5) NULL DEFAULT NULL ,
  `city` VARCHAR(50) NULL DEFAULT NULL ,
  `state` VARCHAR(50) NULL DEFAULT NULL ,
  `phone` VARCHAR(255) NULL DEFAULT NULL ,
  `language` VARCHAR(255) NULL DEFAULT NULL ,
  `date_format` VARCHAR(255) NULL DEFAULT NULL ,
  `time_format` VARCHAR(255) NULL DEFAULT NULL ,
  `calender_type` VARCHAR(255) NULL DEFAULT NULL ,
  `insert_datetime` DATETIME NOT NULL ,
  `update_datetime` DATETIME NOT NULL ,
  PRIMARY KEY (`user_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `animals`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `animals` (
  `idanimal` INT UNSIGNED NOT NULL ,
  `idbreed` INT UNSIGNED NULL ,
  `idowner` INT(11) NULL ,
  `idcolor` INT UNSIGNED NULL ,
  `idlocation` INT NULL ,
  `idhomelocation` INT NULL ,
  `type` TINYINT NOT NULL DEFAULT 1 ,
  `number_animal` VARCHAR(45) NOT NULL ,
  `velos_production_status` VARCHAR(30) NOT NULL ,
  `velos_reproduction_status` VARCHAR(30) NOT NULL ,
  `sex` ENUM('M','F') NOT NULL DEFAULT 'F' ,
  `name` VARCHAR(45) NOT NULL ,
  `life_number` VARCHAR(30) NOT NULL ,
  `condition_score` VARCHAR(20) NOT NULL ,
  `last_location_update` DATETIME NULL DEFAULT NULL ,
  `matricola_madre` VARCHAR(30) NOT NULL ,
  `matricola_padre` VARCHAR(30) NOT NULL ,
  `lactation` TINYINT NOT NULL DEFAULT 0 ,
  `lactation_days` INT NOT NULL ,
  `is_present` ENUM('Y','N') NOT NULL DEFAULT 'Y' ,
  `departure_date` DATE NULL DEFAULT NULL ,
  `notes` VARCHAR(1024) NOT NULL ,
  `lastupdate_animal` DATETIME NOT NULL ,
  `lastupdate_activity` DATETIME NOT NULL ,
  `lastupdate_attention` DATETIME NOT NULL ,
  PRIMARY KEY (`idanimal`) ,
  INDEX `fk_animals_breeds1_idx` (`idbreed` ASC) ,
  INDEX `fk_animals_color1_idx` (`idcolor` ASC) ,
  INDEX `fk_animals_locations1_idx` (`idlocation` ASC) ,
  INDEX `fk_animals_locations2_idx` (`idhomelocation` ASC) ,
  INDEX `fk_animals_users1_idx` (`idowner` ASC) ,
  CONSTRAINT `fk_animals_breed1`
    FOREIGN KEY (`idbreed` )
    REFERENCES `breeds` (`idbreed` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_animals_cm1`
    FOREIGN KEY (`idcolor` )
    REFERENCES `colors` (`idcolor` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_animals_locations1`
    FOREIGN KEY (`idlocation` )
    REFERENCES `locations` (`idlocation` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_animals_locations2`
    FOREIGN KEY (`idhomelocation` )
    REFERENCES `locations` (`idlocation` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_animals_users1`
    FOREIGN KEY (`idowner` )
    REFERENCES `users` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `groups` (
  `idgroup` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `num` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `idhomelocation` INT NULL ,
  PRIMARY KEY (`idgroup`) ,
  INDEX `fk_groups_groups_types_idx` (`idgroup` ASC) ,
  INDEX `fk_groups_locations1_idx` (`idhomelocation` ASC) ,
  CONSTRAINT `fk_groups_locations1`
    FOREIGN KEY (`idhomelocation` )
    REFERENCES `locations` (`idlocation` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `modules`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `modules` (
  `module_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `module_name` VARCHAR(255) NOT NULL ,
  `module_url` VARCHAR(255) NOT NULL ,
  `parent` INT(11) NOT NULL ,
  `insert_datetime` DATETIME NOT NULL ,
  `update_datetime` DATETIME NOT NULL ,
  PRIMARY KEY (`module_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `roles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `roles` (
  `role_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `parent` INT(11) NULL DEFAULT NULL ,
  `insert_datetime` DATETIME NOT NULL ,
  `update_datetime` DATETIME NOT NULL ,
  PRIMARY KEY (`role_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `module_assign`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `module_assign` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `role_id` INT(11) NULL DEFAULT NULL ,
  `user_id` INT(11) NULL DEFAULT NULL COMMENT 'Assign module to particular user' ,
  `module_id` INT(11) NOT NULL ,
  `insert_datetime` DATETIME NOT NULL ,
  `update_datetime` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_module_assign_1_idx` (`user_id` ASC) ,
  INDEX `fk_module_assign_2_idx` (`module_id` ASC) ,
  INDEX `fk_module_assign_3_idx` (`role_id` ASC) ,
  CONSTRAINT `fk_module_assign_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_module_assign_2`
    FOREIGN KEY (`module_id` )
    REFERENCES `modules` (`module_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_module_assign_3`
    FOREIGN KEY (`role_id` )
    REFERENCES `roles` (`role_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `responders`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `responders` (
  `idresponder` VARCHAR(16) NOT NULL ,
  `idanimal` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`idresponder`) ,
  INDEX `fk_responders_animals1_idx` (`idanimal` ASC) ,
  CONSTRAINT `fk_responders_animals1`
    FOREIGN KEY (`idanimal` )
    REFERENCES `animals` (`idanimal` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `conditions_history`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `conditions_history` (
  `idcondition` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `idanimal` INT UNSIGNED NOT NULL ,
  `date_updated` DATETIME NOT NULL ,
  `level` TINYINT NOT NULL ,
  PRIMARY KEY (`idcondition`) ,
  INDEX `fk_conditions_animals1_idx` (`idanimal` ASC) ,
  CONSTRAINT `fk_conditions_animals1`
    FOREIGN KEY (`idanimal` )
    REFERENCES `animals` (`idanimal` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `locomotions_history`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `locomotions_history` (
  `idlocomotion` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `idanimal` INT UNSIGNED NOT NULL ,
  `date_updated` DATETIME NOT NULL ,
  `level` TINYINT NOT NULL ,
  PRIMARY KEY (`idlocomotion`) ,
  INDEX `fk_locomotions_history_animals1_idx` (`idanimal` ASC) ,
  CONSTRAINT `fk_locomotions_history_animals1`
    FOREIGN KEY (`idanimal` )
    REFERENCES `animals` (`idanimal` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `animals_groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `animals_groups` (
  `idanimal` INT UNSIGNED NOT NULL ,
  `idgroup` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`idanimal`, `idgroup`) ,
  INDEX `fk_groups_has_animals_animals1_idx` (`idanimal` ASC) ,
  INDEX `fk_groups_has_animals_groups1_idx` (`idgroup` ASC) ,
  CONSTRAINT `fk_groups_has_animals_groups1`
    FOREIGN KEY (`idgroup` )
    REFERENCES `groups` (`idgroup` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_groups_has_animals_animals1`
    FOREIGN KEY (`idanimal` )
    REFERENCES `animals` (`idanimal` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `activities`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `activities` (
  `idactivity` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `idanimal` INT UNSIGNED NOT NULL ,
  `type` VARCHAR(45) NOT NULL ,
  `period_datetime` DATETIME NOT NULL ,
  `period_number` TINYINT(3) UNSIGNED NOT NULL ,
  `counter_value` INT NOT NULL ,
  `x_factor` DECIMAL(12,6) NOT NULL ,
  `attention` TINYINT(1) NOT NULL ,
  `x_factor_group_correction` DECIMAL(12,6) NOT NULL ,
  `idgroup` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`idactivity`) ,
  INDEX `fk_activities_animals1_idx` (`idanimal` ASC) ,
  INDEX `IDX_activities_period_datetime` (`period_datetime` ASC) ,
  CONSTRAINT `fk_activities_animals1`
    FOREIGN KEY (`idanimal` )
    REFERENCES `animals` (`idanimal` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iMilk_Queue_TX`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `iMilk_Queue_TX` (
  `idpacchetto` SMALLINT UNSIGNED NOT NULL ,
  `command` VARCHAR(7) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `fields` VARCHAR(1024) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL ,
  `stato` VARCHAR(10) NOT NULL DEFAULT 'NEW' ,
  `ts` DOUBLE NOT NULL ,
  `n_sent` TINYINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`idpacchetto`) ,
  INDEX `INDEX_data_insert` (`ts` ASC) )
ENGINE = MEMORY
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'iMilk Queue TX (commands to SEND)';


-- -----------------------------------------------------
-- Table `label_data_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `label_data_type` (
  `idlabel_data_type` TINYINT(1) UNSIGNED NOT NULL ,
  `velos_code` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`idlabel_data_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `animals_label_values`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `animals_label_values` (
  `idanimals_label_values` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `idanimal` INT UNSIGNED NOT NULL ,
  `idlabel_data_type` TINYINT(1) UNSIGNED NOT NULL ,
  `velos_id` INT UNSIGNED NOT NULL ,
  `timestamp` DATETIME NOT NULL ,
  `value` INT UNSIGNED NOT NULL ,
  INDEX `fk_animals_has_label_data_type_label_data_type1_idx` (`idlabel_data_type` ASC) ,
  INDEX `fk_animals_has_label_data_type_animals1_idx` (`idanimal` ASC) ,
  INDEX `IDX_animals_labels_timestamp` (`timestamp` ASC) ,
  PRIMARY KEY (`idanimals_label_values`) ,
  CONSTRAINT `fk_animals_has_label_data_type_animals1`
    FOREIGN KEY (`idanimal` )
    REFERENCES `animals` (`idanimal` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_animals_has_label_data_type_label_data_type1`
    FOREIGN KEY (`idlabel_data_type` )
    REFERENCES `label_data_type` (`idlabel_data_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `animals_label_velos_values`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `animals_label_velos_values` (
  `idalvv` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `velos_id` INT NOT NULL ,
  `idanimal` INT UNSIGNED NOT NULL ,
  `idlabel_data_type` TINYINT(1) UNSIGNED NOT NULL ,
  `timestamp` DATETIME NULL ,
  `quarterindex` INT UNSIGNED NOT NULL ,
  `quarterdata` VARCHAR(512) NULL ,
  `n_unsentquarters` INT UNSIGNED NOT NULL ,
  `s_strength` INT NOT NULL ,
  PRIMARY KEY (`idalvv`) ,
  INDEX `fk_animals_label_velos_values_animals1_idx` (`idanimal` ASC) ,
  INDEX `fk_animals_label_velos_values_label_data_type1_idx` (`idlabel_data_type` ASC) ,
  INDEX `IDX_label_values_velos_timestamp` (`timestamp` ASC) ,
  CONSTRAINT `fk_animals_label_velos_values_animals1`
    FOREIGN KEY (`idanimal` )
    REFERENCES `animals` (`idanimal` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_animals_label_velos_values_label_data_type1`
    FOREIGN KEY (`idlabel_data_type` )
    REFERENCES `label_data_type` (`idlabel_data_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `events`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `events` (
  `idevent` INT NOT NULL ,
  `description` VARCHAR(45) NOT NULL ,
  `velos_class_name` VARCHAR(25) NOT NULL ,
  PRIMARY KEY (`idevent`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `animals_events`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `animals_events` (
  `idanimals_events` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `velos_idevent` INT UNSIGNED NOT NULL ,
  `idanimal` INT UNSIGNED NOT NULL ,
  `idevent` INT NOT NULL ,
  `timestamp` DATETIME NOT NULL ,
  `memo` VARCHAR(100) NULL ,
  `performer` VARCHAR(100) NULL ,
  `info` VARCHAR(100) NULL ,
  INDEX `fk_animals_events_animals1_idx` (`idanimal` ASC) ,
  INDEX `fk_animals_events_events1_idx` (`idevent` ASC) ,
  PRIMARY KEY (`idanimals_events`) ,
  INDEX `IDX_Velos_Idevent` (`velos_idevent` ASC) ,
  CONSTRAINT `fk_animals_events_animals1`
    FOREIGN KEY (`idanimal` )
    REFERENCES `animals` (`idanimal` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_animals_events_events1`
    FOREIGN KEY (`idevent` )
    REFERENCES `events` (`idevent` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `user_roles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `user_roles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `role_id` INT(11) NOT NULL ,
  `user_id` INT(11) NOT NULL ,
  `insert_datetime` DATETIME NOT NULL ,
  `update_datetime` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_user_roles_1_idx` (`role_id` ASC) ,
  INDEX `fk_user_roles_2_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_user_roles_1`
    FOREIGN KEY (`role_id` )
    REFERENCES `roles` (`role_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_roles_2`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `animals_label_periods`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `animals_label_periods` (
  `idanimals_labels_periods` INT NOT NULL ,
  `idanimal` INT UNSIGNED NOT NULL ,
  `data` DATE NOT NULL ,
  `period` TINYINT NOT NULL ,
  `dim` INT NOT NULL ,
  `production_status` INT NOT NULL ,
  `legactivity_count` INT NOT NULL ,
  `avg_legactivity_count` INT NOT NULL ,
  `lying2standing_count` INT NOT NULL ,
  `avg_lying2standing_count` INT NOT NULL ,
  `lying_time` INT NOT NULL ,
  `avg_lying_time` INT NOT NULL ,
  `walking_time` INT NOT NULL ,
  `avg_walking_time` INT NOT NULL ,
  `standing_time` INT NOT NULL ,
  `avg_standing_time` INT NOT NULL ,
  PRIMARY KEY (`idanimals_labels_periods`) ,
  INDEX `fk_animals_labels_periods_animals1_idx` (`idanimal` ASC) ,
  CONSTRAINT `fk_animals_labels_periods_animals1`
    FOREIGN KEY (`idanimal` )
    REFERENCES `animals` (`idanimal` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bb_settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `bb_settings` (
  `var_code` VARCHAR(30) NOT NULL ,
  `env` VARCHAR(45) NOT NULL ,
  `type_values` ENUM('INT','STRING','URL','PATH') NOT NULL DEFAULT 'STRING' ,
  `default_value` VARCHAR(255) NOT NULL ,
  `value` VARCHAR(255) NOT NULL ,
  `date_update` DATETIME NULL ,
  PRIMARY KEY (`var_code`) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `animals_attentions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `animals_attentions` (
  `idvelos_attention` INT UNSIGNED NOT NULL ,
  `idanimal` INT UNSIGNED NOT NULL ,
  `type` VARCHAR(90) NOT NULL ,
  `ts` DATETIME NOT NULL ,
  `ts_expire` DATETIME NULL ,
  `checked` TINYINT NOT NULL ,
  PRIMARY KEY (`idvelos_attention`) ,
  INDEX `fk_animal_attentions_animals1_idx` (`idanimal` ASC) ,
  CONSTRAINT `fk_animal_attentions_animals1`
    FOREIGN KEY (`idanimal` )
    REFERENCES `animals` (`idanimal` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `milking_devices`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `milking_devices` (
  `iddevice` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `model` VARCHAR(45) NOT NULL DEFAULT 'iMilk600' ,
  `name` VARCHAR(45) NOT NULL ,
  `IP` VARCHAR(45) NOT NULL ,
  `firmware_version` VARCHAR(45) NULL ,
  PRIMARY KEY (`iddevice`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `milking_sessions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `milking_sessions` (
  `idpannello` SMALLINT NOT NULL ,
  `iddevice` TINYINT UNSIGNED NOT NULL ,
  `idpacchetto` SMALLINT UNSIGNED NOT NULL ,
  `idanimal` INT UNSIGNED NULL ,
  `anomalie` CHAR(2) NOT NULL ,
  `informazioni` CHAR(2) NOT NULL ,
  `attenzioni` CHAR(8) NOT NULL ,
  `blocco_mungitura` CHAR(2) NOT NULL ,
  `number_animal` INT UNSIGNED NULL ,
  `pedometro` CHAR(16) NULL ,
  `data_enter` DATETIME NOT NULL ,
  `pannello_status` CHAR(1) NOT NULL ,
  `produzione` SMALLINT UNSIGNED NOT NULL ,
  `milking_time` SMALLINT UNSIGNED NOT NULL ,
  `alarms` CHAR(2) NOT NULL ,
  `alarms_washing` CHAR(2) NOT NULL ,
  `informazioni_washing` TINYINT NOT NULL ,
  `flusso_attuale` SMALLINT UNSIGNED NOT NULL ,
  `flusso_massimo` SMALLINT UNSIGNED NOT NULL ,
  `temperatura_attuale` SMALLINT UNSIGNED NOT NULL ,
  `temperatura_massima` SMALLINT UNSIGNED NOT NULL ,
  `conducibilita_attuale` SMALLINT UNSIGNED NOT NULL ,
  `conducibilita_massima` SMALLINT UNSIGNED NOT NULL ,
  `dati_grafico` VARCHAR(1024) NOT NULL ,
  `ts` DOUBLE NOT NULL ,
  PRIMARY KEY (`idpannello`, `iddevice`) ,
  INDEX `INDEX_idpacchetto` (`idpacchetto` ASC) ,
  INDEX `fk_milking_sessions_milking_devices1_idx` (`iddevice` ASC) ,
  INDEX `INDEX_pedometro` (`pedometro` ASC) ,
  CONSTRAINT `fk_milking_sessions_milking_devices`
    FOREIGN KEY (`iddevice` )
    REFERENCES `milking_devices` (`iddevice` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `milking_data`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `milking_data` (
  `idfindat` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `iddevice` TINYINT UNSIGNED NOT NULL ,
  `idpannello` SMALLINT UNSIGNED NOT NULL ,
  `idanimal` INT UNSIGNED NULL ,
  `number_animal` INT NOT NULL ,
  `progressivo_record` SMALLINT UNSIGNED NOT NULL ,
  `produzione` SMALLINT UNSIGNED NOT NULL ,
  `tempo_attesa` SMALLINT UNSIGNED NOT NULL ,
  `tempo_mungitura` SMALLINT UNSIGNED NOT NULL ,
  `flusso_medio` SMALLINT UNSIGNED NOT NULL ,
  `flusso_massimo` SMALLINT UNSIGNED NOT NULL ,
  `flusso_15` SMALLINT UNSIGNED NOT NULL ,
  `flusso_30` SMALLINT UNSIGNED NOT NULL ,
  `flusso_60` SMALLINT UNSIGNED NOT NULL ,
  `flusso_120` SMALLINT UNSIGNED NOT NULL ,
  `temperatura_media` SMALLINT UNSIGNED NOT NULL ,
  `temperatura_massima` SMALLINT UNSIGNED NOT NULL ,
  `conducibilita_media` SMALLINT UNSIGNED NOT NULL ,
  `conducibilita_massima` SMALLINT UNSIGNED NOT NULL ,
  `manual_values` VARCHAR(2) NOT NULL ,
  `allarmi` VARCHAR(2) NOT NULL ,
  `produzione_2minuti` SMALLINT UNSIGNED NOT NULL ,
  `tempo_flusso_basso` SMALLINT UNSIGNED NOT NULL ,
  `dati_grafico` VARCHAR(1024) NOT NULL ,
  `data_enter` DATETIME NOT NULL ,
  `data_start` DATETIME NOT NULL ,
  `data_end` DATETIME NOT NULL ,
  `data_findat` DATETIME NOT NULL ,
  PRIMARY KEY (`idfindat`) ,
  INDEX `fk_milking_data_imilk600_devices1_idx` (`iddevice` ASC) ,
  CONSTRAINT `fk_milking_data_milking_devices`
    FOREIGN KEY (`iddevice` )
    REFERENCES `milking_devices` (`iddevice` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `iMilk_Cmd_Log`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `iMilk_Cmd_Log` (
  `id` SMALLINT NOT NULL AUTO_INCREMENT ,
  `direction` VARCHAR(2) NOT NULL ,
  `cmd` VARCHAR(10) NOT NULL ,
  `cmd_string` VARCHAR(512) NOT NULL ,
  `ts` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MEMORY;


-- -----------------------------------------------------
-- Table `milking_rooms`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `milking_rooms` (
  `idroom` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `iddevice` TINYINT UNSIGNED NOT NULL ,
  `positions_available` TINYINT NOT NULL ,
  `tipo_sala` VARCHAR(25) NOT NULL ,
  `top_left` TINYINT UNSIGNED NOT NULL ,
  `bottom_left` TINYINT UNSIGNED NOT NULL ,
  `top_right` TINYINT UNSIGNED NOT NULL ,
  `bottom_right` TINYINT UNSIGNED NOT NULL ,
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


-- -----------------------------------------------------
-- Table `devices_firmware`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `devices_firmware` (
  `iddf` INT NOT NULL AUTO_INCREMENT ,
  `iddevice` TINYINT UNSIGNED NOT NULL ,
  `filename` VARCHAR(50) NOT NULL ,
  `firmware_tipo` VARCHAR(30) NOT NULL ,
  `filesize` INT UNSIGNED NOT NULL ,
  `num_lines` SMALLINT UNSIGNED NOT NULL DEFAULT '0' ,
  `num_lines_sent` SMALLINT UNSIGNED NOT NULL DEFAULT '0' ,
  `error` VARCHAR(255) NOT NULL DEFAULT '0' ,
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


-- -----------------------------------------------------
-- Table `milking_washing`
-- -----------------------------------------------------
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


-- -----------------------------------------------------
-- Table `milking_sessions_newanm`
-- -----------------------------------------------------
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


-- -----------------------------------------------------
-- Table `languages`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `languages` (
  `idlang` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `code` CHAR(6) NOT NULL ,
  `lang` VARCHAR(32) NOT NULL ,
  `filename` VARCHAR(64) NOT NULL ,
  `is_default` TINYINT NOT NULL ,
  PRIMARY KEY (`idlang`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `informations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `informations` (
  `idinfo` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `descrizione` VARCHAR(50) NOT NULL ,
  `class_name` VARCHAR(100) NOT NULL ,
  `udm` VARCHAR(12) NOT NULL ,
  PRIMARY KEY (`idinfo`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `users_informations`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `users_informations` (
  `user_id` INT(11) NOT NULL ,
  `idinfo` INT UNSIGNED NOT NULL ,
  `custom_value_1` VARCHAR(255) NULL ,
  `custom_value_2` VARCHAR(255) NULL ,
  PRIMARY KEY (`user_id`, `idinfo`) ,
  INDEX `fk_users_has_informations_informations1_idx` (`idinfo` ASC) ,
  INDEX `fk_users_has_informations_users1_idx` (`user_id` ASC) ,
  CONSTRAINT `fk_users_has_informations_users1`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_informations_informations1`
    FOREIGN KEY (`idinfo` )
    REFERENCES `informations` (`idinfo` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `widgets`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `widgets` (
  `idwidgets` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `descrizione` VARCHAR(50) NOT NULL ,
  `class_name` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`idwidgets`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `users_has_widgets`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users_has_widgets` (
  `user_id` int(11) NOT NULL,
  `idwidgets` int(10) unsigned NOT NULL,
  `custom_value_1` varchar(255) NOT NULL,
  `custom_value_2` varchar(255) NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  FOREIGN KEY (`idwidgets`) REFERENCES `widgets` (`idwidgets`)
);
-- -----------------------------------------------------
-- Table `messages`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `messages` (
  `idmessage` INT unsigned NOT NULL ,
  `role_id` INT(11) NOT NULL ,
  `category` VARCHAR(25) NOT NULL ,
  `descrizione` VARCHAR(50) NOT NULL ,
  `type` VARCHAR(10) NOT NULL ,
  PRIMARY KEY (`idmessage`) ,
  INDEX `fk_messages_roles1_idx` (`role_id` ASC) ,
  CONSTRAINT `fk_messages_roles1`
    FOREIGN KEY (`role_id` )
    REFERENCES `roles` (`role_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `messages_log`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `messages_log` (
  `idmsglog` INT unsigned NOT NULL AUTO_INCREMENT ,
  `idmessage` INT unsigned NOT NULL ,
  `idanimal` INT UNSIGNED NULL ,
  `ts` DATETIME NOT NULL ,
  `checked` TINYINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`idmsglog`) ,
  INDEX `fk_messages_log_messages1_idx` (`idmessage` ASC) ,
  INDEX `fk_messages_log_animals1_idx` (`idanimal` ASC) ,
  CONSTRAINT `fk_messages_log_messages1`
    FOREIGN KEY (`idmessage` )
    REFERENCES `messages` (`idmessage` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_messages_log_animals1`
    FOREIGN KEY (`idanimal` )
    REFERENCES `animals` (`idanimal` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `animals_feedings`
-- -----------------------------------------------------
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
  INDEX `fk_animals_feedings_animals1_idx` (`idanimal` ASC) ,
  CONSTRAINT `fk_animals_feedings_animals1`
    FOREIGN KEY (`idanimal` )
    REFERENCES `animals` (`idanimal` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `language_strings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `language_strings` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `idlang` INT UNSIGNED NOT NULL ,
  `msgid` TEXT NOT NULL ,
  `msgstr` TEXT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_language_strings_languages1_idx` (`idlang` ASC) ,
  CONSTRAINT `fk_language_strings_languages1`
    FOREIGN KEY (`idlang` )
    REFERENCES `languages` (`idlang` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bb_system_variables`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `bb_system_variables` (
  `var_code` VARCHAR(30) NOT NULL ,
  `value` VARCHAR(255) NOT NULL ,
  `ts_insert` DATETIME NULL ,
  `ts_update` DATETIME NULL ,
  PRIMARY KEY (`var_code`) )
ENGINE = MEMORY;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
