<?php
class Api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * This is the PROXY for iMilk API
     * Everything is sent to iMilk API, wait for reply and return it
     */
    public function imilk()
    {
        // parse the QUERY_STRING
        $request_uri = $this->input->server('REQUEST_URI');
        $resource = str_replace('/api/imilk/', '', $request_uri);

        // build the URL to call
        $URL = IMILK_WS_URL . $resource;
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($_POST),
            ),
        );

        $context  = stream_context_create($options);
        $return = file_get_contents($URL, false, $context);
        if ($return === false) {
            die("ERROR1: return on calling '$URL'");
        }
        echo $return;
    }


}