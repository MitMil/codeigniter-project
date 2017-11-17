<?php
/**
 * Check login before controller load
 */
class Login
{
    /**
     * Login check
     * @return void
     */
    public function check_login()
    {
        // Get CI instance
        $CI =& get_instance();
        $CI->load->model('mdl_user', '', true);
        //If no session, redirect to login page.
        if (!$CI->mdl_user->check_login()) {
            redirect('login?url='.$_SERVER['REQUEST_URI'], 'refresh');
        } elseif (!check_Allrestriction()) {
            redirect('login/unauthorize', 'refresh');
        } else {

            // for conditions with user roles.
            $is_admin = $is_service = $is_manager = $is_user = false;

            // Get user role from session.
            $user_details = $CI->session->userdata('user');
            $roles = explode(',', $user_details['roles']);
            foreach ($roles as $key => $role) {
                if ($role==1) {
                    $is_admin = true;
                }
                if ($role==2) {
                    $is_service = true;
                }
                if ($role==3) {
                    $is_manager = true;
                }
                if ($role>3) {
                    $is_user = true;
                }
            }
            
            // Define conditional parameters for user roles.
            define('IS_ADMIN', $is_admin);
            define('IS_SERVICE', $is_service);
            define('IS_MANAGER', $is_manager);
            define('IS_USER', $is_user);

            // Check if initially startup
            $segment = $CI->uri->segment(1).'/'.$CI->uri->segment(2);
            // Check and redirect to ifc install if bbweb ifc installation not processed
            $install_url = get_bbweb_ifc_installation();
            if ((isset($install_url) && $install_url) && ($segment!=$install_url)) {
                $new_url = trim(str_replace($segment, $install_url, current_url()));
                if ($install_url=='users/ifc_policy') {
                    if (!BBWEB_IFC_REGISTRATION && ($segment != 'users/ifc_policy' && $segment != 'users/ifc_registration') && (!IS_ADMIN)) {
                        redirect($new_url, 'refresh');
                    }
                } else {
                    redirect($new_url, 'refresh');
                }
            }

            // Check if mobile device.
            $is_mobile = false;
            $detect = new Mobile_Detect;
            if ($detect->isMobile()) {
                $is_mobile = true;
            }
            // Define conditional parameter for mobile device.
            define('IS_MOBILE', $is_mobile);

            // Add current URL in 'Back' session array.
            add_back_url();
        }
    }
}
