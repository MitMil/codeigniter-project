<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (!function_exists('p')) {

    function p($arr)
    {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }
}

function load_views($filename, $data, $load_template = array('top_menu' => true))
{

    $CI =& get_instance();
    $CI->load->view('template/header', $data);
    if (array_key_exists('top_menu', $load_template) && $load_template['top_menu']) {
        $CI->load->view('template/top_menu', $data);
    }
    $CI->load->view($filename, $data);
    $CI->load->view('template/footer', $data);
}

function insert_extra($insert_array)
{
    $insert_array['insert_datetime'] = date('Y-m-d H:i:s');
    $insert_array['update_datetime'] = date('Y-m-d H:i:s');

    return $insert_array;
}

function update_extra($update_array)
{
    $update_array['update_datetime'] = date('Y-m-d H:i:s');
    return $update_array;
}

function create_slug($slug)
{

    $lettersNumbersSpacesHypens = '/[^\-\sa-zA-Z0-9]+/';
    $slug = preg_replace($lettersNumbersSpacesHypens, '', mb_strtolower($slug));
    $spacesAndDuplicateHyphens = '/[\-\s]+/';
    $slug = preg_replace($spacesAndDuplicateHyphens, '-', $slug);

    return $slug;
}
function Redirect_template($type)
{
    switch ($type) {
        case '404':
            load_views('404', array());
            break;

        default:
            # code...
            break;
    }
}
function insert_restriction($page = 'roles')
{

    $CI =& get_instance();
    $user_details = $CI->session->userdata('user');
    //print_r($user_details);
    $allow = false;
    if ($user_details['roles']) {
        $roles = explode(',', $user_details['roles']);
        foreach ($roles as $key => $role) {
            if ($page == 'roles') {
                if ($role == 1 || $role == 2 || $role == 3) {
                    $allow = true;
                    break;
                }
            } else if ($page == 'module') {
                if ($role == 1) {
                    $allow = true;
                    break;
                }
            } else if ($page == 'settings') {
                if ($role == 1) {
                    $allow = true;
                    break;
                }
            }
        }
    }

    return $allow;
}

function check_Allrestriction($page = '')
{

    $CI =& get_instance();

	$user_details = $CI->session->userdata('user');
    //get modules
    $restrict_modules = $user_details['restrict_modules'];

    $controller = '';
    if (!$page) {
        $controller = $CI->uri->segment(1);
    }

    if ($user_details['roles']) {
        $roles = explode(',', $user_details['roles']);
        foreach ($roles as $key => $role) {

            if ($role == 1) {
                return true;
            } //user is admin

            // check if module is NOT available to user
            if ($page && in_array($page, $restrict_modules)) { //argument as controller
                return false;
            } else {
                if (($controller) && in_array($controller, $restrict_modules)) { // default page restriction
                    return false;
                }
            }
        }
        return true;
    } else {
        Redirect_template('404');
    }
}

function gmtime_format($seconds, $format = 'H:i:s')
{
    return gmdate($format, $seconds);
}

/**
 * Display HUMAN format date & time
 * Default format of date & time are logged in user setting date|time format.
 * @param  [string]  $date_time   [db datetime or timestamp]
 * @param  boolean $date_only   [only date return]
 * @param  boolean $time_only   [only time return]
 * @param  string  $date_format [date format e.g. m-d-Y]
 * @param  string  $time_format [time format e.g. h:i]
 * @return [string]               [return datetime|date|time]
 */
function display_datetime($date_time, $date_only = false, $time_only = false, $date_format = '', $time_format = '')
{
    $CI =& get_instance();
    $user_data = $CI->session->userdata('user');

    $user_data['date_format'] = (!$user_data['date_format']) ? 'd/m/y' : $user_data['date_format'];
    $user_data['time_format'] = (!$user_data['time_format']) ? 'H:i' : $user_data['time_format'];

    $date_format = (!$date_format) ? $user_data['date_format'] : $date_format;
    $time_format = (!$time_format) ? $user_data['time_format'] : $time_format;

    if (is_null($date_time) || strpos($date_time, '0000-00-00') !== false) {
        return;
    }

    if (!is_int($date_time)) {
        $date_time = strtotime($date_time);
    }

    $date_time = date('Y-m-d H:i:s', $date_time);

    $timestamp = explode(" ", $date_time);
    $date = $timestamp[0];
    $time = $timestamp[1];

    $display_date = date($date_format, strtotime($date));
    if ($date_only) {
        return $display_date;
    }

    $display_time = date($time_format, strtotime($time));
    if ($time_only) {
        return $display_time;
    }

    return $display_date." ".$display_time;
}

