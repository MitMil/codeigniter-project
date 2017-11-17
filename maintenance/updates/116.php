<?php 

// Define this version number
$BBWEB_NEW_VERSION = 116;

$queries = array();
$queries[] = "SET FOREIGN_KEY_CHECKS=0;";

// new table `informations`
$queries[] = <<<EOF
DROP TABLE IF EXISTS `informations`;
CREATE TABLE `informations` (
  `idinfo` int(11) NOT NULL AUTO_INCREMENT,
  `descrizione` varchar(50) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `udm` varchar(12) NOT NULL,
  PRIMARY KEY (`idinfo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOF;

//Dumping data for `informations`
$queries[] = <<<EOF
INSERT INTO `informations` (`idinfo`, `descrizione`, `class_name`, `udm`) VALUES
(1,	'Total production',	'milking',	'Kg'),
(2,	'Production of a group',	'milking',	'Kg'),
(3,	'Average Milking Time',	'milking',	'hours'),
(4,	'Total duration',	'milking',	'hours'),
(5,	'Milking efficiency',	'milking',	''),
(6,	'Creating a group',	'milking',	''),
(7,	'Animals number',	'milking',	''),
(8,	'Number of animals in lactation',	'herd',	''),
(9,	'Total number of animals',	'herd',	''),
(10,	'Youngstock Number',	'herd',	''),
(11,	'Percent of pregnant animals',	'fertility',	''),
(12,	'Separated Animals',	'separation',	'');
EOF;

// new table `users_informations`
$queries[] = <<<EOF
DROP TABLE IF EXISTS `users_informations`;
CREATE TABLE `users_informations` (
  `user_id` int(11) NOT NULL,
  `idinfo` int(11) NOT NULL,
  `custom_value_1` varchar(255) NOT NULL,
  `custom_value_2` varchar(255) NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `idinfo` (`idinfo`),
  CONSTRAINT `users_informations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `users_informations_ibfk_2` FOREIGN KEY (`idinfo`) REFERENCES `informations` (`idinfo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOF;

// new table `messages`
$queries[] = <<<EOF
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `idmessage` INT unsigned NOT NULL,
  `role_id` INT(11) NOT NULL,
  `category` VARCHAR(25) NOT NULL,
  `descrizione` VARCHAR(50) NOT NULL,
  `type` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`idmessage`),
  INDEX `fk_messages_roles1_idx` (`role_id` ASC) ,
  CONSTRAINT `fk_messages_roles1`
    FOREIGN KEY (`role_id` )
    REFERENCES `roles` (`role_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
EOF;

//Dumping data for `messages`
$queries[] = <<<EOF
INSERT INTO `messages` (`idmessage`, `role_id`, `category`, `descrizione`, `type`) VALUES
(0,     1,  'milking',  'ANIMALS_SEPARETED',                      'animal'),
(2,     1,  'milking',  'ANIMALS_HEAT_ATTENTION',                 'animal'),
(4,     1,  'milking',  'ANIMALS_HEAT_SUSPICIOUS',                'animal'),
(101,   1,  'milking',  'ANIMALS_FEED_BALANCE',                   'animal'),
(103,   1,  'milking',  'ANIMALS_FEEDING_DISABLED',               'animal'),
(201,   1,  'milking',  'ANIMALS_MILK_PRODUCTION_TOO_LOW',        'animal'),
(202,   1,  'milking',  'ANIMALS_MILK_SEPARATION',                'animal'),
(301,   1,  'milking',  'ANIMALS_CALVING',                        'animal'),
(302,   1,  'milking',  'ANIMALS_DRY_OFF',                        'animal'),
(303,   1,  'milking',  'ANIMALS_PREGNANCY_CHECK',                'animal'),
(304,   1,  'milking',  'ANIMALS_NO_HEAT',                        'animal'),
(305,   1,  'milking',  'ANIMALS_NO_INSEMINATION',                'animal'),
(306,   1,  'milking',  'ANIMALS_HEAT',                           'animal'),
(604,   1,  'milking',  'ANIMALS_SMARTTAG_NOT_WORKING_PROPERLY',  'animal'),
(700,   1,  'milking',  'ANIMALS_HEALT_STENDUP_COUNT',            'animal'),
(702,   1,  'milking',  'ANIMALS_HEALT_STEP_COUNT',               'animal'),
(1001,  1,  'Settings', 'General not set',                        'service'),
(1002,  1,  'Settings', 'Networking not set',                     'service'),
(1003,  1,  'Settings', 'KPI not set',                            'service'),
(1004,  1,  'Settings', 'Parlour configurator not set',           'service'),
(2001,  1,  'KPI',      'average_days_first_service',             'service'),
(2002,  1,  'KPI',      'average_services_per_pregnancy',         'service'),
(2003,  1,  'KPI',      'average_days_open',                      'service'),
(2004,  1,  'KPI',      'expected_calving_interval',              'service'),
(2005,  1,  'KPI',      'heat_detection_rate',                    'service'),
(2006,  1,  'KPI',      'conception_rate',                        'service'),
(2007,  1,  'KPI',      'pregnancy_rate',                         'service');
EOF;

// new table `messages_log`
$queries[] = <<<EOF
DROP TABLE IF EXISTS `messages_log`;
CREATE TABLE `messages_log` (
  `idmsglog` INT unsigned NOT NULL AUTO_INCREMENT,
  `idmessage` INT unsigned NOT NULL,
  `idanimal` INT UNSIGNED NULL,
  `ts` DATETIME NOT NULL,
  `checked` TINYINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`idmsglog`),
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
EOF;

//Dumping data for `messages_log`
$queries[] = <<<EOF
INSERT INTO `messages_log` (`idmsglog`, `idmessage`, `idanimal`, `ts`, `checked`) VALUES
(1, 1001,  0,  '0000-00-00 00:00:00',  0),
(2, 1002,  0,  '0000-00-00 00:00:00',  0),
(3, 1003,  0,  '0000-00-00 00:00:00',  0),
(4, 1004,  0,  '0000-00-00 00:00:00',  0);
EOF;

// new table `widgets`
$queries[] = <<<EOF
DROP TABLE IF EXISTS `widgets`;
CREATE TABLE `widgets` (
  `idwidgets` int(11) NOT NULL AUTO_INCREMENT,
  `descrizione` varchar(50) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  PRIMARY KEY (`idwidgets`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOF;

//Dumping data for `widgets`
$queries[] = <<<EOF
INSERT INTO `widgets` (`idwidgets`, `descrizione`, `class_name`) VALUES
(1,	'',	'expected_calving_interval_dashboard'),
(2,	'',	'first_service_avg_days'),
(3,	'',	'fertility_avg_pregnancy_meter'),
(4,	'',	'avg_days_open'),
(5,	'',	'heat_detection_rate'),
(6,	'',	'conception_rate'),
(7,	'',	'pregnancy_rate'),
(8,	'',	'url');
EOF;

// new table `users_has_widgets`
$queries[] = <<<EOF
DROP TABLE IF EXISTS `users_has_widgets`;
CREATE TABLE `users_has_widgets` (
  `user_id` int(11) NOT NULL,
  `idwidgets` int(11) DEFAULT NULL,
  `custom_value_1` varchar(255) NOT NULL,
  `custom_value_2` varchar(255) NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `idwidgets` (`idwidgets`),
  CONSTRAINT `users_has_widgets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `users_has_widgets_ibfk_2` FOREIGN KEY (`idwidgets`) REFERENCES `widgets` (`idwidgets`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOF;

$queries[] = "SET FOREIGN_KEY_CHECKS=1;";
?>