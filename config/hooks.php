<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|   http://codeigniter.com/user_guide/general/hooks.html
|
*/

/**
 * Language Loader ( default language : italian )
 */
$hook['post_controller_constructor'][] = array(
    'class' => 'LanguageLoader',
    'function' => 'initialize',
    'filename' => 'LanguageLoader.php',
    'filepath' => 'hooks'
);

/**
 * BB Settings form DB
 */
$hook['pre_system'] = array(
    'class'    => 'Configurator',
    'function' => 'load',
    'filename' => 'Configurator.php',
    'filepath' => 'hooks'
);

/**
 * Check-login to system
 */
$hook['post_controller_constructor'][] = array(
    'class'    => 'Login',
    'function' => 'check_login',
    'filename' => 'Login.php',
    'filepath' => 'hooks'
    );