function dateformat_choices()
{
    $formats = array(
        'd/m/y' => 'dd / mm / yy',
        'm/d/y' => 'mm / dd / yy',
        'd-m-y' => 'dd - mm - yy',
        'm-d-y' => 'mm - dd - yy',
        'y/m/d' => 'yy / mm / dd',
        'y/d/m' => 'yy / dd / mm',
        'y-m-d' => 'yy - mm - dd',
        'y-d-m' => 'yy - dd - mm',
        );
    return $formats;
}

function system_time_format($time)
{
    if (is_int($time)) {
        if ($time > 0) {
            $time = date('Y-m-d H:i:s', $time);
        } else {
            return '00:00:00';
        }
    }
    return date('H:i:s', strtotime($time));
}

function system_date_format($date)
{
    if (is_int($date)) {
        if ($date > 0) {
            $date = date('Y-m-d H:i:s', $date);
        } else {
            return date('d/m/Y');
        }
    }
    return date('Y-m-d', strtotime($date));
}

function x_week_range($week, $year, $start = true, $end = true)
{

    $return = array();
    $return['start']  = date('Y-m-d', strtotime("$year-W$week-0"));
    $return['end'] = date('Y-m-d', strtotime("$year-W$week-6"));
    return $return;
}

function filter_lbt($name)
{

    if (strpos(strtolower($name), 'avg') !== false) {
        $name = str_replace('avg_', '', $name);
    }
    if (strpos(strtolower($name), 'count') !== false) {
        $name = str_replace('_count', '', $name);
    }
    $name = ucwords(str_replace('_', ' ', strtolower($name)));
    return $name;
}

function get_periods()
{
    $periods = array(
            '0' => array( //MILKING MORNING
                'id' => '1',
                'name' => 'MILKING MORNING',
                'start_time' => '04:30:00',
                'end_time' => '07:30:00',
            ),
            '1' => array( //DAY
                'id' => '2',
                'name' => 'DAY',
                'start_time' => '07:30:00',
                'end_time' => '16:30:00',
            ),
            '2' => array( // MILKING AFTERNOON
                'id' => '3',
                'name' => 'MILKING AFTERNOON',
                'start_time' => '16:30:00',
                'end_time' => '19:30:00',
            ),
            '3' => array( // NIGHT
                'id' => '4',
                'name' => 'NIGHT',
                'start_time' => '19:30:00',
                'end_time' => '04:30:00', // morning next day
            )
        );
    return $periods;
}

function period_star_end_datetime($date, $period, $periods)
{

    $return = array();
    foreach ($periods as $key => $p_data) {
        if ($p_data['id'] == $period) {
            if (strtotime($p_data['start_time']) > strtotime($p_data['end_time'])) {
                $return['start'] = $date.' '.$p_data['start_time'];
                $return['end']   = date('Y-m-d', strtotime('+1 day', strtotime($date))).' '.$p_data['end_time'];
            } else {
                $return['start'] = $date.' '.$p_data['start_time'];
                $return['end']   = $date.' '.$p_data['end_time'];
            }
            break;
        }
    }

    return $return;
}

function different_shade($hex, $diff = 20)
{
    $rgb = str_split(trim($hex, '# '), 2);
    foreach ($rgb as &$hex) {
        $dec = hexdec($hex);
        if ($diff >= 0) {
            $dec += $diff;
        } else {
            $dec -= abs($diff);
        }
        $dec = max(0, min(255, $dec));
        $hex = str_pad(dechex($dec), 2, '0', STR_PAD_LEFT);
    }
    return '#'.implode($rgb);
}

/**
 * Get diffrence between two dates in days
 * @param  string|int $start start date or timestamp
 * @param  string|int $end   if null set current timestamp
 * @return [int]        return number of days
 */
function get_days_diff($start, $end = '')
{

    $start_date = (is_int($start)) ? $start : strtotime($start);
    $end_date = ($end) ? ((is_int($end)) ? $end : strtotime($end)) : time();

    $datediff = $end_date - $start_date;
    $days = floor($datediff/(60*60*24));

    return $days;
}

function extend_range($graph_data)
{
    foreach ($graph_data as $graph_name => $range) {
        if ($range['value'] < $range['range_start']) {
            $graph_data[$graph_name]['range_start'] = $range['value'];
        } else if ($range['value'] > $range['range_end']) {
            $graph_data[$graph_name]['range_end'] = $range['value'];
        }
    }
    return $graph_data;
}

if (!function_exists('font_awesome_arr')) {
    function font_awesome_arr()
    {
        $pattern = '/\.(fa-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
        $subject = file_get_contents('assets/sbadmin2/bower_components/font-awesome/css/font-awesome.css');

        preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);

        $icons = array();

        foreach ($matches as $match) {
            $icons[$match[1]] = $match[2];
        }

        return $icons;
    }
}

/**
 * Generate URL based on parameters and current query string.
 * if $query_string is true, return URL will have ? or & at the end.
 *
 * @param  string $segments          URL segments in string.
 * @param  string $query_string      new query string to add in URL.
 * @param  string $remove_parameters remove any unnecessary query string parameters.
 *
 * @return string                    URL with query string.
 */
