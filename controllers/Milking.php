<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Milking extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('animals/mdl_status', '', true);
        $this->load->model('milking/mdl_pannello', '', true);
        $this->load->model('milking/mdl_milking', '', true);
        $this->load->model('mdl_settings', '', true);
        $this->load->helper(array('form',  'url'));
    }

    public function index()
    {
        redirect('milking/monitor');
    }

    public function monitor()
    {
        $data = array();
        $data['msg'] = '';
        $data['msg_class'] ='';
        $data['devicesWithRooms'] = array();
        $data['pannelli_totali'] = 0;

        $data['iddevice'] = '';
        $data['device_list'] = $this->mdl_settings->get_milkingdevice();

        if ($data['device_list']) {

            $iddevice = (isset($_GET['iddevice']) && $_GET['iddevice']) ? $_GET['iddevice'] : $data['device_list'][0]['iddevice'];
            $data['iddevice'] = $iddevice;

            $pconfigurator = $this->mdl_settings->select_pconfigurator($iddevice);
            if ($pconfigurator) {
                foreach ($pconfigurator as $key => $value) {
                    $data['devicesWithRooms'][$value['IP']][$value['idroom']] = $value;
                    $room_max = max($value['top_left'], $value['bottom_left'], $value['top_right'], $value['bottom_right']);
                    if ($room_max > $data['pannelli_totali']) {
                        $data['pannelli_totali'] = $room_max;
                    }
                }
            }
        }

        // DEFINE MONITOR CONSTANTS
        //$data['MONITOR_REFRESH'] = 3000; // millisecond
        //$data['PANNELLO_REFRESH'] = 5000; // millisecond
        //$data['LIVEAJAX_REFRESH'] = 5000; // millisecond
        //$data['UPDATEPANEL_REFRESH'] = 5000; // millisecond
        $data['icon_path'] = base_url() . 'assets/images/milking/icon/';

        load_views('milking/monitor', $data);
    }

    public function monitorajax()
    {
        // Handle AJAX Request.
        if (isset($_GET['is_ajax']) && $_GET['is_ajax']) {
            
            $last_ts = (isset($_GET['last_ts']) && $_GET['last_ts']) ? $_GET['last_ts'] : 0;
            $idpannello = (isset($_GET['idpannello']) && $_GET['idpannello']) ? $_GET['idpannello'] : 0;

            $return = array();
            $return['result'] = "OK";
            $return['error'] = 0;
            $return['last_ts'] = $last_ts;
            //$return['last_ts'] = 0; # DEBUG MODE

            if ($idpannello) {
                $data_pannello = $response = $this->setupPannelloAnimal($idpannello);
            } else {
                $data_pannello = $response = $this->getChangedPannelloData($last_ts);
                if ($data_pannello) {
                    $temp_total = sizeof($data_pannello);
                    //$ts = DateTime::createFromFormat("Y-m-d H:i:s", $data_pannello[$temp_total-1]['ts']);
                    $return['last_ts'] = $data_pannello[$temp_total-1]['ts'];
                }
            }

            $return['data'] = $data_pannello;

            echo json_encode($return);
            die;
        }

    }
      

    public function liveajax()
    {
        // Handle AJAX Request.
        if (isset($_GET['is_ajax']) && $_GET['is_ajax']) {
            
            $last_ts = (isset($_GET['last_ts']) && $_GET['last_ts']) ? $_GET['last_ts'] : 0;
            $idpannello = (isset($_GET['idpannello']) && $_GET['idpannello']) ? $_GET['idpannello'] : 0;

            $return = array();
            $return['result'] = "OK";
            $return['error'] = 0;
            $return['last_ts'] = $last_ts;

            if ($idpannello) {
                $data_pannello = $response = $this->getLiveData($idpannello);
            }

            $return['data'] = $data_pannello;

            echo json_encode($return);
            die;
        }

    }
      

    public function sessions()
    {
        $data = array();
        $data['msg'] = '';
        $data['msg_class'] ='';

        $data['sessions'] = $this->mdl_milking->get_sessions();
        load_views('milking/sessions', $data);
    }


    private function getChangedMilkingSessions($timestamp)
    {

         // GET last 30 logs
        $this->db->select('*');
        if ($timestamp > 0) {
            $this->db->where('ts >', $timestamp);
        }
        $this->db->from('milking_sessions');
        $query = $this->db->get();
        $data_pannelli = $query->result_array();
        //echo $this->db->last_query();
        return $data_pannelli;
    }

    private function setRandomStatus()
    {
        $this->db->select('*');
        $this->db->from('milking_sessions');
        $query = $this->db->get();
        $data_pannelli = $query->result_array();

        foreach ($data_pannelli as $key => $value) {

            $update = array(
                'pannello_status' => rand(-1, 5),
                'produzione' => rand(100, 10000),
                'milking_time' => rand(100, 10000),
                'alarms' => '00',
                'attenzioni' => $bitString,
                'ts' => (microtime(true) * 100)
                );
            $this->db->where('idpannello', $value['idpannello']);
            $this->db->update('milking_sessions', $update);
        }
    }

    private function getChangedPannelloData($timestamp)
    {
        $data = array();
        $sessions = self::getChangedMilkingSessions($timestamp);

        // Only for testing
        //self::setRandomStatus();

        foreach ($sessions as $session) {
            //var_dump($session);
            $pannelloData = $this->setupPannelloAnimal($session['idpannello']);
            $data[] = $pannelloData;
        }

        return $data;
    }

    private function setupPannelloAnimal($idpannello)
    {
        $pannelloData = array();

        $pannello = $this->mdl_pannello->get_sessionsbypannello($idpannello);
        $pannelloData['idpannello'] = $idpannello;
        if ($pannello) {
            $pannelloData = $pannello;
            // Rewrite some data
            $pannelloData['produzione'] = $this->mdl_pannello->getProduzione($pannello['produzione']);
            $pannelloData['produzione_grammi'] = $pannello['produzione'];
            $pannelloData['milking_time'] = $this->mdl_pannello->getMilkingTime($pannello['milking_time']);

            // GET ANIMAL VALUES
            if(isset($pannello['idanimal']) && $pannello['idanimal'] > 0) {
                $idanimal = $pannello["idanimal"];
                $animalValues  = $this->mdl_animal->initbyAnimalID($idanimal);

                if ($animalValues) {
                    $pannelloData['prod_status'] = $animalValues->getProductionStatus();
                    $pannelloData['reprod_status'] = $animalValues->getReproductionStatus();
                    $pannelloData['dim'] = $animalValues->getDIM();
                    $pannelloData['lact'] = $animalValues->getLactation();

                    // GET Produzione expected
                    $data = $this->mdl_milking->get_last_milking_data($pannello['idanimal'], 20);
                    // @TODO: optimize include classes from imilk repo
                    include_once(APPLICATION_PATH . "/../imilk/iMilkClient/ProduzioneAttesa.php");
                    // @TODO: optimize include classes from imilk repo
                    $paObj = new iMilkClient_ProduzioneAttesa();
                    $paObj->initData($data);
                    $produzione_attesa = $paObj->getProduzioneAttesa();
                    $pannelloData['produzione_exp'] = number_format($produzione_attesa/1000, 2, ",", ".") . 'kg';

                    // GET Temp., Cond. Expected
                    $sql_exp = "SELECT AVG(conducibilita_media) AS conductivity_exp, AVG(temperatura_media) AS temperature_exp, AVG(tempo_mungitura) AS milking_time_exp
                         FROM milking_data WHERE idanimal='$idanimal' GROUP BY idanimal ORDER BY data_findat DESC LIMIT 0,10";
                    $query = $this->db->query($sql_exp);
                    $exp_values = $query->row();
                    $pannelloData['conductivity_exp'] = $exp_values->conductivity_exp;
                    $pannelloData['temperature_exp'] = $exp_values->temperature_exp;
                    $pannelloData['milking_time_exp'] = gmtime_format($exp_values->milking_time_exp, 'i:s');
                }
            }
        }
        return $pannelloData;
    }

    private function getLiveData($idpannello)
    {
        $pannello = $this->mdl_pannello->get_sessionsbypannello($idpannello);

        $data = array();
        // Random data
        $data['conductivity_value'] = $pannello['conducibilita_attuale'];
        $data['temperature_value'] = $pannello['temperatura_attuale'];
        // DATI GRAFICO
        $dati_grafico = unserialize($pannello['dati_grafico']);

        $graph_data = [];
        if (is_array($dati_grafico)) {
            foreach ($dati_grafico as $k => $value) {
                $graph_data[$k]['x'] = $k;
                $graph_data[$k]['y'] = $value;
            }
        }
        $data['graph_data'] = $graph_data;

        return $data;
    }

}
