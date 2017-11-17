<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_user', '', true);
        $this->load->model('mdl_module', '', true);
    }

    public function index()
    {
        if ($this->session->userdata('user')) {
            redirect('dashboard', 'refresh');
        } else {
            $data = array();
            $load_template = array('top_menu' => false, 'sidebar' => false);

            //This method will have the credentials validation
            $this->form_validation->set_rules('username', _('Username'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('password', _('Password'), 'trim|required|xss_clean|callback_checkDatabase');
            if ($this->form_validation->run() == false) {
                //Field validation failed.  User redirected to login page

                load_views('login', $data, $load_template);
            } else {
                if (isset($_GET['url'])) {
                    header('Location: '.$_GET['url']);
                } else {
                    //Go to private area
                    redirect('dashboard', 'refresh');
                }
            }
        }
    }

    public function checkDatabase($password)
    {

        //Field validation succeeded.  Validate against database
        $username = $this->input->post('username');
        $remember = $this->input->post('remember') ? true : false;

        //query the database
        $result = $this->mdl_user->login($username, $password);
        if ($result) {
            $sess_array = array();
            foreach ($result as $row) {
                $data = $this->mdl_user->user_get_by_id($row->user_id, true);

                $roles = array();
                foreach ($data['roles'] as $key => $value) {
                    $roles[] = $value['role_id'];
                }

                //get modules of user
                $restrict_modules = $this->mdl_module->get_user_restrict_modules($data['user_id']);
                $sess_array = array(
                    'id' => $data['user_id'],
                    'username' => $data['username'],
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'language' => $data['language'],
                    'date_format' => $data['date_format'],
                    'time_format' => $data['time_format'],
                    'profile_image' => $data['profile'],
                    'roles' => implode(',', $roles),
                    'restrict_modules' => $restrict_modules,
                );
                if ($remember) {
                    $this->autologin->login($username, $remember);
                }
                $this->session->set_userdata('user', $sess_array);
            }

            return true;
        } else {
            $this->form_validation->set_message('checkDatabase', _('Invalid username or password'));
            return false;
        }
    }

    public function logout()
    {
        $this->autologin->logout();
        $this->session->unset_userdata('user');
        session_destroy();
        redirect();
    }

    public function unauthorize()
    {
        $data = array();
        $data['heading'] = 'ERROR';
        $data['message'] = _('You are not authorize to access this area.').' <a  href="'.base_url().'login/logout" class="btn btn-link">'._('Logout').'</a>';
        $this->load->view('/errors/html/error_404', $data);
    }
}
