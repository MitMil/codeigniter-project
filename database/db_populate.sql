SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Populate Database `interpuls_bb` (ver. 1.5)
--

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`idevent`, `description`, `velos_class_name`) VALUES
(-7, 'Pregnancy Check Event', 'PregnancyCheckEvent'),
(-6, 'KeepOpen Event', 'KeepOpenEvent'),
(-5, 'Heat Event', 'HeatEvent'),
(-4, 'Insemination Event', 'InseminationEvent'),
(-3, 'Calving Event', 'CalvingEvent'),
(-2, 'Dryoff Event', 'DryoffEvent'),
(-1, 'Birth Event', 'BirthEvent');


--
-- Dumping data for table `label_data_type`
--

INSERT INTO `label_data_type` (`idlabel_data_type`, `velos_code`) VALUES
(1, 'EATING_TIME '),
(2, 'LEG_ACTIVITY'),
(3, 'NECK_ACTIVITY'),
(4, 'LYING_TO_STANDING_COUNT'),
(5, 'LYING_TIME'),
(6, 'WALKING_TIME'),
(7, 'STANDING_TIME');


--
-- Dumping data for table `bb_settings`
--

INSERT INTO `bb_settings` (`var_code`, `env`, `type_values`, `default_value`, `value`, `date_update`) VALUES
('VELOS_URL', 'VELOS', 'URL', 'http://192.168.0.157', '', NULL),
('VELOS_URL_REMOTE', 'VELOS', 'URL', 'http://192.168.0.157', '', NULL),
('VELOS_ACCOUNT', 'VELOS', 'STRING', 'iFC', '', NULL),
('VELOS_PASSWORD', 'VELOS', 'STRING', 'iFCInterPuls', '', NULL),
('VELOS_CONNECTION_TIMER', 'VELOS', 'INT', '1200', '', NULL),
('BACKUP_FTP_SERVER', 'BACKUP', 'STRING', '', '', NULL),
('BACKUP_FTP_PATH', 'BACKUP', 'PATH', '/', '', NULL),
('BACKUP_FTP_USERNAME', 'BACKUP', 'STRING', '', '', NULL),
('BACKUP_FTP_PASSWORD', 'BACKUP', 'STRING', '', '', NULL),
('IMILK_DEBUG_ENABLED', 'IMILK', 'INT', '0', '', NULL),
('IMILK_SOCKET_IP', 'IMILK', 'STRING', '192.168.0.99', '', NULL),
('IMILK_WS_ALLOW_HOST', 'IMILK', 'STRING', 'localhost', '', NULL),
('IMILK_WS_URL', 'IMILK', 'URL', 'http://localhost/imilk/api/', '', NULL),
('BBWEB_VERSION', 'BBWEB', 'INT', '115', '', NULL),
('BBWEB_VWP1', 'BBWEB', 'INT', '60', '', NULL),
('BBWEB_VWP2', 'BBWEB', 'INT', '45', '', NULL),
('BBWEB_VWP3', 'BBWEB', 'INT', '45', '', NULL),
('BBWEB_PERIOD_DRY_OFF', 'BBWEB', 'INT', '220', '', NULL),
('BBWEB_PERIOD_GESTATION', 'BBWEB', 'INT', '280', '', NULL),
('BBWEB_AVERAGE_DAYS_TO_FS', 'BBWEB', 'STRING', 'a:10:{i:0;a:1:{s:4:"SORT";s:1:"0";}i:1;s:1:"1";i:2;s:7:"#FF0000";i:3;s:3:"VWP";i:4;s:7:"#00B050";i:5;a:1:{s:1:"V";s:3:"+32";}i:6;s:7:"#FFF400";i:7;a:1:{s:1:"V";s:3:"+21";}i:8;s:7:"#FF0000";i:9;a:1:{s:1:"V";s:3:"+21";}}', '', NULL),
('BBWEB_AVERAGE_DAYS_OPEN', 'BBWEB', 'STRING', 'a:10:{i:0;a:1:{s:4:"SORT";s:1:"1";}i:1;s:2:"40";i:2;s:7:"#FF0000";i:3;s:3:"VWP";i:4;s:7:"#00B050";i:5;a:1:{s:1:"V";s:3:"110";}i:6;s:7:"#FFF400";i:7;a:1:{s:1:"V";s:3:"130";}i:8;s:7:"#FF0000";i:9;a:1:{s:1:"V";s:3:"200";}}', '', NULL),
('BBWEB_EXP_CALVING_INTERVAL', 'BBWEB', 'STRING', 'a:10:{i:0;a:1:{s:4:"SORT";s:1:"2";}i:1;s:3:"320";i:2;s:7:"#FFF400";i:3;a:1:{s:1:"V";s:3:"360";}i:4;s:7:"#00B050";i:5;a:1:{s:1:"V";s:3:"400";}i:6;s:7:"#FFF400";i:7;a:1:{s:1:"V";s:3:"420";}i:8;s:7:"#FF0000";i:9;a:1:{s:1:"V";s:3:"500";}}', '', NULL),
('BBWEB_HEAT_DETECTION_RATE', 'BBWEB', 'STRING', 'a:8:{i:0;a:1:{s:4:"SORT";s:1:"3";}i:1;s:2:"0%";i:2;s:7:"#FF0000";i:3;a:1:{s:1:"V";s:3:"40%";}i:4;s:7:"#FFF400";i:5;a:1:{s:1:"V";s:3:"60%";}i:6;s:7:"#00B050";i:7;a:1:{s:1:"V";s:4:"100%";}}', '', NULL),
('BBWEB_CONCEPTION_RATE', 'BBWEB', 'STRING', 'a:8:{i:0;a:1:{s:4:"SORT";s:1:"4";}i:1;s:2:"0%";i:2;s:7:"#FF0000";i:3;a:1:{s:1:"V";s:3:"30%";}i:4;s:7:"#FFF400";i:5;a:1:{s:1:"V";s:3:"50%";}i:6;s:7:"#00B050";i:7;a:1:{s:1:"V";s:3:"80%";}}', '', NULL),
('BBWEB_PREGNANCY_RATE', 'BBWEB', 'STRING', 'a:8:{i:0;a:1:{s:4:"SORT";s:1:"5";}i:1;s:2:"0%";i:2;s:7:"#FF0000";i:3;a:1:{s:1:"V";s:3:"10%";}i:4;s:7:"#FFF400";i:5;a:1:{s:1:"V";s:3:"25%";}i:6;s:7:"#00B050";i:7;a:1:{s:1:"V";s:3:"50%";}}', '', NULL),
('BBWEB_AVG_SERVICES_X_PREGN', 'BBWEB', 'STRING', 'a:8:{i:0;a:1:{s:4:"SORT";s:1:"6";}i:1;s:1:"1";i:2;s:7:"#00B050";i:3;a:1:{s:1:"V";s:3:"2.5";}i:4;s:7:"#FFF400";i:5;a:1:{s:1:"V";s:3:"3.5";}i:6;s:7:"#FF0000";i:7;a:1:{s:1:"V";s:2:"10";}}', '', NULL),
('BBWEB_AVERAGE_LACTATIONS', 'BBWEB', 'STRING', 'a:8:{i:0;a:1:{s:4:"SORT";s:1:"8";}i:1;s:1:"2";i:2;s:7:"#FF0000";i:3;a:1:{s:1:"V";s:3:"2.5";}i:4;s:7:"#FFF400";i:5;a:1:{s:1:"V";s:1:"3";}i:6;s:7:"#00B050";i:7;a:1:{s:1:"V";s:3:"4.5";}}', '', NULL),
('BBWEB_AVERAGE_DIM', 'BBWEB', 'STRING', 'a:8:{i:0;a:1:{s:4:"SORT";s:1:"9";}i:1;s:1:"0";i:2;s:7:"#00B050";i:3;a:1:{s:1:"V";s:3:"250";}i:4;s:7:"#FFF400";i:5;a:1:{s:1:"V";s:3:"650";}i:6;s:7:"#FF0000";i:7;a:1:{s:1:"V";s:3:"950";}}', '', NULL),
('BBWEB_HC_DIM', 'BBWEB', 'STRING', 'a:11:{i:0;a:1:{s:4:"SORT";s:2:"10";}i:1;s:1:"0";i:2;s:7:"#00EFFD";i:3;s:3:"VWP";i:4;s:7:"#FFFFFF";i:5;a:1:{s:1:"V";s:3:"120";}i:6;s:7:"#E5E5E5";i:7;a:1:{s:1:"V";s:3:"200";}i:8;s:7:"#9F9F9F";i:9;a:1:{s:1:"V";s:3:"305";}i:10;s:7:"#818181";}', '', NULL),
('BBWEB_IFC_REGISTRATION', 'BBWEB', 'INT', '0', '', NULL),
('BBWEB_IFC_URL', 'BBWEB', 'URL', 'https://ifc-server.myfarm.cloud', '', NULL),
('SYSTEM_IP', 'SYSTEM', 'STRING', '192.168.0.200', '', NULL),
('SYSTEM_NETMASK', 'SYSTEM', 'STRING', '255.255.255.0', '', NULL),
('SYSTEM_GATEWAY', 'SYSTEM', 'STRING', '192.168.0.1', '', NULL),
('SYSTEM_CLIENT_NAME', 'SYSTEM', 'STRING', '', '', NULL),
('SYSTEM_NAME', 'SYSTEM', 'STRING', '', '', NULL),
('SYSTEM_SERIAL_NUMBER', 'SYSTEM', 'STRING', '', '', NULL),
('SYSTEM_EMAIL', 'SYSTEM', 'STRING', '', '', NULL),
('SYSTEM_ADDRESS', 'SYSTEM', 'STRING', '', '', NULL),
('SYSTEM_CITY', 'SYSTEM', 'STRING', '', '', NULL),
('SYSTEM_STATE', 'SYSTEM', 'STRING', '', '', NULL),
('SYSTEM_COUNTRY', 'SYSTEM', 'STRING', '', '', NULL),
('SYSTEM_CAP', 'SYSTEM', 'STRING', '', '', NULL),
('SYSTEM_PHONE', 'SYSTEM', 'STRING', '', '', NULL);


