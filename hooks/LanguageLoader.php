<?php
class LanguageLoader
{
    public function initialize()
    {
        // Get CI instance
        $CI =& get_instance();

        // If user is logged in, load user's selected language.
        if ($CI->session->userdata('user')) {
            $user_details = $CI->session->userdata('user');
            $default_lang = ($user_details['roles'] == 1) ? 'en_SUI' : 'en_US';
            $language = (array_key_exists('language', $user_details) && $user_details['language']) ? $user_details['language'] : $default_lang;
        } else {
            // Default Language
            $language = 'en_SUI';
        }

        // Language change from query string
        if (isset($_GET['lang']) && $_GET['lang']) {
            $language = $_GET['lang'];
        }
        $gettext_text_domain = 'default';
        $CI->load->model('mdl_settings', '', true);
        $lang_data  = $CI->mdl_settings->data_by_lang_id($language);
        $lang_name = '';
        $lang = '';
        if ($lang_data) {
            $stored_file         = $lang_data->filename;
            $lang_name           = $lang_data->code;
            if ($stored_file) {
                $folders             = explode('/', $stored_file);
                $lang                = $folders[0];
                $mo_file             = explode('.', $folders[3]);
                $gettext_text_domain = $mo_file[0];
            }
        } else {
            $gettext_text_domain = 'default';
        }
        // Load Language
        $CI->load->library(
            'gettext',
            array(
                'gettext_locale' => $lang_name.'.UTF-8',
                'gettext_text_domain' => $gettext_text_domain,
                'gettext_locale_dir' => 'language/locale/'.$lang
            )
        );
    }
}