function generate_url($segments, $query_string = '', $remove_parameters = '')
{
    $url = base_url().trim($segments, '/');
    if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) {

        $server_qstring = $_SERVER['QUERY_STRING'];
        if ($remove_parameters) {
            $query_array = array();
            parse_str($server_qstring, $query_array);

            $remove_parameters_arr = explode(",", $remove_parameters);
            foreach ($remove_parameters_arr as $key => $value) {
                if (array_key_exists(trim($value), $query_array)) {
                    unset($query_array[trim($value)]);
                }
            }

            $server_qstring = http_build_query($query_array);
        }

        if ($server_qstring) {
            $url .= "?". $server_qstring;
            if ($query_string) {
                $url .= ($query_string === true) ? "&" : "&" . $query_string;
            }
        } else if ($query_string) {
            $url .= ($query_string === true) ? "?" : "?" . $query_string;
        }

    } elseif ($query_string) {
        $url .= ($query_string === true) ? "?" : "?". $query_string;
    }

    return $url;
}

/**
 * get current URL with query string.
 *
 * @return string
 */
function current_full_url()
{
    $current_url = current_url();
    return $_SERVER['QUERY_STRING'] ? $current_url.'?'.$_SERVER['QUERY_STRING'] : $current_url;
}

/**
 * Add current URL in session back array.
 *
 * @return void
 */
function add_back_url()
{
    /** @var string Current URL with full query string */
    $current_url = current_full_url();

    /** @var array URLs that starts with any elements will be ignored in Back Tracking  */
    $ignore_list = array();

    /** @var string URL without base */
    $ignore_str =  str_replace(base_url(), '', $current_url);

    foreach ($ignore_list as $ignore_prefix) {
        if (strpos($ignore_str, $ignore_prefix) !== false) {
            // current URL is in ignore list
            return;
        }
    }

    $CI =& get_instance();
    $back_arr = $CI->session->userdata('back_arr');
    if ($back_arr) {
        $count_back_arr = count($back_arr);

        if ($back_arr[$count_back_arr-1] == $current_url) {
            // Page refresh, don't do anything!
        } elseif (($count_back_arr >= 2) && $back_arr[$count_back_arr-2] == $current_url) {
            // Back button is clicked.
            unset($back_arr[$count_back_arr-1]);
        } else {
            // Add url to Back array.
            $back_arr[] = $current_url;
        }

    } else {
        $back_arr = array($current_url);
    }

    // Save array in the session.
    $CI->session->set_userdata('back_arr', $back_arr);
}

/**
 * get Back URL.
 *
 * @return string URL of last visited page.
 */
function back_url()
{
    $CI =& get_instance();
    $back_arr = $CI->session->userdata('back_arr');
    $back_url = '';
    if ($back_arr) {
        $count_back_arr = count($back_arr);
        if ($count_back_arr >= 2) {
            $back_url = $back_arr[$count_back_arr-2];
        }
    }
    return $back_url;
}

/**
 * get the class name from view file.
 * for URL generation.
 * @return string 1st segment of URL
 */
function getClass()
{
    $CI =& get_instance();
    return $CI->router->fetch_class();
}

/**
 * get the method name from view file.
 * for URL generation.
 * @return string all segments of URL after 1st position
 */
function getMethod()
{
    $CI =& get_instance();
    $segments = $CI->uri->segment_array();
    array_shift($segments);
    array_shift($segments);

    $return = $CI->router->fetch_method().'/';
    if ($segments) {
        foreach ($segments as $segment) {
            $return .= $segment.'/';
        }
    }
    $return = rtrim($return, '/');
    return $return;
}

/**
 * To fetch View Directory path.
 * @return string view dir path.
 */
function getViewpath()
{
    return APPLICATION_PATH.'/application/views';
}

/**
 * Check if string is serialized data.
 * @param  string  $string
 * @return boolean return true if serialized.
 */
function is_serialize($string)
{
    $data = @unserialize($string);
    if ($data !== false) {
        return true;
    }

    return false;
}

/**
 * Check if all lactations filter are removed.
 * @return boolean return true if removed
 */
function is_fitlers_removed()
{
    $no_data = false;
    if (isset($_GET['is_filter']) && $_GET['is_filter']) {
        if (!isset($_GET['lactation'])) {
            $no_data = true;
        }
    }
    return $no_data;
}


/**
 * Get Velos URL based on mobile or desktop with animal ID.
 *
 * @todo add both URLs of the Velos: local and remote.
 *
 * @param  string $animalid [description]
 *
 * @return string URL
 */