--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `name`, `email`, `profile`, `address`, `cap`, `city`, `state`, `phone`, `language`, `date_format`, `time_format`, `calender_type`, `insert_datetime`, `update_datetime`) VALUES
(1, 'SUI', 'ca77f2eb4d782401d64ff57fb4199c60', '', '', 'fa fa-users', '', '', NULL, NULL, '', 'en_US', '', '', '', NOW(), NOW()),
(2, 'service', 'f0dbaddda5bc543394f671469d0c08e4', 'Service iFC', 'ifc@myfarm.cloud', 'fa fa-user', '', '', NULL, NULL, '', 'en_US', '', '', '', NOW(), NOW()),
(3, 'manager', '1d0258c2440a8d19e716292b231e3190', 'Manager', 'manager@myfarm.cloud', 'fa fa-user', '', '', NULL, NULL, '', 'en_US', '', '', '', NOW(), NOW()),
(4, 'farmer', '97f974881b3726d9a77014b5f3b4d795', 'Farmer', 'farmer@myfarm.cloud', 'fa fa-user', '', '', NULL, NULL, '', 'en_US', '', '', '', NOW(), NOW());


--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `name`, `parent`, `insert_datetime`, `update_datetime`) VALUES
(1, 'Admin', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Service', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'Manager', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'User', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00');


