<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Utilities extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_settings', '', true);
    }


    public function index()
    {
        redirect('utilities/sendcmd');
    }

    public function sendcmd()
    {
        $data = array();
        $data['msg'] = '';
        $data['msg_class'] ='';

        // add devices list
        $data['device_list'] = $this->mdl_settings->get_milkingdevice();

        if (isset($_POST['submit'])) {

            $this->form_validation->set_rules('CMD', 'CMD', 'required');

            if ($this->form_validation->run() !== false) {

                $post_data = array();
                $post_data['iddevice'] = $this->input->post('iddevice');
                $post_data['CMD'] = $this->input->post('CMD');
                $post_data['STR'] = $this->input->post('STR');

                // Fetch time when call start
                $data['time_send'] = microtime(true);

                // build the URL to call
                $data['post_url'] = $URL = IMILK_WS_URL."progimk/sendcmd/";
                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($post_data),
                        ),
                    );
                $context  = stream_context_create($options);
                $result = file_get_contents($URL, false, $context);

                // Fetch time when call end
                $data['time_receive'] = microtime(true);

                // Result handling
                if ($result === false) {
                    // Error
                    $data["result"] = _('No response found.');
                } else {
                    // Decode JSON response into Array
                    $data["result"] = json_decode($result, true);
                }

                // Post data to display in html
                $data['post_data'] = $post_data;
            }
        }

        load_views('utilities/sendcmd', $data);
    }


    public function updatefirmware()
    {
        /**
         * @TODO: we should create integration of both the environment: imilk and bbweb
         * Here it includes directly the needed class but we have to improve it
         */
        include(APPLICATION_PATH . "/../imilk/iMilkClient/FirmwareUploader.php");
        // init new iMilkClient_FirmwareUploader
        $fUploader = new iMilkClient_FirmwareUploader();
        $writeDirFirmwares = $fUploader->getFilePath();

        // load & config UPLOAD library
        $config['upload_path']          = $writeDirFirmwares;
        $config['allowed_types']        = '*';
        $this->load->library('upload', $config);

        // init data and add devices list
        $data = [];

        if (isset($_POST['submit'])) {

            if ($this->upload->do_upload('firmware')) {

                $upload_data = $this->upload->data();

                // convert FILE to GUL format
                $full_path = $upload_data["full_path"];
                $ret_converter = genera_file_gul($full_path);
                if ($ret_converter !== 0) {
                    die("ERROR 'genera_file_gul' - return: '$ret_converter'");
                }

                // get data of the uploaded file
                $raw_name = $upload_data["raw_name"];
                $filesize = $upload_data["file_size"] * 1024;
                $firmware_tipo = substr($upload_data["orig_name"], 0, -4);

                // read info from file gul
                $file_gul = substr($full_path, 0, -4) . ".gul";
                $lines = file($file_gul);
                $num_lines = count($lines);

                // set iddevice
                $iddevice = $this->input->post('iddevice');

                // start UPDATE procedure in DB
                $this->db->query("UPDATE devices_firmware SET in_progress=0");
                $this->db->query("INSERT INTO devices_firmware SET 
                      iddevice = '$iddevice',
                      filename = '$raw_name',
                      firmware_tipo = '$firmware_tipo', 
                      filesize = '$filesize',
                      num_lines = '$num_lines',
                      in_progress = 1,
                      data_upload = NOW()");
                $iddf = $this->db->insert_id();

                // call iMilk API to run PROGIMK process
                $post_data = array();
                $post_data['iddevice'] = $iddevice;
                $post_data['CMD'] = "PROGFIRMWARE";
                $post_data['STR'] = $iddf;

                // build the URL to call
                $URL = IMILK_WS_URL."progimk/sendcmd/";
                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($post_data),
                    ),
                );
                $context  = stream_context_create($options);
                $result = file_get_contents($URL, false, $context);

                $data = ['upload_data' => $upload_data, 'result' => $result];
            } else {
                $data = ['error' => $this->upload->display_errors()];
            }
        }

        // get Firmwares log
        $sql_logs = 'SELECT *, IF(num_lines > 0, 
                                    ROUND(num_lines_sent / num_lines * 100),
                                    0
                                 ) AS perc_sent 
                     FROM devices_firmware ORDER BY data_upload DESC';
        $query = $this->db->query($sql_logs);
        $data['firmware_logs'] = $query->result_array();

        // add devices list
        $data['device_list'] = $this->mdl_settings->get_milkingdevice();

        load_views('utilities/updatefirmware', $data);

    }

    /**
     * Get Percentage Progress bar by iddf
     * @return int
     */
    public function getpercinprogress()
    {
        //$iddf = filter_input(INPUT_GET, "iddf");
        $iddf = isset($_GET["iddf"]) ? $_GET["iddf"] : null;
        $return = array('perc_sent' => 0);
        if (!is_null($iddf)) {

            // get perc_sent by iddf
            $query = $this->db->query("SELECT iddf, error, IF(num_lines > 0, ROUND(num_lines_sent / num_lines * 100), 0) AS perc_sent FROM devices_firmware WHERE iddf='$iddf'");
            $dLog = $query->row_array();
            $return = $dLog;
        }
        echo json_encode($return);
        die;
    }
}
