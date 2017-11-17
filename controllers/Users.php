<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_role', '', true);
        $this->load->model('mdl_module', '', true);
        $this->load->model('mdl_settings', '', true);
        $this->load->helper(array('form', 'url'));
    }

    public function index()
    {
        $data = array();
        $data['msg'] = '';
        $data['msg_class'] ='';

        if (isset($_GET['insert'])) {
            if ($_GET['insert']) {
                $data['msg_class'] = "alert-success";
                $data['msg'] = _("Successfully Inserted.");
            }
        }

        if (isset($_GET['update'])) {
            if ($_GET['update']) {
                $data['msg_class'] = "alert-success";
                $data['msg'] = _("Successfully Updated.");
            }
        }

        if (isset($_POST['delete'])) {
            if ($_POST['delete']) {
                $user_id = $_POST['delete'];
                $response = $this->mdl_user->user_delete($user_id);
                if ($response) {
                    $data['msg_class'] = "alert-success";
                    $data['msg'] = _("Successfully Deleted.");
                } else {
                    $data['msg_class'] = "alert-danger";
                    $data['msg'] = _("Something goes wrong please try again.");
                }
                echo json_encode($data);
                die;
            }
        }

        $filter = array();
        if (isset($_GET['role_id']) && ($_GET['role_id'])) {
            $filter['role_id'] = $_GET['role_id'];
            $data['role_id'] = $_GET['role_id'];
        }

        $data["results"]=$this->mdl_user->get_users($filter);
        $data["user_restrictions_roles"] = $this->mdl_user->user_restrictions_roles('role_id');

        load_views('users', $data);
    }

    public function view()
    {

        $data = array();
        $data["url"] = "users";

        if (isset($_GET['id']) && ($_GET['id'])) {

            $data['user_id'] = $_GET['id'];
            $data['user_data'] = $this->mdl_user->user_get_by_id($data['user_id']);
            if (!$data['user_data']) {
                redirect('users');
            }
            load_views('user_view', $data);

        } else {
            redirect($data["url"]);
        }
    }

    public function insert()
    {

        if (insert_restriction()) { //allow logged user to insert

            $data = array();
            $back_url = 'users';
            $data["form_insert"] = "1";
            $data["update"] = 0;
            $data['msg'] = '';
            $data['msg_class'] ='';
            $data["roles"] = $this->mdl_role->get_roles();

            //update id
            $data['user_id'] = (isset($_GET['id'])) ? $_GET['id'] : '';
            if ($data['user_id']) {
                $data["form_insert"] = "";
                $data["update"] = 1;
                $data['user_data'] = $this->mdl_user->user_get_by_id($data['user_id']);
                $data['user_data']['all_languages'] = $this->mdl_settings->select_all_languages();
                if (!$data['user_data']) {
                    redirect('users/insert');
                }
            }

            if (isset($_POST['submit'])) {
                //p($_POST); die;
                $config['upload_path'] = './assets/uploads/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = '1024';
                if (isset($_FILES['profile']) && !empty($_FILES['profile']['name'])) {
                    $ext = end(explode(".", $_FILES['profile']['name']));
                    $config['file_name'] = ($this->input->post('update')) ? $this->input->post('update').'.'.$ext : $this->mdl_user->get_next_insertid().'.'.$ext;
                }

                if ($this->input->post('old_file') && isset($_FILES['profile']) && !empty($_FILES['profile']['name']) && file_exists(APPLICATION_PATH.'/assets/uploads/'.$this->input->post('old_file'))) {
                    unlink(APPLICATION_PATH.'/assets/uploads/'.$this->input->post('old_file'));
                }

                //$config['max_width']  = '1024';
                //$config['max_height']  = '768';
                $this->load->library('upload', $config);

                $this->form_validation->set_rules('username', 'Username', 'required|callback_username_check');
                $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check');
                $this->form_validation->set_rules('role[]', 'Role', 'required|xss_clean');
                $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
                $this->form_validation->set_rules('password', 'Password', 'required|xss_clean');
                $this->form_validation->set_rules('profile', 'Image', 'callback_handle_upload');
                //die;

                if ($this->form_validation->run() == false) {

                    load_views('user_registration', $data);

                } else {

                    $data_array = array();

                    if ($this->input->post('update')) {
                        if (isset($_POST['profile'])) {
                            $data_array['profile'] = isset($_POST['profile']) ? $_POST['profile'] : $_POST['selected_icon'];
                        } else if (isset($_POST['old_file'])) {
                            $data_array['profile'] = isset($_POST['old_file']) ? $_POST['old_file'] : $_POST['selected_icon'];
                        }
                    } else {
                        $data_array['profile'] = $_POST['selected_icon'];
                    }

                    $data_array['username'] = $this->input->post('username');
                    if (isset($data['user_data']) && $data['user_data']) {
                        if ($data['user_data']['password'] != $this->input->post('password')) {
                            $data_array['password'] = md5($this->input->post('password'));
                        }
                    } else {
                        $data_array['password'] = md5($this->input->post('password'));
                    }

                    $data_array['email'] = $this->input->post('email');
                    $data_array['name'] = $this->input->post('name');

                    $data_array['address']=$this->input->post('address');
                    $data_array['cap']=$this->input->post('cap');
                    $data_array['city']=$this->input->post('city');
                    $data_array['state']=$this->input->post('state');
                    $data_array['phone']=$this->input->post('phone');
                    $data_array['language']=$this->input->post('language');
                    $data_array['date_format']=$this->input->post('date_format');
                    $data_array['time_format']=$this->input->post('time_format');
                    $data_array['calender_type']=$this->input->post('calender_type');

                    if ($this->input->post('update')) {
                        $response = $this->mdl_user->user_update($this->input->post('update'), $data_array);
                        if ($response) {

                            $update_user_role = array();
                            $update_user_role['role_id'] = $this->input->post('role');
                            $response = $this->mdl_user->update_user_role($data['user_id'], $update_user_role);
                            $user_details = $this->session->userdata('user');
                            $user_details['language'] = $data_array['language'];
                            $this->session->set_userdata('user', $user_details);
                            redirect($back_url.'?update=1');
                        } else {
                            $data['msg_class'] = "alert-danger";
                            $data['msg'] = _("Something goes wrong please try again.");
                            load_views('user_registration', $data);
                        }
                    } else {
                        $response = $this->mdl_user->user_insert($data_array);
                        if ($response) {

                            $insert_user_role = array();
                            $insert_user_role['user_id'] = $response;
                            $insert_user_role['role_id'] = $this->input->post('role');
                            $response = $this->mdl_user->insert_user_role($insert_user_role);

                            redirect($back_url.'?insert=1');
                        } else {
                            $data['msg_class'] = "alert-danger";
                            $data['msg'] = _("Something goes wrong please try again.");
                            load_views('user_registration', $data);
                        }
                    }

                    //print_pre($_POST); die;
                    //$this->load->view('formsuccess');
                }
            } else {
            //p($data); die;
            //  print_pre($data); die;
                load_views('user_registration', $data);
            }
        } else {
            redirect('users');
        }

    }

    public function modules()
    {
        $data = array();
        $data['msg'] = '';
        $data['msg_class'] = '';

        $user_id = (isset($_GET['user_id']) && ($_GET['user_id'])) ? $_GET['user_id'] : '';
        $role_id = (isset($_GET['role_id']) && ($_GET['role_id'])) ? $_GET['role_id'] : '';

        if (!$user_id && !$role_id) {
            $user_info = $this->session->userdata('user');
            redirect(getClass().'/'.getMethod().'?user_id='.$user_info['id']);
            die;
        }

        if (isset($_POST['submit'])) {
            $new_records = '';

            if (isset($_POST['role']) && $_POST['role']) {
                $new_records = $_POST['role'];

                $o_records = isset($_POST['modules_assign']) ? explode(',', $_POST['modules_assign']) : array();

                $old_records = array();
                foreach ($o_records as $key => $value) {
                    $old_records[$value] = true;
                }
                $db_array = array();
                foreach ($new_records as $key => $m_a) {

                    if (array_key_exists($m_a, $old_records)) {
                        unset($old_records[$m_a]);
                        continue;
                    }

                    $insert_arr = array();
                    $temp = explode('_', $m_a);
                    $insert_arr['module_id'] = $temp[0];
                    $insert_arr['role_id'] = $temp[1];

                    array_push($db_array, $insert_arr);
                }

                $old_records_array = array();
                foreach ($old_records as $key => $m_a) {

                    $insert_arr = array();
                    $temp = explode('_', $key);
                    $insert_arr['module_id'] = $temp[0];
                    $insert_arr['role_id'] = $temp[1];

                    array_push($old_records_array, $insert_arr);
                }

                $response = $this->mdl_module->module_assign($db_array, $old_records_array);
                $data['msg_class'] = "alert-success";
                $data['msg'] = _("Successfully Updated.");

            } else if (isset($_POST['user_edit']) && $_POST['user_edit']) {

                $new_records = (isset($_POST['user']) && $_POST['user']) ? $_POST['user'] : '';

                $o_records = isset($_POST['modules_user_assign']) ? explode(',', $_POST['modules_user_assign']) : array();

                $old_records = array();
                foreach ($o_records as $key => $value) {
                    $old_records[$value] = true;
                }

                $db_array = array();
                if ($new_records) {
                    foreach ($new_records as $key => $m_a) {

                        if (array_key_exists($m_a, $old_records)) {
                            unset($old_records[$m_a]);
                            continue;
                        }

                        $insert_arr = array();
                        $temp = explode('_', $m_a);
                        $insert_arr['module_id'] = $temp[0];
                        $insert_arr['user_id'] = $temp[1];
                        if ($temp[1]) {
                            array_push($db_array, $insert_arr);
                        }
                    }
                }
                $old_records_array = array();
                foreach ($old_records as $key => $m_a) {

                    $insert_arr = array();
                    $temp = explode('_', $key);
                    $insert_arr['module_id'] = $temp[0];
                    $insert_arr['user_id'] = $temp[1];
                    if ($temp[1]) {
                        array_push($old_records_array, $insert_arr);
                    }
                }
                //p($_POST);
                //p($old_records_array);
                //p($db_array); die;
                $response = $this->mdl_module->module_assign($db_array, $old_records_array);
                $data['msg_class'] = "alert-success";
                $data['msg'] = _("Successfully Updated.");
            }

            if ($this->input->post('update')) {

                $this->mdl_module->module_assign($response, $this->input->post('update'));
                $data['msg_class'] = "alert-success";
                $data['msg'] = _("Successfully Updated.");
            }

        }
        // $data['modules'] = $this->mdl_module->get_modules();

        if ($user_id) {
            $data['user_id'] = $_GET['user_id'];
            $data['users'][0] = $this->mdl_user->user_get_by_id($data['user_id']);

        } else if ($role_id) {

            $data['role_id'] = $_GET['role_id'];
            $data['roles'][0] = $this->mdl_role->role_get_by_id($data['role_id']);

        } else {

            $data['roles']=$this->mdl_role->get_roles();
        }
        $data['modules'] = $this->mdl_module->get_modules_user_id($user_id);
        $modules_assign=$this->mdl_module->get_modules_assign($role_id);
        $data['modules_assign'] = array();
        foreach ($modules_assign as $key => $value) {
            $data['modules_assign'][$value['module_id'].'_'.$value['role_id']] = true;
            $data['modules_user_assign'][$value['module_id'].'_'.$value['user_id']] = true;
        }

        load_views('modules_assign', $data);
    }


    public function username_check($str)
    {
        //print_pre($data); die;
        if ($str) {
            if ($this->mdl_user->username_check($str)) {
                $this->form_validation->set_message('username_check', 'The '.$str.' already exists');
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    public function email_check($str)
    {
        if ($str) {
            if ($this->mdl_user->email_check($str)) {
                $this->form_validation->set_message('email_check', 'The '.$str.' already exists');
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    public function handle_upload()
    {
        if (isset($_FILES['profile']) && !empty($_FILES['profile']['name'])) {
            if ($this->upload->do_upload('profile')) {
            // set a $_POST value for 'profile' that we can use later
                $upload_data = $this->upload->data();
                $_POST['profile'] = $upload_data['file_name'];
                return true;
            } else {
            // possibly do some clean up ... then throw an error
                $this->form_validation->set_message('handle_upload', $this->upload->display_errors());
                return false;
            }
        } else {
          // throw an error because nothing was uploaded
            return true;
        }
    }

    public function ifc_policy()
    {
        $data = array();

        if (BBWEB_IFC_REGISTRATION) {
            redirect(site_url());
            exit;
        }

        if (isset($_SESSION['privacy']) && $_SESSION['privacy']) {
            redirect('users/ifc_registration');
            exit;
        }

        if (IS_SERVICE) {
            $this->load->view('ifc_policy', $data);
        } else {
            $data['heading'] = 'ERROR';
            $data['message'] = _('This system is not ready to use').' <a  href="'.base_url().'login/logout" class="btn btn-link">'._('Logout').'</a>';
            $this->load->view('errors/html/error_404', $data);
        }
    }

    /**
     * This procedure will do a simple work:
            - check connection when load the page
            - ask to the user for Serial Number
            - after submit the form create certificates (already done in ifc_registration)
     */
    public function ifc_installation()
    {
        $installation_log_file = APPLICATION_PATH . "/../write-dir/logs/installation.log";

        $data = array();
        $data['msg'] = '';
        $data['msg_class'] ='alert-warning';
        $data['serial_number'] = '';
        $data['flag_showform'] = true;

        $this->load->model('mdl_settings', '', true);

        // - ping server host to check connection.
        $parse = parse_url(BBWEB_IFC_URL);
        $ifcserver_hostname = $parse['host'];
        $connection_is_available = ping($ifcserver_hostname, 80, 10);
        if (!$connection_is_available) {
            $data['flag_showform'] = false;
            $data['msg_class'] = "alert-danger";
            $msg_error = _("Server connection not available.");
            $data['msg'] = $msg_error;
            writeOutputArrayOnFile($installation_log_file, $msg_error);
        }

        // Handle POST Request as per action
        if (isset($_POST['submit'])) {

            // - Register IFC with deafult values to iFC Server.
            $this->form_validation->set_rules('serial_number', 'Serial number', 'required|xss_clean');
            
            if ($this->form_validation->run() == false) {

                $data['msg'] = _('Please, enter a valid Serial number');
                $data['msg_class'] ='alert-warning';
                writeOutputArrayOnFile($installation_log_file, "Form NOT valid (msg: Please, enter a valid Serial number!)");

            } else {

                $data['user'] = array();
                $data['user']['serial_number'] = $this->input->post('serial_number');

                /**
                 * @todo: make new private function for curl calls.
                 * Davide: Yes, but study it better than this bad code.
                 */
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, BBWEB_IFC_URL . "installation.php");
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

                if($server_output !== false) {

                    // decode output.
                    $response = json_decode($server_output, true);
                    if ($response && $response['error'] == 0) {

                        /****
                         * Certificates on server side are created based on this SERIAL NUMBER
                         * MUST STOP procedure for any kind of trying and show only the error
                         * Message class become DANGER
                         */
                        $data['flag_showform'] = false;
                        $data['msg_class'] = "alert-danger";

                        $protectedKeysPath = APPLICATION_PATH . "/../write-dir/keys/";
                        if (!file_exists($protectedKeysPath)) {
                            mkdir($protectedKeysPath, 0777);
                            chmod($protectedKeysPath, 0777);
                        }
                        file_put_contents($protectedKeysPath . 'ca.crt', $response['ca.crt']);
                        file_put_contents($protectedKeysPath . 'client.crt', $response['client.crt']);
                        file_put_contents($protectedKeysPath . 'client.key', $response['client.key']);

                        // - saveCertificate will Save CERTs to their location.
                        $command = "sudo " . APPLICATION_PATH . "/../utilities/openvpn/saveCertificate.sh"
                            . " --key_ca '" . $protectedKeysPath . 'ca.crt' . "'"
                            . " --key_certificate '" . $protectedKeysPath . 'client.crt' . "'"
                            . " --key_private '" . $protectedKeysPath . 'client.key' . "'";
                        exec($command, $command_output, $command_return);

                        // LOG the OUTPUT of the command
                        writeOutputArrayOnFile($installation_log_file, $command_output);

                        if ($command_return != 0) {
                            $error = "ERROR: saveCertificate.sh error!";
                            $data['msg'] = $error;
                            writeOutputArrayOnFile($installation_log_file, $error);

                        } else {

                            // prepare the response
                            $json_data = getJSON($command_output);
                            $return['json_response'] = serialize($command_output);

                            if ($json_data['error'] == 0) {
                                $update_arr = array();
                                $update_arr[] = array('var_code' => 'SYSTEM_SERIAL_NUMBER', 'value' => $data['user']['serial_number']);
                                $response = $this->mdl_settings->update_batch($update_arr, 'var_code');

                                // - Store Serial number in ifcinfo.sh for future use.
                                $cmd_to_store_sn = "sudo " . APPLICATION_PATH . "/../utilities/installprocedures.sh " . $data['user']['serial_number'];
                                exec($cmd_to_store_sn, $cmd_to_store_sn_output, $cmd_to_store_sn_return);

                                // LOG the OUTPUT of the command
                                writeOutputArrayOnFile($installation_log_file, $cmd_to_store_sn_output);

                                if ($cmd_to_store_sn_return != 0) {

                                    $error = "ERROR: installprocedures.sh error!";
                                    $data['msg'] = $error;
                                    writeOutputArrayOnFile($installation_log_file, $error);

                                } else {
                                    // PROCEDURE COMPLETED!
                                    $data['msg_class'] = "alert-success";
                                    $msg_OK = _("OK! Installation completed successfully, the system need to be rebooted.");
                                    $data['msg'] = $msg_OK;
                                    writeOutputArrayOnFile($installation_log_file, $msg_OK);
                                }

                            } else {
                                $data['msg'] = $json_data["message"];
                                writeOutputArrayOnFile($installation_log_file, "JSON data msg: " . $json_data["message"]);
                            }
                        }

                    } else {
                        $data['msg'] = $response['message'];
                        writeOutputArrayOnFile($installation_log_file, "Response msg: " . $response['message']);
                    }
                } else {
                    $error = _("Cannot connect to the server: ") . BBWEB_IFC_URL;
                    $data['msg'] = $error;
                    writeOutputArrayOnFile($installation_log_file, $error);
                }
            }
        }

        load_views('ifc_installation', $data, array('top_menu' => false));

    }

    public function ifc_registration()
    {
        if (BBWEB_IFC_REGISTRATION) {
            redirect(site_url());
            exit;
        }

        $data = array();
        $data['msg'] = '';
        $data['msg_class'] ='';
        $data['serial_number'] = '';

        $this->load->model('mdl_settings', '', true);

        if (isset($_POST['privacy']) && $_POST['privacy']) {
            $_SESSION['privacy'] = $_POST['privacy'];
        } else if (isset($_SESSION['privacy']) && $_SESSION['privacy']) {
            // Do nothing
        } else {
            redirect('users/ifc_policy');
        }

        if (isset($_POST['submit'])) {
            $this->form_validation->set_rules('serial_number', 'Serial number', 'required|xss_clean');
            $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('address', 'address', 'required|xss_clean');
            $this->form_validation->set_rules('city', 'city', 'required|xss_clean');
            $this->form_validation->set_rules('country', 'country', 'required|exact_length[2]');
            $this->form_validation->set_rules('cap', 'cap', 'required|xss_clean');
            $this->form_validation->set_rules('phone', 'phone', 'required|xss_clean');

            // custom error message
            $this->form_validation->set_message('customAlpha', 'Should only contain alpha numeric and dash');

            if ($this->form_validation->run() == false) {
                $this->load->view('ifc_registration', $data);
            } else {
                $data['user'] = array();
                $data['user']['serial_number'] = $this->input->post('serial_number');
                $data['user']['name']          = $this->input->post('name');
                $data['user']['email']         = $this->input->post('email');
                $data['user']['address']       = $this->input->post('address');
                $data['user']['city']          = $this->input->post('city');
                $data['user']['country']       = $this->input->post('country');
                $data['user']['cap']           = $this->input->post('cap');
                $data['user']['phone']         = $this->input->post('phone');

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, BBWEB_IFC_URL."registration2.php");
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
                    $this->db->where('var_code', 'BBWEB_IFC_REGISTRATION');
                    $this->db->update('bb_settings', array('value' => 1));

                    $update_arr = array();
                    $update_arr[] = array('var_code' => 'SYSTEM_NAME', 'value' => $data['user']['name']);
                    $update_arr[] = array('var_code' => 'SYSTEM_SERIAL_NUMBER', 'value' => $data['user']['serial_number']);
                    $update_arr[] = array('var_code' => 'SYSTEM_EMAIL', 'value' => $data['user']['email']);
                    $update_arr[] = array('var_code' => 'SYSTEM_ADDRESS', 'value' => $data['user']['address']);
                    $update_arr[] = array('var_code' => 'SYSTEM_CITY', 'value' => $data['user']['city']);
                    $update_arr[] = array('var_code' => 'SYSTEM_COUNTRY', 'value' => $data['user']['country']);
                    $update_arr[] = array('var_code' => 'SYSTEM_CAP', 'value' => $data['user']['cap']);
                    $update_arr[] = array('var_code' => 'SYSTEM_PHONE', 'value' => $data['user']['phone']);
                    $response = $this->mdl_settings->update_batch($update_arr, 'var_code');

                    $msg = _("Registration completed successfully. IFC will reboot now.");
                    redirect(base_url('settings/reboot?autostart=1&result=success&msg='.urlencode($msg)));

                    exit;

                } else {
                    $data['msg_class'] = "alert-danger";
                    $data['msg'] = $response['message'];
                }
                $this->load->view('ifc_registration', $data);
            }
        } else {
            $data['serial_number'] = get_ifc_serialno();
            $this->load->view('ifc_registration', $data);
        }

    }

    // callback function
    private function customAlpha($str)
    {
        if (!preg_match('/^[a-z0-9\-]+$/i', $str)) {
            return false;
        } else {
            return true;
        }
    }
}
