<?php
class Modules extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_role', '', true);
        $this->load->model('mdl_module', '', true);
    }

    public function index()
    {
        $data=array();
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
                $response = '';
                if (insert_restriction('module')) {
                    $module_id = $_POST['delete'];
                    $response = $this->mdl_module->module_delete($module_id);
                }
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

        $data['results']=$this->mdl_module->get_modules();
        load_views('modules', $data);
    }

    public function view()
    {
        $data = array();
        $data["url"] = "modules";

        if (isset($_GET['id']) && ($_GET['id'])) {

            $data['module_id'] = $_GET['id'];
            $data['module_data'] = $this->mdl_module->module_get_by_id($data['module_id']);
            load_views('module_view', $data);

        } else {
            redirect($data["url"]);
        }
    }

    public function insert()
    {

        $data=array();
        $back_url="module";
        $data["form_insert"] = "1";
        $data["update"] = 0;
        $data['msg'] = '';
        $data['msg_class'] ='';

        $data['results']=$this->mdl_module->get_modules();

        $data['module_id'] = (isset($_GET['id'])) ? $_GET['id'] : '';
        if ($data['module_id']) {
            $data["form_insert"] = "";
            $data["update"] = 1;
            $data['module_data'] = $this->mdl_module->module_get_by_id($data['module_id']);
        } else {

            if (!insert_restriction('module')) {
                redirect('modules');
                exit;
            }
        }
        if (isset($_POST['submit'])) {

            $this->form_validation->set_rules('name', 'Name', 'required|xss_clean|callback_name_check');

            if ($this->form_validation->run() == false) {

                load_views('module_registration', $data);

            } else {

                $data_array=array();
                $data_array['module_name'] = $this->input->post('name');
                $data_array['parent'] = $this->input->post('parent');
                $module_url = $this->input->post('url');
                if ($module_url) {
                    $data_array['module_url'] = $module_url;
                }

                if ($this->input->post('update')) {
                    $response=$this->mdl_module->module_update($this->input->post('update'), $data_array);
                    if ($response) {
                        redirect('modules');
                    } else {
                        $data['msg_class'] = "alert-danger";
                        $data['msg'] = _("Something goes wrong please try again.");
                        load_views('module_registration', $data);
                    }
                } else {
                    $response =$this->mdl_module->module_insert($data_array);

                    if ($response) {
                        redirect('modules');
                    } else {
                        $data['msg_class'] = "alert-danger";
                        $data['msg'] = _("Something goes wrong please try again.");
                        load_views('module_registration', $data);
                    }
                }
            }

        } else {
            load_views('module_registration', $data);
        }
    }
    public function name_check($str)
    {
        //print_pre($data); die;
        if ($str) {
            if ($this->mdl_module->name_check($str)) {
                $this->form_validation->set_message('name_check', 'The '.$str.' already exists');
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
    public function assign()
    {
        $data = array();
        $data['msg'] = '';
        $data['msg_class'] = '';

        $user_id = (isset($_GET['user_id']) && ($_GET['user_id'])) ? $_GET['user_id'] : '';
        $role_id = (isset($_GET['role_id']) && ($_GET['role_id'])) ? $_GET['role_id'] : '';

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
        $data['modules'] = $this->mdl_module->get_modules();

        if ($user_id) {
            $data['user_id'] = $_GET['user_id'];
            $data['users'][0] = $this->mdl_user->user_get_by_id($data['user_id']);

        } else if ($role_id) {

            $data['role_id'] = $_GET['role_id'];
            $data['roles'][0] = $this->mdl_role->role_get_by_id($data['role_id']);

        } else {

            $data['roles']=$this->mdl_role->get_roles();
        }
        $modules_assign=$this->mdl_module->get_modules_assign($role_id);
        $data['modules_assign'] = array();
        foreach ($modules_assign as $key => $value) {
            $data['modules_assign'][$value['module_id'].'_'.$value['role_id']] = true;
            $data['modules_user_assign'][$value['module_id'].'_'.$value['user_id']] = true;
        }

        load_views('modules_assign', $data);
    }
}