function getVelosURL($animalid = "")
{
    $action_url = VELOS_URL;
    if (isset($_SERVER['HTTP_X_REAL_IP']) && $_SERVER['HTTP_X_REAL_IP']) {
        $action_url = VELOS_URL_REMOTE;
    }

    if($animalid) {
        $action_url .= "farm/AnimalOverviewPage.web?id=" . $animalid;
    }

    return $action_url;
}


/**
 * Remove ENV prefix and underscore from bb_settings var_code.
 * @param  [type] $var_code Variable Code constant.
 * @param  [type] $env      Environment Keyname
 *
 * @return [type]           Display String.
 */
function displayVarCode($var_code, $env)
{
    $env_len = strlen($env);
    $var_code_len = strlen($var_code);
    if ($var_code_len > $env_len) {
        if (substr($var_code, 0, $env_len) == $env) {
            $var_code =  substr($var_code, $env_len);
        }
    }
    return str_replace('_', ' ', $var_code);
}

/**
 * Get IFC Serial Number
 * @return string serialnumber
 *
 * @TODO: WE REALLY NEED THIS FUNCTION?
 *
 */
function get_ifc_serialno()
{
    $serial_number = null;
    $globalSettingsFile = APPLICATION_PATH . '/../Protected/ifcInfo.sh';
    
    if (file_exists($globalSettingsFile)) {
        $file = fopen($globalSettingsFile, "r") or exit("Unable to open file!");
        //Output a line of the file until the end is reached
        while (!feof($file)) {
            $line = fgets($file);
            if (strpos($line, 'SERIAL=') !== false) {
                $serial_number = trim(str_replace('SERIAL=', '', $line));
                //break;
            }
        }
        fclose($file);
    }
    
    // if Serial Number is NULL die, cannot go ahead!
    if (is_null($serial_number)) {
        die('iFC serial number not found');
    }
    
    return $serial_number;
}


/**
 * get_bbweb_ifc_installation function check IFC_INSTALLATION variable is set or not in globalSettings.sh
 * @return String
 *
 * @TODO: WE REALLY NEED THIS FUNCTION?
 *
 */
function get_bbweb_ifc_installation()
{
    $IFC_INSTALLATION = null;
    $ifcInfoFile = APPLICATION_PATH . '/../Protected/ifcInfo.sh';
    
    if (file_exists($ifcInfoFile)) {
        $file = fopen($ifcInfoFile, "r") or exit("Unable to open file!");
        //Output a line of the file until the end is reached
        while (!feof($file)) {
            $line = fgets($file);
            if (strpos($line, 'IFC_INSTALLATION=') !== false) {
                $IFC_INSTALLATION = trim(str_replace('IFC_INSTALLATION=', '', $line));
                break;
            }
        }
        fclose($file);
    }
    
    // if IFC_INSTALLATION is NULL redirect to installation process!
    if (is_null($IFC_INSTALLATION)) {
        return 'users/ifc_installation';
    } else {
        return 'users/ifc_policy';
    }
}


/**
 * Set ifc installation flag
 * @return null
 *
 * @TODO: WE REALLY NEED THIS FUNCTION?
 *
 */
function set_ifc_installation($serial_number="")
{
    $ifcInfoFile = APPLICATION_PATH . '/../Protected/ifcInfo.sh';
    
    if (!file_exists($ifcInfoFile)) {
       set_ifc_serialno($serial_number);
    } else {
        $content = file_get_contents($ifcInfoFile);
        $content .= "IFC_INSTALLATION=1\n";
        file_put_contents($ifcInfoFile,$content);
    }
    return true;
}


/**
 * get JSON from shell script output
 * @param  [type] $output [description]
 * @return [type]         [description]
 */
function getJSON($output)
{
    foreach ($output as $str_json) {
        $outputArray = json_decode($str_json, true);
        if ($outputArray === null && json_last_error() !== JSON_ERROR_NONE) {
            // JSON decode error, continue
            continue;
        } else {
            return $outputArray;
        }
    }
    return false;
}


/**
 * To get Graph Colors.
 *
 * @return array colors array by Reproduction status.
 */
function getGraphColors()
{
    $color_DRY   = '#FFC039'; // All DRY colors.
    $main_colors = array(
        'OPEN' => array(
            'MAIN'   => '#5B8DD7', // Main Graph Inner Ring color.
            'INMILK' => '#FFF8E7', // Main Graph Outer Ring INMILK color.
            'DRY'    => $color_DRY,// Main Graph Outer Ring DRY color.
        ),
        'INSEMINATED' => array(
            'MAIN'   => '#8B4D4F',
            'INMILK' => '#FFF8E7',
            'DRY'    => $color_DRY,
        ),
        'PREGNANT' => array(
            'MAIN'   => '#529732',
            'INMILK' => '#FFF8E7',
            'DRY'    => $color_DRY,
        ),
        'KEEPOPEN' => array(
            'MAIN'   => '#3E3E3E',
            'INMILK' => '#FFF8E7',
            'DRY'    => $color_DRY,
        ),
        'UNKNOWN' => array(
            'MAIN'   => '#7F7F7F',
            'INMILK' => '#FFF8E7',
            'DRY'    => $color_DRY,
        ),
        // For Status Table.
        'DRY'    => $color_DRY,
        'INMILK' => '#FFF8E7',
    );
    return $main_colors;
}


