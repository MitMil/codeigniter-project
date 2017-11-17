<?php
class Roles extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_role', '', true);
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

                $role_id = $_POST['delete'];
                $response = $this->mdl_role->role_delete($role_id);
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

        $data['results']=$this->mdl_role->get_roles();
        load_views('roles', $data);

    }

    public function view()
    {

        $data = array();
        $data["url"] = "roles";

        if (isset($_GET['id']) && ($_GET['id'])) {

            $data['role_id'] = $_GET['id'];
            $data['role_data'] = $this->mdl_role->role_get_by_id($data['role_id']);
            if (!$data['role_data']) {
                redirect('roles');
            }
            load_views('role_view', $data);

        } else {
            redirect($data["url"]);
        }
    }

    public function insert()
    {

        if (insert_restriction()) { //allow logged user to insert
            $data = array();
            $back_url = 'roles';
            $data["form_insert"] = "1";
            $data["update"] = 0;
            $data['msg'] = '';
            $data['msg_class'] ='';

            //update id
            $data['role_id'] = (isset($_GET['id'])) ? $_GET['id'] : '';
            if ($data['role_id']) {
                $data["form_insert"] = "";
                $data["update"] = 1;
                $data['role_data'] = $this->mdl_role->role_get_by_id($data['role_id']);
                if (!$data['role_data']) {
                    redirect('roles/insert');
                }
            }

            if (isset($_POST['submit'])) {

                $this->form_validation->set_rules('name', 'Name', 'required|xss_clean|callback_name_check');

                if ($this->form_validation->run() == false) {
                    load_views('role_registration', $data);
                } else {
                    $data_array = array();
                    $data_array['name'] = $this->input->post('name');
                    $data_array['parent'] = 3;

                    if ($this->input->post('update')) {
                        $response = $this->mdl_role->role_update($this->input->post('update'), $data_array);
                        if ($response) {

                            redirect($back_url.'?update=1');
                        } else {
                            $data['msg_class'] = "alert-danger";
                            $data['msg'] = _("Something goes wrong please try again.");
                            load_views('role_registration', $data);
                        }
                    } else {
                        $response = $this->mdl_role->role_insert($data_array);
                        if ($response) {
                            redirect($back_url.'?insert=1');
                        } else {
                            $data['msg_class'] = "alert-danger";
                            $data['msg'] = _("Something goes wrong please try again.");
                            load_views('role_registration', $data);
                        }
                    }
                }
            } else {
                //p($data); die;
            //  print_pre($data); die;
                load_views('role_registration', $data);
            }
        } else {
            redirect('roles');
        }
    }

    public function name_check($str)
    {
        //print_pre($data); die;
        if ($str) {
            if ($this->mdl_role->name_check($str)) {
                $this->form_validation->set_message('name_check', 'The '.$str.'already exists');
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
}
