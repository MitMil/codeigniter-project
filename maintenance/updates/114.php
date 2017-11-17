<?php
    
// Define this version number
$BBWEB_NEW_VERSION = 114;

$queries = array();
$queries[] = "SET FOREIGN_KEY_CHECKS=0;";
// new table languages
$queries[] = <<<EOF
DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `idlang` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(6) NOT NULL,
  `lang` varchar(32) NOT NULL,
  `filename` varchar(64) NOT NULL,
  `is_default` tinyint(4) NOT NULL,
  PRIMARY KEY (`idlang`),
  UNIQUE KEY `id` (`idlang`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOF;

//Dumping data for `languages`
// $queries[] = <<<EOF
// INSERT INTO `languages` (`code`, `lang`, `filename`, `is_default`) VALUES
// ('en_US', 'English (American)', '', 1),
// ('it_IT', 'Italian', '', 1);
// EOF;


// new table language_strings
$queries[] = <<<EOF
DROP TABLE IF EXISTS `language_strings`;
CREATE TABLE `language_strings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idlang` int(11) NOT NULL,
  `msgid` text NOT NULL,
  `msgstr` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idlang` (`idlang`),
  CONSTRAINT `language_strings_ibfk_1` FOREIGN KEY (`idlang`) REFERENCES `languages` (`idlang`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
EOF;

$queries[] = "DELETE FROM modules WHERE module_id >= 32;";

// add language and upload language module
$queries[] = <<<EOF
INSERT INTO `modules` (`module_id`, `module_name`, `module_url`, `parent`, `insert_datetime`, `update_datetime`) VALUES
(32,'Languages',              'settings/languages',       6,  '0000-00-00 00:00:00',  '0000-00-00 00:00:00');
EOF;

// Only remove predefined Module Assigned.
$queries[] = "DELETE FROM module_assign WHERE user_id IS NULL AND module_id >= 32;";

//dump assign language module
$queries[] = <<<EOF
INSERT INTO `module_assign` (`id`, `role_id`, `user_id`, `module_id`, `insert_datetime`, `update_datetime`) VALUES
(NULL,1,  NULL, 32, '0000-00-00 00:00:00',  '0000-00-00 00:00:00'),
(NULL,2,  NULL, 32, '0000-00-00 00:00:00',  '0000-00-00 00:00:00');
EOF;

$queries[] = "SET FOREIGN_KEY_CHECKS=1;";
