<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends CI_Controller
{
    /**
     * Default values for data array
     * @var array
     */
    private $_data = array(
        'msg'       => '',
        'msg_class' => '',
        'update'    => 0
    );

    /**
     * language locale folder path
     */
    private $language_path = APPLICATION_PATH . '/../write-dir/bbweb/language/';

    /**
     * Settings array values that come from bb_settings table
     * @var array
     */
    private $_settings = array();
    
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_settings', '', true);
        $this->load->helper(array('form',  'url'));
        $this->load->library('Gettext_lib');
        $this->load->model('mdl_dashboard', '', true);
        $this->user_details = $this->session->userdata('user');
        $this->_role_id = $this->user_details['roles'];
        // GET settings from bb_settings table
        $this->_settings = $this->mdl_settings->get_settings();
        // get settings from bb_settings table
        $this->_data['settings'] = $this->_settings;
    }

    public function index()
    {
        // set default data values
        $data = $this->_data;
        $role_id = $this->_role_id;
        // filter data comes from FORM
        $SETTINGS = filter_input(INPUT_POST, "SETTINGS", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $SETTINGS_DEFAULT = filter_input(INPUT_POST, "SETTINGS_DEFAULT", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        
        if (!is_null($SETTINGS)) {
            
            $data['update'] = 1;
            $update_arr = array();
            
            // set validation rules
            $this->_addValidationForSettings($SETTINGS);
            
            if ($this->form_validation->run() != false) {

                foreach ($SETTINGS as $var_code => $value) {

                    // Set value and remove if value is same as default value.
                    $value_default = $this->_getValSetting($SETTINGS_DEFAULT, $var_code);
                    if ($value) {
                        $value = ($value != $value_default) ? $value : '' ;
                    }

                    $update_arr[] = array('var_code' => $var_code, 'value' => $value, 'date_update' => date('Y-m-d H:i:s'));
                }

                $response = $this->mdl_settings->update_batch($update_arr, 'var_code');
                
                // set Class and Msg for response
                $data['msg_class'] = $this->_getClassForResponse($response);
                $data['msg']       = _($this->_getMsgForResponse($response));
                if ($data['msg_class'] === 'alert-danger') {
                    $insert_message = array(
                            'role_id' => $role_id,
                            'category' => 'settings',
                            'descrizione' => 'Failed to Update General Settings',
                            'type' => 'service'
                        );
                    $return_val = $this->passed_messages($insert_message);
                }
                // GET again settings after updating them
                $data['settings'] = $this->mdl_settings->get_settings();
            }
        }
        // load custom view
        load_views('settings/general', $data);
    }

    public function kpi()
    {
        // set default data values
        $data = $this->_data;
        $role_id = $this->_role_id;
        // filter data comes from FORM
        $SETTINGS = filter_input(INPUT_POST, "SETTINGS", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $SETTINGS_DEFAULT = filter_input(INPUT_POST, "SETTINGS_DEFAULT", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        
        if (!is_null($SETTINGS)) {
            
            $data['update'] = 1;
            $update_arr = array();

            // set validation rules
            $this->_addValidationForSettings($SETTINGS);
            
            if ($this->form_validation->run() != false) {

                foreach ($SETTINGS as $var_code => $value) {

                    // Set value and remove if value is same as default value.
                    $value_default = $this->_getValSetting($SETTINGS_DEFAULT, $var_code);
                    if ($value) {
                        if (is_array($value)) {
                            /* KPI Graph Thresholds. */
                            // Check if edited.
                            $flag_update = false;
                            $i = 0;
                            foreach ($value as $item) {
                                if (is_array($item) && isset($item['V']) && $item['V']) {
                                    $flag_update = true;
                                    if ($item['V'] == $value_default[$i]['V']) {
                                        $value[$i]['V'] = '';
                                    }
                                }
                                $i++;
                            }

                            // Serialize new values.
                            $value = ($flag_update) ? serialize($value) : '' ;

                        } else {
                            $value = ($value != $value_default) ? $value : '' ;
                        }

                        $update_arr[] = array('var_code' => $var_code, 'value' => $value, 'date_update' => date('Y-m-d H:i:s'));
                    }
                }

                $response = $this->mdl_settings->update_batch($update_arr, 'var_code');
                
                // set Class and Msg for response
                $data['msg_class'] = $this->_getClassForResponse($response);
                $data['msg']       = _($this->_getMsgForResponse($response));
                if ($data['msg_class'] === 'alert-danger') {
                    $insert_message = array(
                            'role_id' => $role_id,
                            'category' => 'settings',
                            'descrizione' => 'Failed to update KPI Settings',
                            'type' => 'service'
                        );
                    $return_val = $this->passed_messages($insert_message);
                }
                // GET again settings after updating them
                $data['settings'] = $this->mdl_settings->get_settings();
            }
        }
        load_views('settings/kpi', $data);
    }

    public function networking()
    {
        // set default data values
        $data = $this->_data;
        $role_id = $this->_role_id;
        // filter data comes from FORM
        $SETTINGS = filter_input(INPUT_POST, "SETTINGS", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $SETTINGS_DEFAULT = filter_input(INPUT_POST, "SETTINGS_DEFAULT", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        
        if (!is_null($SETTINGS)) {
            
            $data['update'] = 1;
            $update_arr = array();

            //  if client_name is NOT empty call the remote procedure
            if ($SETTINGS['SYSTEM_CLIENT_NAME'] != SYSTEM_CLIENT_NAME) {
                $this->form_validation->set_rules('SETTINGS[SYSTEM_CLIENT_NAME]', 'Client Name', 'required|xss_clean|callback_customAlpha');
            }

            // if networking is NOT empty call local reboot
            if (($SETTINGS['SYSTEM_IP'] != SYSTEM_IP) || ($SETTINGS['SYSTEM_NETMASK'] != SYSTEM_NETMASK) || ($SETTINGS['SYSTEM_GATEWAY'] != SYSTEM_GATEWAY)) {
                $this->form_validation->set_rules('SETTINGS[SYSTEM_IP]', 'IP', 'required|xss_clean');
                $this->form_validation->set_rules('SETTINGS[SYSTEM_NETMASK]', 'NETMASK', 'required|xss_clean');
                $this->form_validation->set_rules('SETTINGS[SYSTEM_GATEWAY]', 'GATEWAY', 'required|xss_clean');
            }

            // custom error message
            $this->form_validation->set_message('customAlpha', 'Should only contain alpha numeric and dash');

            if ($this->form_validation->run() != false) {

                foreach ($SETTINGS as $var_code => $value) {

                    $value_default = $this->_getValSetting($SETTINGS_DEFAULT, $var_code);
                    if ($value) {
                        if ($value != $value_default) {
                            $update_arr[] = array('var_code' => $var_code, 'value' => $value, 'date_update' => date('Y-m-d H:i:s'));
                        }
                    }
                }

                if ($update_arr) {

                    //  if client_name is NOT empty call the remote procedure
                    if ($SETTINGS['SYSTEM_CLIENT_NAME'] != SYSTEM_CLIENT_NAME) {

                        $data['user'] = array();
                        $data['user']['action']         = 'update_client_name';
                        $data['user']['serial_number']  = $SETTINGS['SYSTEM_SERIAL_NUMBER'];
                        $data['user']['client_name']    = $SETTINGS['SYSTEM_CLIENT_NAME'];
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, BBWEB_IFC_URL."update.php");
                        // Use HTTPS connection to ifc-server
                        curl_setopt($ch, CURLOPT_CAINFO, APPLICATION_PATH . "/../utilities/ssl/cacert.pem");
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                        // set POST data
                        curl_setopt($ch, CURLOPT_POST, count($data['user']));
                        // in real life you should use something like:
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data['user']));
                        // receive server response ...
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $server_output = curl_exec($ch);
                        curl_close($ch);
                        $response = json_decode($server_output, true);
                        if ($response && $response['error'] == 0) {

                            $response = $this->mdl_settings->update_batch($update_arr, 'var_code');
                        } else {
                            $data['msg_class'] = $this->_getClassForResponse(false);
                            $data['msg'] = $response['message'];
                        }
                    }


                    // if networking is NOT empty call local reboot
                    if (($SETTINGS['SYSTEM_IP'] != SYSTEM_IP) || ($SETTINGS['SYSTEM_NETMASK'] != SYSTEM_NETMASK) || ($SETTINGS['SYSTEM_GATEWAY'] != SYSTEM_GATEWAY)) {

                        /**
                         * call configNetwork.sh to save ip, netmask and gateway
                         */
                        $command = "sudo ".APPLICATION_PATH . "/../utilities/openvpn/configNetwork.sh"
                                . " --system_ip '".$SETTINGS['SYSTEM_IP']."'"
                                . " --system_netmask '".$SETTINGS['SYSTEM_NETMASK']."'"
                                . " --system_gateway '".$SETTINGS['SYSTEM_GATEWAY']."'";
                        //die;
                        exec($command, $command_output, $command_return);

                        if ($command_return != 0) {
                            echo "<h2>output:</h2>";
                            print_r($command_output);
                            echo "<h2>return:</h2>";
                            print_r($command_return);

                        } else {

                            // prepare the response
                            $json_data = getJSON($command_output);
                            $return['json_response'] = serialize($command_output);

                            if ($json_data['error']!=0) {
                                $data['msg_class'] = "alert-danger";
                                $data['msg'] = $json_data["message"];

                                $insert_message = array(
                                        'role_id' => $role_id,
                                        'category' => 'settings',
                                        'descrizione' => 'Failed to Set NetWorking Settings',
                                        'type' => 'service'
                                    );
                                $return_val = $this->passed_messages($insert_message);
                            } else {
                                $response = $this->mdl_settings->update_batch($update_arr, 'var_code');
                                /**
                                 * @todo: we need to create open url for testing if system is active or now.
                                 * To check if system is boot up after reboot.
                                 * Also, need to work on jsonp call for crossdomain issue.
                                 */
                                $system_oldip = SYSTEM_IP;
                                redirect(base_url('settings/reboot?autostart=1&system_oldip='.$system_oldip));
                                exit;
                            }
                        }

                    }
                }

                $data['settings'] = $this->mdl_settings->get_settings();
            }
        }

        load_views('settings/networking', $data);
    }

    /**
     * Device Reboot
     * @return [type] [description]
     */
    public function reboot()
    {
        if (isset($_POST['start']) && $_POST['start']) {
            $return = array();

            // Reboot started or not
            if (isset($_POST['reboot']) && $_POST['reboot']) {
                $return['status'] = 'stop';
                $return['message'] = _('Your device successfully rebooted..');
            } else {
                // Fire command for reboot device
                $command = "sudo reboot";
                exec($command, $command_output, $command_return);
                $return['status'] = 'continue';
            }
            echo json_encode($return);
            die;
        } else {

            $data = array();

            if (isset($_GET['msg']) && $_GET['msg']) {
                if (isset($_GET['result']) && $_GET['result']) {
                    switch ($_GET['result']) {
                        case 'success':
                            $data['msg_class'] = "alert-success";
                            break;

                        default:
                            $data['msg_class'] = "alert-danger";
                            break;
                    }
                }
                $data['msg'] = urldecode($_GET['msg']);
            }

            load_views('settings/reboot', $data);
        }
    }

    /**
     * Parlour Configurator
     * Costruzione Sala , Modifica Sala esistente
     */
    public function pconfigurator()
    {
        $data = array();
        $pconfigurator = array();
        $numero_milknet = '';
        $role_id = $this->_role_id;
        if (isset($_POST['data_sala']) && $_POST['data_sala']) {
            $deviceWithRooms = json_decode(urldecode($_POST['data_sala']), true);
            $response = $this->mdl_settings->pconfigurator($deviceWithRooms);
            if (!$response) {
                $insert_message = array(
                        'role_id' => $role_id,
                        'category' => 'settings',
                        'descrizione' => 'Failed to Set Pconfigurator',
                        'type' => 'service'
                    );
                $return_val = $this->passed_messages($insert_message);
            }
            redirect('settings/pconfigurator_callback');
            exit;
        }
        $data = $this->mdl_settings->select_pconfigurator();
        foreach ($data as $k => $v) {
            $data[$k]['tipoSala'] = $data[$k]['tipo_sala'];
            $data[$k]['ip'] = $data[$k] ['IP'];
            unset($data[$k]['tipo_sala']);
            unset($data[$k]['IP']);
        }
        $json['data'] = json_encode($data);
        //var_dump($json['data']);

        //load_views('settings/pconfigurator', $data);
        load_views('settings/pconfigurator', $json);
    }

    public function pconfigurator_callback()
    {
        $data = array();
        $json = array();

        if (isset($_POST['data']) && $_POST['data']) {
            $data = json_decode($_POST['data'], true);
            $result = $this->mdl_settings->pconfigurator($data);
        }

        $data = $this->mdl_settings->select_pconfigurator();
        foreach ($data as $k => $v) {
            $data[$k]['tipoSala'] = $data[$k]['tipo_sala'];
            $data[$k]['ip'] = $data[$k] ['IP'];
            unset($data[$k]['tipo_sala']);
            unset($data[$k]['IP']);
        }
        $json['data'] = json_encode($data);
        load_views('settings/pconfigurator_callback', $json);
    }

    public function psubmit()
    {
        $data = array();
        load_views('settings/psubmit', $data);
    }

    /**
     * Return the right callback based on type_value
     * @param string $type
     * @return string
     */
    private function _getCallbackCheck($type)
    {
        switch ($type) {
            case 'URL':
                return 'callback_url_check';
            case 'STRING':
                return 'callback_string_check';
            case 'PATH':
                return 'callback_path_check';
            case 'INT':
                return 'callback_int_check';
            default:
                return '';
        }
    }
    
    /**
     * Return the value, it check for serialized data
     * @param array $ar
     * @param string $var
     * @return mixed
     */
    private function _getValSetting($ar, $var)
    {
        $value = '';
        if (isset($ar[$var])) {
            if (is_serialize($ar[$var])) {
                $value = unserialize($ar[$var]);
            } else {
                $value = $ar[$var];
            }
        }
        return $value;
    }
    
    /**
     * Return the class for response
     * @param boolean $r
     * @return string
     */
    private function _getClassForResponse($r)
    {
        return ($r) ?  'alert-success' : 'alert-danger';
    }
    
    /**
     * Return the message for response
     * @param boolean $r
     * @return string
     */
    private function _getMsgForResponse($r)
    {
        return ($r) ?  'Successfully updated.' : 'Something goes wrong please try again.';
    }
    
    /**
     * Create the right validation rules for settings
     * @param array $SETTINGS The settings come from POST data
     * @return void
     */
    private function _addValidationForSettings($SETTINGS)
    {
        // init validation procedure
        foreach ($this->_settings as $value) {
            $var_code = $value["var_code"];
            // add validation ONLY for the fields come from FORM
            if (isset($SETTINGS[$var_code])) {
                $this->form_validation->set_rules("SETTINGS[$var_code]", $var_code, $this->_getCallbackCheck($value['type_values']));
            }
        }
    }
    
    
    
    public function url_check($str)
    {
        if ($str) {
            if (!filter_var($str, FILTER_VALIDATE_URL) === false) {
                return true;
            } else {
                $this->form_validation->set_message('url_check', $str.' is not valid url');
                return false;
            }
        } else {
            return true;
        }
    }

    public function string_check($str)
    {
        if ($str) {
            if (is_string($str)) {
                return true;
            } else {
                $this->form_validation->set_message('string_check', $str.' is not valid string');
                return false;
            }
        } else {
            return true;
        }
    }

    public function int_check($str)
    {
        if ($str) {
            if (is_int($str) || is_numeric($str)) {
                return true;
            } else {
                $this->form_validation->set_message('int_check', $str.' is not valid integer');
                return false;
            }
        } else {
            return true;
        }
    }

    public function path_check($str)
    {
        if ($str) {
            if (preg_match('#^(/.*)$#', $str)) {
                return true;
            } else {
                 $this->form_validation->set_message('path_check', $str.' is not valid path');
                return false;
            }
        } else {
                return true;
        }
    }

    // callback function
    public function customAlpha($str)
    {
        if (!preg_match('/^[a-z0-9\-]+$/i', $str)) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * Languages insert update and delete language
     * @return Array
     * @todo optimize function
     */
    public function languages()
    {
        $this->check_dir_exists($this->language_path);
        $data = array();
        $data['all_lang'] = $this->mdl_settings->select_all_languages();

        //scan for insert new strings
        //@todo : Rearrange and optimize code
        if (isset($_GET['scan']) && isset($_GET['idlang'])) {
            $lang_id      = $_GET['idlang'];
            $lang_data    = $this->mdl_settings->data_by_lang_id($lang_id);
            $lang_code    = $lang_data->code;
            $lang_name    = $lang_data->lang;
            
            $db_lang_data = array();
            $old_data     = $this->mdl_settings->select_language_data_byid($lang_id);
            foreach ($old_data as $value) {
                $db_lang_data[] = $value['msgid'];
            }
            
            $directory    = APPLICATION_PATH.'/application/';
            $gettext      = new Gettext_lib();
            $lines        = $gettext->scan_dir($directory);
            if (!empty($lines)) {
                $directory        = APPLICATION_PATH.'/assets/';
                $lines_assets     = $gettext->scan_dir($directory);
                $final_lines      = array_unique(array_merge($lines, $lines_assets));
                $final_lines_diff = array_diff($final_lines, $db_lang_data);
                if ($final_lines_diff) {
                    $path      = $this->language_path.'locale/'.$lang_name.'/'.$lang_code.'/LC_MESSAGES/';
                    $originals = glob($path."*.{mo,po}", GLOB_BRACE);
                    if ($originals) {
                        foreach ($originals as $file) {
                            unlink($file);
                        }
                    }
                    $this->check_dir_exists($path);
                    $mtime        = strtotime("now");
                    $file_mo_path = $path.'default_'.$mtime.'.mo';
                    $file_po_path = $path.'default.po';
                    $db_mo_path   = $lang_name.'/'.$lang_code.'/LC_MESSAGES/default_'.$mtime.'.mo';
                    $response     = $this->mdl_settings->insert_language_data($final_lines_diff, $lang_code, $lang_id, false, true);
                    $new_data     = $this->mdl_settings->select_language_data_byid($lang_id);
                    $write_arr    = array();
                    foreach ($new_data as $value) {
                        $write_arr[$value['msgid']] = $value['msgstr'];
                    }
                    $fp = fopen($file_po_path, "w");
                    fclose($fp);
                    $file = fopen($file_po_path, "r+") or exit("Unable to open file!");
                    $data =   "msgid \"\"\nmsgstr \"\"\n"
                        . "\"Project-Id-Version: bbweb\\n\"\n"
                        . "\"POT-Creation-Date: ".date("y-m-d H:i:s")."\\n\"\n"
                        . "\"PO-Revision-Date: ".date("y-m-d H:i:s")."\\n\"\n"
                        . "\"Last-Translator: \\n\"\n"
                        . "\"Language-Team: \\n\"\n"
                        . "\"Language: ".$lang_code."\\n\"\n"
                        . "\"Content-Type: text/plain; charset=UTF-8\\n\"\n"
                        . "\"MIME-Version: 1.0\\n\"\n"
                        . "\"Content-Transfer-Encoding: 8bit\\n\"\n";
                    fwrite($file, $data."\n");
                    foreach ($write_arr as $key => $value) {
                        fwrite($file, 'msgid "' . $key . '"' . "\n" . 'msgstr "'.$value.'"' . "\n\n");
                    }
                    fclose($file);
                    $fp = fopen($file_mo_path, "w");
                    fclose($fp);
                    exec('msgfmt '.$file_po_path.' -o '.$file_mo_path);
                    $this->mdl_settings->update_language_path($lang_id, $db_mo_path);
                }
                redirect(base_url('settings/languages?msg=Successfully Scanned&msg_class=alert-success'));
            }

        }

        //add new language
        if (isset($_POST['next']) && isset($_POST['lang_code']) && isset($_POST['lang_name'])) {
            $this->form_validation->set_rules('lang_code', 'Language Code', 'required|xss_clean|callback_lang_code_check');
            $this->form_validation->set_rules('lang_name', 'Language Name', 'required|xss_clean');
            if ($this->form_validation->run() === true) {
                $new_language  = $_POST['lang_code'];
                $new_lang_name = $_POST['lang_name'];
                if ($this->mdl_settings->check_lang_name_exists($new_lang_name)) {
                    $data['msg_class'] = "alert-danger";
                    $data['msg']       = _('langauge name is already used');
                } else {
                    $path         = $this->language_path.'locale/'.$new_lang_name.'/'.$new_language.'/LC_MESSAGES/';
                    $this->check_dir_exists($path);
                    $mtime        = strtotime("now");
                    $file_mo_path = $path.'default_'.$mtime.'.mo';
                    $file_po_path = $path.'default.po';
                    $filename     = $new_lang_name.'/'.$new_language.'/LC_MESSAGES/'.'default_'.$mtime.'.mo';
                    $return       = $this->mdl_settings->insert_language($new_language, $new_lang_name, $filename, 'insert_language');
                    if ($return) {
                        $response       = $this->new_language_files($file_po_path, $file_mo_path, $new_language, $return);
                        if ($response) {
                            redirect('settings/language_modify?new_lang='.$new_language.'&lang_id='.$return.'&lang_name='.$new_lang_name);
                        } else {
                            $data['msg_class'] = "alert-danger";
                            $data['msg']       = _('Does Not insert');
                        }
                    } else {
                        $data['msg_class'] = "alert-danger";
                        $data['msg']       = _('Does Not insert');
                    }
                }
            }
        }

        //edit language
        if (isset($_POST['edit_lang']) && $_POST['edit_lang']) {
            $this->form_validation->set_rules('lang_code', 'Language Code', 'required|xss_clean|callback_lang_code_check');
            $this->form_validation->set_rules('lang_name', 'Language Name', 'required|xss_clean');
            if ($this->form_validation->run() === true) {
                $edited_code = $_POST['lang_code'];
                $edited_name = $_POST['lang_name'];
                $lang_id     = $_POST['lang_id'];
                $old_code    = $_GET['lang_code'];
                $old_name    = $_GET['lang_name'];
                $lang_data   = $this->mdl_settings->data_by_lang_id($lang_id);
                $stored_file = $lang_data->filename;
                $folders     = explode('/', $stored_file);
                $filepath    = $edited_name.'/'.$edited_code.'/LC_MESSAGES/'.$folders[3];
                if ($edited_name !== $old_name) {
                    if ($this->mdl_settings->check_lang_name_exists($edited_name)) {
                        $response = false;
                    } else {
                        $response = $this->mdl_settings->update_language($edited_code, $edited_name, $filepath, $lang_id);
                    }
                } else {
                    $response = $this->mdl_settings->update_language($edited_code, $edited_name, $filepath, $lang_id);
                }
                if ($response) {
                    if (file_exists($this->language_path.'locale/'.$old_name.'/'.$old_code)) {
                        rename($this->language_path.'locale/'.$old_name.'/', $this->language_path.'locale/'.$edited_name.'/');
                        rename($this->language_path.'locale/'.$edited_name.'/'.$old_code.'/', $this->language_path.'locale/'.$edited_name.'/'.$edited_code.'/');
                        $data['msg_class'] = "alert-success";
                        $data['msg']       = _('Selected Language Code Updated Successfully');
                        $data['updated']   = 1;
                    } else {
                        $data['msg_class'] = "alert-danger";
                        $data['msg']       = _('Does Not Rename Folder Name');
                    }
                } else {
                    $data['msg_class'] = "alert-danger";
                    $data['msg']       = _('langauge name is already used');
                }
            }
        }

        //delete language
        if (isset($_POST['delete'])) {
            if ($_POST['delete']) {
                $lang_id   = $_POST['delete'];
                $lang_data = $this->mdl_settings->data_by_lang_id($lang_id);
                $lang_code = $lang_data->code;
                $lang_name = $lang_data->lang;
                $response  = $this->mdl_settings->language_delete($lang_id);
                if ($response) {
                    $delete_data       = $this->deleteDirectory($this->language_path.'locale/'.$lang_name.'/');
                    if ($delete_data) {
                        $data['msg_class'] = "alert-success";
                        $data['msg']       = _("Successfully Deleted.");
                    } else {
                        $data['msg_class'] = "alert-danger";
                        $data['msg']       = _("Could Not Delete Files.");
                    }
                } else {
                    $data['msg_class'] = "alert-danger";
                    $data['msg']       = _("Something goes wrong please try again.");
                }
                echo json_encode($data);
                die;
            }
        }

        //uplaod .po file
        if (isset($_POST['submit']) && $_POST['submit']) {
            $this->form_validation->set_rules('upload_lang_name', 'Language Name', 'required|xss_clean');
            $this->form_validation->set_rules('upload_lang_code', 'Language Code', 'required|xss_clean|callback_lang_code_check');
            if ($this->form_validation->run() === true) {
                if (isset($_FILES['lang_file']['name']) && $_FILES['lang_file']['name']) {
                    $uploaded_lang_code = $_POST['upload_lang_code'];
                    $uploaded_lang_name = $_POST['upload_lang_name'];
                    $allowed            =  array('po');
                    $filename           = $_FILES['lang_file']['name'];
                    $ext                = pathinfo($filename, PATHINFO_EXTENSION);
                    if (!in_array($ext, $allowed)) {
                        $data['msg_class'] = "alert-danger";
                        $data['msg']       = _('Please upload .po file');
                    } else {
                        $path              = $this->language_path.'locale/'.$uploaded_lang_name.'/'.$uploaded_lang_code.'/LC_MESSAGES/';
                        $this->check_dir_exists($path);
                        $file_path         = $path.'default.po';
                        move_uploaded_file($_FILES['lang_file']['tmp_name'], $file_path);
                        $originals = glob($path."*.mo");
                        if ($originals) {
                            foreach ($originals as $file) {
                                unlink($file);
                            }
                        }
                        $mtime       = strtotime("now");
                        $mo_file     = $path.'default_'.$mtime.'.mo';
                        $stored_file = $uploaded_lang_name.'/'.$uploaded_lang_code.'/LC_MESSAGES/default_'.$mtime.'.mo';
                        $response    = $this->mdl_settings->insert_language($uploaded_lang_code, $uploaded_lang_name, $stored_file, 'uploaded_language');
                        if ($response) {
                            $return    = $this->generate_uploaded_po($file_path);
                            $_response = $this->mdl_settings->insert_language_data($return, $uploaded_lang_code, $response, true);
                            $fp = fopen($mo_file, "w");
                            fclose($fp);
                            exec('msgfmt '.$file_path.' -o '.$mo_file);
                            $data['msg_class'] = "alert-success";
                            $data['msg']       = 'Successfully Uploaded';
                            $default_lang      = ($this->user_details['roles'] == 1) ? 'en_SUI' : 'en_US';
                            $session_language  = (array_key_exists('language', $this->user_details) ) ? $this->user_details['language'] : $default_lang;
                            if ($response === $session_language) {
                                redirect(base_url('settings/languages?msg=Successfully Uploaded&msg_class=alert-success'));
                            }
                        } else {
                            $data['msg_class'] = "alert-danger";
                            $data['msg']       = _('Does Not insert');
                        }
                    }
                }
            }
        }
        $data['all_lang'] = $this->mdl_settings->select_all_languages();
        load_views('settings/languages', $data);
    }
    /**
     * lang_code_check description
     * @param  string $str
     * @return Boolean
     */
    public function lang_code_check($str)
    {
        if ($str) {
            if (preg_match('/([a-z])\w+_([A-Z])\w/', $str)) {
                return true;
            } else {
                $this->form_validation->set_message('lang_code_check', 'The '.$str.' is not valid language code');
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * language_modify insert translated  string of original string
     * @return Array
     * @todo optimize function
     */
    public function language_modify()
    {
        $data = array();
        $flag = false;
        $this->check_dir_exists($this->language_path);
        if (isset($_GET['new_lang']) && $_GET['new_lang']) {
            $data['new_lang'] = $_GET['new_lang'];
        }
        if (isset($_GET['lang_id']) && $_GET['lang_id']) {
            $data['lang_id'] = $_GET['lang_id'];
        }
        if (isset($_GET['lang_name']) && $_GET['lang_name']) {
            $data['lang_name'] = $_GET['lang_name'];
        }
        if (isset($_GET['is_default']) && $_GET['is_default']) {
            $data['is_default'] = $_GET['is_default'];
        }
        if (isset($data['new_lang']) && isset($data['lang_name'])) {
            $data['filename'] = $filename = $this->language_path.'locale/'.$data['lang_name'].'/'.$_GET['new_lang'].'/LC_MESSAGES/default.po';
        }
        if (isset($_POST['save']) && $_POST['save']) {
            $new_lang           = $_POST['new_lang'];
            $lang_id            = $_POST['lang_id'];
            $lang_name          = $_POST['lang_name'];
            $lang_data          = $this->mdl_settings->data_by_lang_id($lang_id);
            $stored_file        = $lang_data->filename;
            $translated_str_arr = $_POST['trans_lang'];
            $response           = $this->mdl_settings->translated_string($translated_str_arr, $new_lang, $filename, $lang_id);
            if ($response) {
                $path      = $this->language_path.'locale/'.$lang_name.'/'.$new_lang.'/LC_MESSAGES/';
                $originals = glob($path."*.mo");
                if ($originals) {
                    foreach ($originals as $file) {
                        unlink($file);
                    }
                }
                $mtime           = strtotime("now");
                $mo_file         = $path.'default_'.$mtime.'.mo';
                $new_stored_file = $lang_name.'/'.$new_lang.'/LC_MESSAGES/default_'.$mtime.'.mo';
                $response        = $this->mdl_settings->update_language_path($lang_id, $new_stored_file);
                $fp = fopen($mo_file, "w");
                fclose($fp);
                exec('msgfmt '.$filename.' -o '.$mo_file);
                $data['msg_class'] = "alert-success";
                $data['msg']       = _('Translated Successfully');
                $flag = true;
            } else {
                $data['msg_class'] = "alert-danger";
                $data['msg']       = _('Does Not insert');
            }
        }
        if (isset($_GET['po_file']) && $_GET['po_file']) {
            $filename     = $_GET['po_file'];
            $folders     = explode('/', $filename);
            $language    = $folders[9];
            if (file_exists($filename)) {
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=".$language.".po");
                header("Content-Type: text/plain");
                header("Content-Transfer-Encoding: binary");
                // read the file from disk
                readfile($filename);
                die;
            }
        }
        $data['all_lang_data'] = $this->mdl_settings->select_language_data_byid($data['lang_id']);
        if ($flag) {
            redirect(base_url('settings/language_modify?new_lang='.$new_lang.'&is_default='.$_GET['is_default'].'&lang_id='.$lang_id.'&msg=Translated Successfully'.'&msg_class=alert-success'));
        }
        load_views('settings/language_modify', $data);
    }
    /**
     * check_dir_exists is create dir if not exists
     * @return boolean
     */
    private function check_dir_exists($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        return true;
    }
    /**
     * new_language_files is generate .po and .mo for particular given language code
     * @param  string $path
     * @return Boolean
     */
    public function new_language_files($po_file, $mo_file, $lang_code, $lang_id)
    {
        $directory = APPLICATION_PATH.'/application/';
        $gettext   = new Gettext_lib();
        $lines     = $gettext->scan_dir($directory);
        $db_lang_data = array();
        if (!empty($lines)) {
            $directory    = APPLICATION_PATH.'/assets/';
            $lines_assets = $gettext->scan_dir($directory);
            $final_lines  = array_merge($lines, $lines_assets);
            $final_lines  = array_unique($final_lines);
            $response = $this->mdl_settings->insert_language_data($final_lines, $lang_code, $lang_id, false, false);
            if (!$response) {
                return false;
            }
            $file_po     = $gettext->create_po($final_lines, $po_file);
            $fp = fopen($mo_file, "w");
            fclose($fp);
            exec('msgfmt '.$po_file.' -o '.$mo_file);
            return true;
        }
        return false;
    }
    /**
     * deleteDirectory is delete directory and sub directory and files
     * @param  String $dir  path of dir
     * @return Boolean
     */
    public function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }

    /**
     * generate array from .po file for store in database
     * @return Array
     */
    public function generate_uploaded_po($filepath)
    {
        $data       = array();
        $return_arr = array();
        $file       = fopen($filepath, "r+") or exit("Unable to open file!");
        while (!feof($file)) {
            $line =fgets($file);
            if (strpos($line, 'msgid') !== false) {
                $line            = trim(str_replace('msgid', "", $line));
                $data['msgid'][] = trim($line, '"');
            }
            if (strpos($line, 'msgstr') !== false) {
                $line             = trim(str_replace('msgstr', "", $line));
                $data['msgstr'][] = trim($line, '"');
            }
        }
        $return_arr = array_combine($data['msgid'], $data['msgstr']);
        return $return_arr;
    }

    /**
     * insert messages for dashboard
     */
    public function passed_messages($data)
    {
        $return_val = $this->mdl_dashboard->insert_service_message($data);
        return $return_val;
    }
}