function randomFloat($min = 0, $max = 1)
{
    return number_format($min + mt_rand() / mt_getrandmax() * ($max - $min), 2);
}


function hextoBitArr($hex)
{
    $bitString = base_convert($hex, 16, 2);
    $arBits = array_reverse(str_split($bitString));
    $arValues = array();
    for ($i=0; $i<(strlen($hex)*4); $i++) {
        $arValues[$i] = isset($arBits[$i]) ? $arBits[$i] : "0";
    }
    return $arValues;
}

/**
 * Get and check array value from given key
 * @param  [string] $key
 * @param  [array] $array
 * @return [string]
 */
function getArrayVal($key, $array)
{
    if (array_key_exists($key, $array) && $array[$key]) {
        return $array[$key];
    }
    return false;

}


/**
 * Ping any IP or url
 * @param  string $host    ip or url
 * @param  int $port    port number
 * @param  int $timeout timeout in seconds
 * @return int          false on error | ping time in millisecond on success.
 */
function ping($host, $port, $timeout)
{
    // https://stackoverflow.com/questions/1239068/ping-site-and-return-result-in-php
    $ts_start = microtime(true); 
    $fP = @fSockOpen($host, $port, $errno, $errstr, $timeout); 
    if (!$fP) {
        return false;
    } 
    $ts_end = microtime(true);
    return round((($ts_end - $ts_start)*1000), 0); 
}

/**
 * Write output rows on file
 * @param $file
 * @param array|string $output
 */
function writeOutputArrayOnFile($file, $output)
{
    if (!file_exists($file)) {
        $content = date("Y-m-d H:i:s") . " - created file" . "\n";
        $fp = fopen($file, "w");
        fwrite($fp, $content);
        fclose($fp);
    }

    if (is_array($output) && count($output) > 0) {
        foreach ($output as $row) {
            $myrow = date("Y-m-d H:i:s") . " - " . $row . "\n";
            file_put_contents($file, $myrow, FILE_APPEND);
        }
    }

    if (is_string($output)) {
        $myrow = date("Y-m-d H:i:s") . " - " . $output . "\n";
        file_put_contents($file, $myrow, FILE_APPEND);
    }
}

/**
 * get used application enviroment for update ifc
 * @return String
 */
function get_used_version()
{
    $APPLICATION_ENV = null;
    $globalSettingsFile = APPLICATION_PATH . '/../Protected/globalSettings.sh';
    
    if (file_exists($globalSettingsFile)) {
        $file = fopen($globalSettingsFile, "r") or exit("Unable to open file!");
        //Output a line of the file until the end is reached
        while (!feof($file)) {
            $line = fgets($file);
            if (strpos($line, 'APPLICATION_ENV=') !== false) {
                $APPLICATION_ENV = trim(str_replace('APPLICATION_ENV=', '', $line));
                //break;
            }
        }
        fclose($file);
    }
    return $APPLICATION_ENV;
}

/**
 * list of system supported language code
 * @return Array
 */