--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `role_id`, `user_id`, `insert_datetime`, `update_datetime`) VALUES
(1, 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 2, 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 3, 3, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 4, 4, '0000-00-00 00:00:00', '0000-00-00 00:00:00');


--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`module_id`, `module_name`, `module_url`, `parent`, `insert_datetime`, `update_datetime`) VALUES
(1,	'Key Performance Indicators',	'kpi',						0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(2,	'Animal Label Values',			'alv',						0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(3,	'Users',						'users',					0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(4,	'Roles',						'roles',					0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(5,	'Modules',						'modules',					0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(6,	'Settings',						'settings',					0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(7,	'Milking',						'milking',					0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(8,	'Utilities',					'utilities',				0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(9,	'Service',						'service',					0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(10,'Milking Session',				'sessions',					7,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(11,'Milking Monitor',				'monitor',					7,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(12,'Update Firmware',				'updatefirmware',			8,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(13,'Debug iMiklNET',				'sendcmd',					8,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(14,'Backup Restore',				'backuprestoredb',			9,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(15,'Reset Procedures',				'reset_procedures',			9,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(16,'Update iFC',					'updatesoftware',			9,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(17,'Devices',						'devices',					9,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(18,'Display All Logs',				'showlogs',					9,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(19,'Networking',					'networking',				6,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(20,'Parlour Configurator',			'pconfigurator_callback',	6,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(21,'KPI',							'settings/kpi',				6,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(22,'Reboot iFC',					'reboot',					6,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(23,'General',						'settings/general',			6,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(24,'Herd Composition',				'herd_composition',			1,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(25,'Lactation',					'lactation',				1,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(26,'DIM',							'dim',						1,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(27,'Fertility KPIs',				'fertility_kpis',			1,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(28,'All Modules',					'modules/allmodule',		5,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(29,'Modules Assign',				'assign',					5,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(30,'All Users',					'users/allusers',			3,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(31,'Users Modules',				'users/modules',			3,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(32,'Languages',					'settings/languages',		6,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00');

--
-- Dumping data for table `module_assign`
--

INSERT INTO `module_assign` (`id`, `role_id`, `user_id`, `module_id`, `insert_datetime`, `update_datetime`) VALUES
(1,	1,	NULL,	1,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(2,	2,	NULL,	1,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(3,	3,	NULL,	1,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(4,	4,	NULL,	1,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(5,	1,	NULL,	24,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(6,	2,	NULL,	24,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(7,	3,	NULL,	24,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(8,	4,	NULL,	24,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(9,	1,	NULL,	25,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(10,2,	NULL,	25,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(11,3,	NULL,	25,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(12,4,	NULL,	25,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(13,1,	NULL,	26,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(14,2,	NULL,	26,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(15,3,	NULL,	26,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(16,4,	NULL,	26,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(17,1,	NULL,	27,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(18,2,	NULL,	27,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(19,3,	NULL,	27,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(20,4,	NULL,	27,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(21,1,	NULL,	2,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(22,1,	NULL,	3,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(23,2,	NULL,	3,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(24,3,	NULL,	3,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(25,4,	NULL,	3,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(26,1,	NULL,	30,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(27,2,	NULL,	30,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(28,3,	NULL,	30,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(29,1,	NULL,	31,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(30,2,	NULL,	31,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(31,3,	NULL,	31,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(32,1,	NULL,	4,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(33,2,	NULL,	4,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(34,1,	NULL,	5,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(35,1,	NULL,	28,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(36,1,	NULL,	29,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(37,1,	NULL,	6,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(38,2,	NULL,	6,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(39,3,	NULL,	6,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(40,1,	NULL,	19,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(41,2,	NULL,	19,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(42,1,	NULL,	20,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(43,2,	NULL,	20,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(44,3,	NULL,	20,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(45,1,	NULL,	21,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(46,2,	NULL,	21,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(47,3,	NULL,	21,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(48,1,	NULL,	22,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(49,2,	NULL,	22,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(50,1,	NULL,	23,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(51,2,	NULL,	23,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(52,1,	NULL,	32,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(53,2,	NULL,	32,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(54,1,	NULL,	7,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(55,2,	NULL,	7,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(56,3,	NULL,	7,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(57,4,	NULL,	7,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(58,1,	NULL,	10,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(59,1,	NULL,	11,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(60,2,	NULL,	11,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(61,3,	NULL,	11,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(62,4,	NULL,	11,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(63,1,	NULL,	8,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(64,2,	NULL,	8,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(65,1,	NULL,	12,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(66,2,	NULL,	12,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(67,1,	NULL,	13,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(68,1,	NULL,	9,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(69,2,	NULL,	9,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(70,1,	NULL,	14,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(71,2,	NULL,	14,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(72,1,	NULL,	15,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(73,2,	NULL,	15,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(74,1,	NULL,	16,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(75,2,	NULL,	16,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(76,1,	NULL,	17,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
(77,1,	NULL,	18,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00');

--
-- Dumping data for table `module_assign`
--
INSERT INTO `languages` (`code`, `lang`, `filename`, `is_default`) VALUES ('en_US', 'English (American)', '', 1);
INSERT INTO `languages` (`code`, `lang`, `filename`, `is_default`) VALUES ('it_IT', 'Italian', '', 1);

--
-- Dumping data for table `informations`
--
INSERT INTO `informations` (`idinfo`, `descrizione`, `class_name`, `udm`) VALUES
(1,	'Total production',					'milking',		'Kg'),
(2,	'Production of a group',			'milking',		'Kg'),
(3,	'Average Milking Time',				'milking',		'hours'),
(4,	'Total duration',					'milking',		'hours'),
(5,	'Milking efficiency',				'milking',		''),
(6,	'Creating a group',					'milking',		''),
(7,	'Animals number',					'milking',		''),
(8,	'Number of animals in lactation',	'herd',			''),
(9,	'Total number of animals',			'herd',			''),
(10,'Youngstock Number',				'herd',			''),
(11,'Percent of pregnant animals',		'fertility',	''),
(12,'Separated Animals',				'separation',	'');

--
-- Dumping data for table `messages`
--
INSERT INTO `messages` (`idmessage`, `role_id`, `category`, `descrizione`, `type`) VALUES
(0,		1,	'milking',	'ANIMALS_SEPARETED',						'animal'),
(2,		1,	'milking',	'ANIMALS_HEAT_ATTENTION',					'animal'),
(4,		1,	'milking',	'ANIMALS_HEAT_SUSPICIOUS',					'animal'),
(101,	1,	'milking',	'ANIMALS_FEED_BALANCE',						'animal'),
(103,	1,	'milking',	'ANIMALS_FEEDING_DISABLED',					'animal'),
(201,	1,	'milking',	'ANIMALS_MILK_PRODUCTION_TOO_LOW',			'animal'),
(202,	1,	'milking',	'ANIMALS_MILK_SEPARATION',					'animal'),
(301,	1,	'milking',	'ANIMALS_CALVING',							'animal'),
(302,	1,	'milking',	'ANIMALS_DRY_OFF',							'animal'),
(303,	1,	'milking',	'ANIMALS_PREGNANCY_CHECK',					'animal'),
(304,	1,	'milking',	'ANIMALS_NO_HEAT',							'animal'),
(305,	1,	'milking',	'ANIMALS_NO_INSEMINATION',					'animal'),
(306,	1,	'milking',	'ANIMALS_HEAT',								'animal'),
(604,	1,	'milking',	'ANIMALS_SMARTTAG_NOT_WORKING_PROPERLY',	'animal'),
(700,	1,	'milking',	'ANIMALS_HEALT_STENDUP_COUNT',				'animal'),
(702,	1,	'milking',	'ANIMALS_HEALT_STEP_COUNT',					'animal'),
(1001,	1,	'Settings',	'General not set',							'service'),
(1002,	1,	'Settings',	'Networking not set',						'service'),
(1003,	1,	'Settings',	'KPI not set',								'service'),
(1004,	1,	'Settings',	'Parlour configurator not set',				'service'),
(2001,	1,	'KPI',		'average_days_first_service',				'service'),
(2002,	1,	'KPI',		'average_services_per_pregnancy',			'service'),
(2003,	1,	'KPI',		'average_days_open',						'service'),
(2004,	1,	'KPI',		'expected_calving_interval',				'service'),
(2005,	1,	'KPI',		'heat_detection_rate',						'service'),
(2006,	1,	'KPI',		'conception_rate',							'service'),
(2007,	1,	'KPI',		'pregnancy_rate',							'service');



--
-- Dumping data for table `messages_log`
--
INSERT INTO `messages_log` (`idmsglog`, `idmessage`, `idanimal`, `ts`, `checked`) VALUES
(1,	1001,	0,	'0000-00-00 00:00:00',	0),
(2,	1002,	0,	'0000-00-00 00:00:00',	0),
(3,	1003,	0,	'0000-00-00 00:00:00',	0),
(4,	1004,	0,	'0000-00-00 00:00:00',	0);

--
-- Dumping data for table `widgets`
--
INSERT INTO `widgets` (`idwidgets`, `descrizione`, `class_name`) VALUES
(1,	'',	'expected_calving_interval_dashboard'),
(2,	'',	'first_service_avg_days'),
(3,	'',	'fertility_avg_pregnancy_meter'),
(4,	'',	'avg_days_open'),
(5,	'',	'heat_detection_rate'),
(6,	'',	'conception_rate'),
(7,	'',	'pregnancy_rate'),
(8,	'',	'url');

SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