function language_code_list()
{
     $lang_code_arr = array(
                        'aa_DJ'  => 'Afar - Djibouti',
                        'aa_ER'  => 'Afar - Eritrea',
                        'aa_ET'  => 'Afar - Ethiopia',
                        'af_ZA'  => 'Afrikaans - South Africa',
                        'ak_GH'  => 'Akan',
                        'am_ET'  => 'Amharic - Ethiopia',
                        'an_ES'  => 'Aragonese - Spain',
                        'anp_IN' => 'Angika',
                        'ar_AE'  => 'Arabic - United Arab Emirates',
                        'ar_BH'  => 'Arabic - Bahrain',
                        'ar_DZ'  => 'Arabic - Algeria',
                        'ar_EG'  => 'Arabic - Egypt',
                        'ar_IN'  => 'Arabic - India',
                        'ar_IQ'  => 'Arabic - Iraq',
                        'ar_JO'  => 'Arabic - Jordan',
                        'ar_KW'  => 'Arabic - Kuwait',
                        'ar_LB'  => 'Arabic - Lebanon',
                        'ar_LY'  => 'Arabic - Libya',
                        'ar_MA'  => 'Arabic - Morocco',
                        'ar_OM'  => 'Arabic - Oman',
                        'ar_QA'  => 'Arabic - Qatar',
                        'ar_SA'  => 'Arabic - Saudi Arabia',
                        'ar_SD'  => 'Arabic - Sudan',
                        'ar_SS'  => 'Arabic',
                        'ar_SY'  => 'Arabic - Syria',
                        'ar_TN'  => 'Arabic - Tunisia',
                        'ar_YE'  => 'Arabic - Yemen',
                        'as_IN'  => 'Assamese - India',
                        'ast_ES' => 'Asturian - Spain',
                        'ayc_PE' => 'Ayc',
                        'az_AZ'  => 'Azerbaijani - Azerbaijan',
                        'be_BY'  => 'Belarusian - Belarus',
                        'bem_ZM' => 'Bemba - Zambia',
                        'ber_DZ' => 'Berber - Algeria',
                        'ber_MA' => 'Berber - Morocco',
                        'bg_BG'  => 'Bulgarian - Bulgaria',
                        'bho_IN' => 'Bhojpuri',
                        'bn_BD'  => 'Bengali - Bangladesh',
                        'bn_IN'  => 'Bengali - India',
                        'bo_CN'  => 'Tibetan - China',
                        'bo_IN'  => 'Tibetan - India',
                        'br_FR'  => 'Breton - France',
                        'brx_IN' => 'Bodo',
                        'bs_BA'  => 'Bosnian - Bosnia and Herzegovina',
                        'byn_ER' => 'Blin - Eritrea',
                        'ca_AD'  => 'Catalan - Andorra',
                        'ca_ES'  => 'Catalan - Spain',
                        'ca_FR'  => 'Catalan - France',
                        'ca_IT'  => 'Catalan - Italy',
                        'cmn_TW' => 'cmn',
                        'crh_UA' => 'Crimean Turkish - Ukraine',
                        'csb_PL' => 'Kashubian - Poland',
                        'cs_CZ'  => 'Czech - Czech Republic',
                        'cv_RU'  => 'Chuvash - Russia',
                        'cy_GB'  => 'Welsh - United Kingdom',
                        'da_DK'  => 'Danish - Denmark',
                        'de_AT'  => 'German - Austria',
                        'de_BE'  => 'German - Belgium',
                        'de_CH'  => 'German - Switzerland',
                        'de_DE'  => 'German - Germany',
                        'de_LI'  => 'German - Liechtenstein',
                        'de_LU'  => 'German - Luxembourg',
                        'doi_IN' => 'Dogri',
                        'dv_MV'  => 'Divehi - Maldives',
                        'dz_BT'  => 'Dzongkha - Bhutan',
                        'el_CY'  => 'Greek - Cyprus',
                        'el_GR'  => 'Greek - Greece',
                        'en_AG'  => 'English - Antigua and Barbuda',
                        'en_AU'  => 'English - Australia',
                        'en_BW'  => 'English - Botswana',
                        'en_CA'  => 'English - Canada',
                        'en_DK'  => 'English - Denmark',
                        'en_GB'  => 'English - United Kingdom',
                        'en_HK'  => 'English - Hong Kong SAR China',
                        'en_IE'  => 'English - Ireland',
                        'en_IN'  => 'English - India',
                        'en_NG'  => 'English - Nigeria',
                        'en_NZ'  => 'English - New Zealand',
                        'en_PH'  => 'English - Philippines',
                        'en_SG'  => 'English - Singapore',
                        'en_US'  => 'English - United States',
                        'en_ZA'  => 'English - South Africa',
                        'en_ZM'  => 'English - Zambia',
                        'en_ZW'  => 'English - Zimbabwe',
                        'es_AR'  => 'Spanish - Argentina',
                        'es_BO'  => 'Spanish - Bolivia',
                        'es_CL'  => 'Spanish - Chile',
                        'es_CO'  => 'Spanish - Colombia',
                        'es_CR'  => 'Spanish - Costa Rica',
                        'es_CU'  => 'Spanish',
                        'es_DO'  => 'Spanish - Dominican Republic',
                        'es_EC'  => 'Spanish - Ecuador',
                        'es_ES'  => 'Spanish - Spain',
                        'es_GT'  => 'Spanish - Guatemala',
                        'es_HN'  => 'Spanish - Honduras',
                        'es_MX'  => 'Spanish - Mexico',
                        'es_NI'  => 'Spanish - Nicaragua',
                        'es_PA'  => 'Spanish - Panama',
                        'es_PE'  => 'Spanish - Peru',
                        'es_PR'  => 'Spanish',
                        'es_PY'  => 'Spanish - Paraguay',
                        'es_SV'  => 'Spanish - El Salvador',
                        'es_US'  => 'Spanish - United States',
                        'es_UY'  => 'Spanish - Uruguay',
                        'es_VE'  => 'Spanish - Venezuela',
                        'et_EE'  => 'Estonian - Estonia',
                        'eu_ES'  => 'Basque - Spain',
                        'eu_FR'  => 'Basque - France',
                        'fa_IR'  => 'Persian - Iran',
                        'ff_SN'  => 'Fulah - Senegal',
                        'fi_FI'  => 'Finnish - Finland',
                        'fil_PH' => 'Filipino - Philippines',
                        'fo_FO'  => 'Faroese - Faroe Islands',
                        'fr_BE'  => 'French - Belgium',
                        'fr_CA'  => 'French - Canada',
                        'fr_CH'  => 'French - Switzerland',
                        'fr_FR'  => 'French - France',
                        'fr_LU'  => 'French - Luxembourg',
                        'fur_IT' => 'Friulian - Italy',
                        'fy_DE'  => 'Western Frisian - Germany',
                        'fy_NL'  => 'Western Frisian - Netherlands',
                        'ga_IE'  => 'Irish - Ireland',
                        'gd_GB'  => 'Scottish Gaelic - United Kingdom',
                        'gez_ER' => 'Geez - Eritrea',
                        'gez_ET' => 'Geez - Ethiopia',
                        'gl_ES'  => 'Galician - Spain',
                        'gu_IN'  => 'Gujarati - India',
                        'gv_GB'  => 'Manx - United Kingdom',
                        'hak_TW' => 'Hak',
                        'ha_NG'  => 'Hausa - Nigeria',
                        'he_IL'  => 'Hebrew - Israel',
                        'hi_IN'  => 'Hindi - India',
                        'hne_IN' => 'Hne',
                        'hr_HR'  => 'Croatian - Croatia',
                        'hsb_DE' => 'Upper Sorbian - Germany',
                        'ht_HT'  => 'Haitian - Haiti',
                        'hu_HU'  => 'Hungarian - Hungary',
                        'hy_AM'  => 'Armenian - Armenia',
                        'ia_FR'  => 'Interlingua',
                        'id_ID'  => 'Indonesian - Indonesia',
                        'ig_NG'  => 'Igbo - Nigeria',
                        'ik_CA'  => 'Inupiaq - Canada',
                        'is_IS'  => 'Icelandic - Iceland',
                        'it_CH'  => 'Italian - Switzerland',
                        'it_IT'  => 'Italian - Italy',
                        'iu_CA'  => 'Inuktitut - Canada',
                        'iw_IL'  => 'Hebrew - Israel',
                        'ja_JP'  => 'Japanese - Japan',
                        'ka_GE'  => 'Georgian - Georgia',
                        'kk_KZ'  => 'Kazakh - Kazakhstan',
                        'kl_GL'  => 'Kalaallisut - Greenland',
                        'km_KH'  => 'Khmer - Cambodia',
                        'kn_IN'  => 'Kannada - India',
                        'kok_IN' => 'Konkani - India',
                        'ko_KR'  => 'Korean - South Korea',
                        'ks_IN'  => 'Kashmiri - India',
                        'ku_TR'  => 'Kurdish - Turkey',
                        'kw_GB'  => 'Cornish - United Kingdom',
                        'ky_KG'  => 'Kirghiz - Kyrgyzstan',
                        'lb_LU'  => 'Luxembourgish',
                        'lg_UG'  => 'Ganda - Uganda',
                        'li_BE'  => 'Limburgish - Belgium',
                        'lij_IT' => 'lij',
                        'li_NL'  => 'Limburgish - Netherlands',
                        'lo_LA'  => 'Lao - Laos',
                        'lt_LT'  => 'Lithuanian - Lithuania',
                        'lv_LV'  => 'Latvian - Latvia',
                        'lzh_TW' => 'lzh',
                        'mag_IN' => 'Magahi',
                        'mai_IN' => 'Maithili - India',
                        'mg_MG'  => 'Malagasy - Madagascar',
                        'mhr_RU' => 'mhr',
                        'mi_NZ'  => 'Maori - New Zealand',
                        'mk_MK'  => 'Macedonian - Macedonia',
                        'ml_IN'  => 'Malayalam - India',
                        'mni_IN' => 'Manipuri',
                        'mn_MN'  => 'Mongolian - Mongolia',
                        'mr_IN'  => 'Marathi - India',
                        'ms_MY'  => 'Malay - Malaysia',
                        'mt_MT'  => 'Maltese - Malta',
                        'my_MM'  => 'Burmese - Myanmar [Burma)',
                        'nan_TW' => 'nan',
                        'nb_NO'  => 'Norwegian Bokmål - Norway',
                        'nds_DE' => 'Low German - Germany',
                        'nds_NL' => 'Low German - Netherlands',
                        'ne_NP'  => 'Nepali - Nepal',
                        'nhn_MX' => 'nhn',
                        'niu_NU' => 'Niuean',
                        'niu_NZ' => 'Niuean',
                        'nl_AW'  => 'Dutch - Aruba',
                        'nl_BE'  => 'Dutch - Belgium',
                        'nl_NL'  => 'Dutch - Netherlands',
                        'nn_NO'  => 'Norwegian Nynorsk - Norway',
                        'nr_ZA'  => 'South Ndebele - South Africa',
                        'nso_ZA' => 'Northern Sotho - South Africa',
                        'oc_FR'  => 'Occitan - France',
                        'om_ET'  => 'Oromo - Ethiopia',
                        'om_KE'  => 'Oromo - Kenya',
                        'or_IN'  => 'Oriya - India',
                        'os_RU'  => 'Ossetic - Russia',
                        'pa_IN'  => 'Punjabi - India',
                        'pap_AN' => 'Papiamento - Netherlands Antilles',
                        'pap_AW' => 'Papiamento',
                        'pap_CW' => 'Papiamento',
                        'pa_PK'  => 'Punjabi - Pakistan',
                        'pl_PL'  => 'Polish - Poland',
                        'ps_AF'  => 'Pashto - Afghanistan',
                        'pt_BR'  => 'Portuguese - Brazil',
                        'pt_PT'  => 'Portuguese - Portugal',
                        'quz_PE' => 'quz',
                        'ro_RO'  => 'Romanian - Romania',
                        'ru_RU'  => 'Russian - Russia',
                        'ru_UA'  => 'Russian - Ukraine',
                        'rw_RW'  => 'Kinyarwanda - Rwanda',
                        'sa_IN'  => 'Sanskrit - India',
                        'sat_IN' => 'Santali',
                        'sc_IT'  => 'Sardinian - Italy',
                        'sd_IN'  => 'Sindhi - India',
                        'se_NO'  => 'Northern Sami - Norway',
                        'shs_CA' => 'shs',
                        'sid_ET' => 'Sidamo - Ethiopia',
                        'si_LK'  => 'Sinhala - Sri Lanka',
                        'sk_SK'  => 'Slovak - Slovakia',
                        'sl_SI'  => 'Slovenian - Slovenia',
                        'so_DJ'  => 'Somali - Djibouti',
                        'so_ET'  => 'Somali - Ethiopia',
                        'so_KE'  => 'Somali - Kenya',
                        'so_SO'  => 'Somali - Somalia',
                        'sq_AL'  => 'Albanian - Albania',
                        'sq_MK'  => 'Albanian - Macedonia',
                        'sr_ME'  => 'Serbian - Montenegro',
                        'sr_RS'  => 'Serbian - Serbia',
                        'ss_ZA'  => 'Swati - South Africa',
                        'st_ZA'  => 'Southern Sotho - South Africa',
                        'sv_FI'  => 'Swedish - Finland',
                        'sv_SE'  => 'Swedish - Sweden',
                        'sw_KE'  => 'Swahili - Kenya',
                        'sw_TZ'  => 'Swahili - Tanzania',
                        'szl_PL' => 'szl',
                        'ta_IN'  => 'Tamil - India',
                        'ta_LK'  => 'Tamil',
                        'te_IN'  => 'Telugu - India',
                        'tg_TJ'  => 'Tajik - Tajikistan',
                        'the_NP' => 'the',
                        'th_TH'  => 'Thai - Thailand',
                        'ti_ER'  => 'Tigrinya - Eritrea',
                        'ti_ET'  => 'Tigrinya - Ethiopia',
                        'tig_ER' => 'Tigre - Eritrea',
                        'tk_TM'  => 'Turkmen - Turkmenistan',
                        'tl_PH'  => 'Tagalog - Philippines',
                        'tn_ZA'  => 'Tswana - South Africa',
                        'tr_CY'  => 'Turkish - Cyprus',
                        'tr_TR'  => 'Turkish - Turkey',
                        'ts_ZA'  => 'Tsonga - South Africa',
                        'tt_RU'  => 'Tatar - Russia',
                        'ug_CN'  => 'Uighur - China',
                        'uk_UA'  => 'Ukrainian - Ukraine',
                        'unm_US' => 'unm',
                        'ur_IN'  => 'Urdu - India',
                        'ur_PK'  => 'Urdu - Pakistan',
                        'uz_UZ'  => 'Uzbek - Uzbekistan',
                        've_ZA'  => 'Venda - South Africa',
                        'vi_VN'  => 'Vietnamese - Vietnam',
                        'wa_BE'  => 'Walloon - Belgium',
                        'wae_CH' => 'Walser',
                        'wal_ET' => 'Wolaytta',
                        'wo_SN'  => 'Wolof - Senegal',
                        'xh_ZA'  => 'Xhosa - South Africa',
                        'yi_US'  => 'Yiddish - United States',
                        'yo_NG'  => 'Yoruba - Nigeria',
                        'yue_HK' => 'Cantonese',
                        'zh_CN'  => 'Chinese - China',
                        'zh_HK'  => 'Chinese - Hong Kong SAR China',
                        'zh_SG'  => 'Chinese - Singapore',
                        'zh_TW'  => 'Chinese - Taiwan',
                        'zu_ZA'  => 'Zulu - South Africa');
     return $lang_code_arr;
}
