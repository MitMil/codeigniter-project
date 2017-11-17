<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_role', '', true);
        $this->load->model('mdl_module', '', true);
        $this->load->model('mdl_kpi', '', true);
        $this->load->model('mdl_dashboard', '', true);
        $this->load->model('milking/mdl_milking', '', true);
        $this->user_details = $this->session->userdata('user');
        $this->_user_id     = $this->user_details['id'];
        $this->_role_id     = $this->user_details['roles'];
    }

    public function index()
    {
        $data = array();
        $user_id = $this->_user_id;
        $role_id = $this->_role_id;
        if (isset($_POST['info_save'])) {
            $selected = $period = $groups = '';
            if (isset($_POST['info_checkbox'])) {
                $selected = array_keys($_POST['info_checkbox']);
            }
            if (isset($_POST['info_hours'])) {
                $period = $_POST['info_hours'];
            }
            if (isset($_POST['info_groups'])) {
                $groups = $_POST['info_groups'];
            }
            $response = $this->mdl_dashboard->StoreCheckedInfo($user_id, $selected, $period, $groups);
        }
        // Fetch all events of all animals
        $all_events = $this->mdl_kpi->get_all_events();

        $aspp_data = array();
        $service_per_pregnancy = array();
        $do_animal = array();
        $adfc_data = array();
        $ado_data = array();

        foreach ($all_events as $event) {

            // Data for 'Average Days to First Service' | CalvingEvent & InseminationEvent
            if ($event['idevent'] == CalvingEvent || $event['idevent'] == InseminationEvent) {
                $adfc_data[$event['idanimal']][] = $event;
            }

            // Date for 'Average Days Open' | CalvingEvent
            if ($event['idevent'] == CalvingEvent) {
                $ado_data[$event['idanimal']][] = $event;
            }
        }

        /*
         * Calculation for 'Average Days to First Service'
         */
        $adfs = 0;
        $adfs_temp = array();
        $count_service1 = 0;
        $total_days_service1 = 0;

        foreach ($adfc_data as $idanimal => $events) {
            $temp = 0;
            $count_lactation = 0;
            $adfs_temp[$idanimal][$temp] = array();

            foreach ($events as $event) {

                // CalvingEvent
                if ($event['idevent'] == CalvingEvent) {
                    $adfs_temp[$idanimal][$temp]['cal'] = $event['timestamp'];
                }

                // InseminationEvent
                if ($event['idevent'] == InseminationEvent) {

                    // Only consider First InseminationEvent after CalvingEvent
                    if (array_key_exists($temp, $adfs_temp[$idanimal]) && !array_key_exists('in', $adfs_temp[$idanimal][$temp])) {
                        $adfs_temp[$idanimal][$temp]['in'] = $event['timestamp'];

                        // Calculate the days between CalvingEvent date & First InseminationEvent date
                        if (array_key_exists('cal', $adfs_temp[$idanimal][$temp])) {
                            $adfs_temp[$idanimal][$temp]['diff'] = get_days_diff($adfs_temp[$idanimal][$temp]['cal'], $adfs_temp[$idanimal][$temp]['in']);

                            $count_lactation += 1;


                            $count_service1 += 1;
                            $total_days_service1 += $adfs_temp[$idanimal][$temp]['diff'];
                            $temp += 1;
                        }
                    }
                }
            }
        }
         // 'Average Days to First Service'
        if ($count_service1) {
            $adfs = $total_days_service1 / $count_service1;
        }

        $data['average_days_first_service'] = array(
            'range_start' => 30,
            'range_end'   => 120,
            'value'       => number_format($adfs, 0, '.', ''),
        );

        /*
         * Calculation for 'Average Services per Pregnancy'
         */
        $aspp = 0;
        $aspp_data = $this->mdl_kpi->get_aspp_data();
        $count_pregnant = 0;
        $count_insemination = 0;
        foreach ($aspp_data as $event) {

            // Count total pregnancy from 'PregnancyCheckEvent' with event Info as ‘PREGNANT’ or ‘AUTOMATIC_YES’
            if ($event['idevent'] == PregnancyCheckEvent && ($event['info'] == 'PREGNANT' || $event['info'] == 'AUTOMATIC_YES')) {
                $count_pregnant += 1;
            }

            // Count total number of Inseminations | 'InseminationEvent'
            if ($event['idevent'] == InseminationEvent) {
                $count_insemination += 1;
            }
        }

        // 'Average Services per Pregnancy'
        if ($count_pregnant) {
            $aspp = $count_insemination / $count_pregnant;
        }

        $data['average_services_per_pregnancy'] = array(
            'range_start' => 1,
            'range_end'   => 10,
            'value'       => number_format($aspp, 1, '.', ''),
        );

        /*
         * Calculation for 'Average Days Open'
         */
        $ado = 0;
        $ado2 = 0;
        $ado3 = 0;
        $ado_arr = array();
        $ado2_arr = array();
        $ado3_arr = array();
        foreach ($ado_data as $events) {
            $total_calving = count($events);

            if ($total_calving > 1) {
                $rc = strtotime(date('Y-m-d', strtotime($events[$total_calving - 1]['timestamp']))); //recent calving event timestamp
                $pc = strtotime(date('Y-m-d', strtotime($events[$total_calving - 2]['timestamp']))); //previous calving event timestamp

                $days = round((($rc - $pc) / 60 / 60 / 24) - BBWEB_PERIOD_GESTATION);
                $ado_arr[$events[$total_calving - 1]['idanimal']] = $days;

                if ($events[$total_calving - 1]['lactation'] == 2) {
                    $ado2_arr[$events[$total_calving - 1]['idanimal']] = $days;
                } else if ($events[$total_calving - 1]['lactation'] == 3) {
                    $ado3_arr[$events[$total_calving - 1]['idanimal']] = $days;
                }
            }
        }

        if ($ado_arr) {
            $ado = round(array_sum($ado_arr) / count($ado_arr));
        }
        if ($ado2_arr) {
            $ado2 = round(array_sum($ado2_arr) / count($ado2_arr));
        }
        if ($ado3_arr) {
            $ado3 = round(array_sum($ado3_arr) / count($ado3_arr));
        }

        $data['average_days_open'] = array(
            'range_start' => 40,
            'range_end'   => 200,
            'value'       => number_format($ado, 1, '.', ''),
        );


        /*
         * Calculation for 'Expected Calving Interval'
         */
        $avg_expected = 0;
        $ExpC_events = $this->mdl_kpi->get_expected_calving_data();
        $ExpC_data = array();
        $index = 0;
        foreach ($ExpC_events as $events) {
            if ($events['idevent'] == CalvingEvent) {
                $idanimal = $events['idanimal'];
                if (!array_key_exists($idanimal, $ExpC_data)) {
                    $ExpC_data[$idanimal] = array();
                }
                if (array_key_exists($index, $ExpC_data[$idanimal]) && $ExpC_data[$idanimal]) {
                    $index += 1;
                } else {
                    $index = 0;
                }
                $ExpC_data[$idanimal][$index]['cal_start_time'] =  $events['timestamp'];
            }

            if ($events['idevent'] == InseminationEvent && $events['idanimal'] == $idanimal) {
                $ExpC_data[$idanimal][$index]['last_insemination_time'] =  $events['timestamp'];
            }
        }
        $expected_calving_interval = array();
        foreach ($ExpC_data as $events) {
            foreach ($events as $event) {
                if (array_key_exists('last_insemination_time', $event)) {

                    $last_insemination = strtotime(date('Y-m-d', strtotime($event['last_insemination_time']))); //last insemination event timestamp
                    $cal_start_time = strtotime(date('Y-m-d', strtotime($event['cal_start_time']))); //calving event timestamp

                    $days = round(( ($last_insemination - $cal_start_time) / 60 / 60 / 24) + BBWEB_PERIOD_GESTATION);
                    $expected_calving_interval[] = $days;
                }
            }
        }

        if ($expected_calving_interval) {
            $avg_expected = (array_sum($expected_calving_interval) / count($expected_calving_interval));
        }
        $data['expected_calving_interval'] = array(
            'range_start' => 320,
            'range_end'   => 500,
            'value'       => number_format($avg_expected, 0, '.', ''),
        );


        /*
         * Calculation for 'Heat Detection Rate'
         */
        $HDR = ((21 * $aspp) / ((($ado2 - BBWEB_VWP2 + 11) + ($ado3 - BBWEB_VWP3 + 11))/2)) * 100;
        $data['heat_detection_rate'] = array(
            'range_start' => 0,
            'range_end'   => 100,
            'value'       => number_format($HDR, 1, '.', ''),
        );

        /*
         * Calculation for 'Conception Rate'
         */
        $conception_rate = 0;
        $temp_conception_rate_data = $this->mdl_kpi->get_conception_rate_data();
        $conception_rate_data = array();
        foreach ($temp_conception_rate_data as $key => $value) {
            if (!array_key_exists($value['idanimal'], $conception_rate_data)) {
                $conception_rate_data[$value['idanimal']] = array();
            }
            $conception_rate_data[$value['idanimal']][] = $value;
        }

        $inseminations = array();
        foreach ($conception_rate_data as $idanimal => $animal_events) {
            foreach ($animal_events as $key => $value) {
                if ($value['idevent'] == CalvingEvent) { // calving event.
                    break;
                }
                if ($value['idevent'] == PregnancyCheckEvent) {
                    if (($value['info'] == 'PREGNANT' || $value['info'] == 'AUTOMATIC_YES')) {
                        if (!array_key_exists($value['idanimal'], $inseminations)) {
                            $inseminations[$value['idanimal']] = 0;
                        }
                    } elseif (!array_key_exists($value['idanimal'], $inseminations)) {
                        break;
                    }
                }
                if ($value['idevent'] == InseminationEvent) {
                    if (!array_key_exists($value['idanimal'], $inseminations)) {
                        break;
                    }
                    $inseminations[$value['idanimal']] += 1;
                }
            }
        }

        if ($inseminations) {
            $conception_rate = (count($inseminations) / array_sum($inseminations)) * 100;
        }
        $data['conception_rate'] = array(
            'range_start' => 0,
            'range_end'   => 80,
            'value'       => number_format($conception_rate, 1, '.', ''),
        );

        /*
         * Calculation for 'Pregnancy Rate'
         */
        $pregnancy_rate = ($HDR * $conception_rate) / 100;
        $data['pregnancy_rate'] = array(
            'range_start' => 0,
            'range_end'   => 60,
            'value'       => number_format($pregnancy_rate, 1, '.', ''),
        );

        $data                         = extend_range($data);
        //get logged in user widgets
        $data['has_widgets_data']     = $this->mdl_dashboard->get_user_widget($user_id);
        //get service messages
        $data['service_messages']     = $this->mdl_dashboard->get_service_messages();
        if (!$data['service_messages']) {
            $response                 = $this->mdl_dashboard->load_default_message();
            $data['service_messages'] = $this->mdl_dashboard->get_service_messages();
        }
        //push KPI values if kpi messages found
        foreach ($data['service_messages'] as $key => $value) {
            if ($value['category'] === 'KPI' && array_key_exists($value['descrizione'], $data)) {
                $data['service_messages'][$key]['value'] = $data[$value['descrizione']]['value'];
            }
        }
        //get information messages
        $data['information_messages']     = $this->mdl_dashboard->get_information($user_id);
        if (!$data['information_messages']) {
            $_res                         = $this->mdl_dashboard->load_information_message($user_id);
            $data['information_messages'] = $this->mdl_dashboard->get_information($user_id);
        }
        //get all information messages for information selection list
        $data['all_information_messages'] = $this->mdl_dashboard->all_information();
        foreach ($data['all_information_messages'] as $key => $value) {
            $data['all_information_messages'][$key]['checked'] = 0;
            foreach ($data['information_messages'] as $k => $v) {
                if ($value['idinfo'] == $v['idinfo']) {
                    $data['all_information_messages'][$key]            = $v;
                    $data['all_information_messages'][$key]['checked'] = 1;
                }
                $data['all_information_messages'][$key]['calc_data'] = $this->mdl_dashboard->get_milking_info('', $value['idinfo']);
            }
        }
        //get group list for group selection in information messages list
        $data['groups']           = $this->mdl_dashboard->get_group_details();
        //get animal messages
        $animal_messages_response = $this->mdl_dashboard->getAnimalMessages(true);
        if (!$animal_messages_response) {
            $_res                     = $this->mdl_dashboard->GetMessageAttentionStatus();
            $animal_messages_response = $this->mdl_dashboard->getAnimalMessages(true);
        } else {
            //check if new data found in animals_attentions table
            $animal_messages_response = $this->mdl_dashboard->sync_animals_messages();
        }
        $data['animal_messages'] = array();
        foreach ($animal_messages_response as $key => $value) {
            $data['animal_messages'][$value['number_animal']][$value['idanimal']][$value['descrizione']] = $value['idmessage'];
            ksort($data['animal_messages']);
        }
        load_views('dashboard', $data);
    }
    public function dashboard_messages()
    {
        $data    = array();
        if (isset($_POST['service_submit']) && ($_POST['service_submit'])) {
            $selected = (isset($_POST['serivce_checkbox'])) ? array_keys($_POST['serivce_checkbox']) : '';
            $response = $this->mdl_dashboard->update_checkbox_value($selected);
            if ($response) {
                $data['msg']       = 'Successfully Updated';
                $data['msg_class'] = 'alert-success';
            } else {
                $data['msg']       = 'Something went wrong , Try again';
                $data['msg_class'] = 'alert-danger';
            }
        }
        $data['service_messages'] = $this->mdl_dashboard->get_service_messages();
        $data['animal_messages']  = $this->mdl_dashboard->getAnimalMessages();
        load_views('dashboard_messages', $data);
    }

    /**
     * Save Selected widgets
     * @todo rearrange code
     */
    public function save_widget()
    {
        $user_id      = $this->_user_id;
        $user_widgets = $this->mdl_dashboard->get_user_widget($user_id);
        if (isset($_POST) && isset($_POST['delete'])) {
            $delete_link[] = $_POST['delete'];
            $response      = $this->mdl_dashboard->delete_user_widget($user_widgets, $delete_link, 'url');
            if ($response) {
                $result['msg']       = 'Successfully Deleted';
                $result['msg_class'] = 'alert-success';
            } else {
                $result['msg']       = 'failed to delete';
                $result['msg_class'] = 'alert-danger';
            }
            echo json_encode($result);
            die;
        }
        if (isset($_POST) && (isset($_POST['checkedvalues']) || isset($_POST['uncheckedvalues']))) {
            $checkedvalues = $uncheckedvalues = $url = array();
            if (isset($_POST['checkedvalues']) && $_POST['checkedvalues']) {
                $checkedvalues = $_POST['checkedvalues'];
            }
            if (isset($_POST['uncheckedvalues']) && $_POST['uncheckedvalues']) {
                $uncheckedvalues = $_POST['uncheckedvalues'];
            }
            if ((isset($_POST['url']) && $_POST['url']) &&  (isset($_POST['url_name']) && $_POST['url_name'])) {
                $url['url'] = $_POST['url_name'].'~'.$_POST['url'];
                if ($checkedvalues) {
                    $checkedvalues = array_merge($checkedvalues, $url);
                } else {
                    $checkedvalues['url']      = $url['url'];
                }
            }
            if ($user_widgets && $checkedvalues) {
                $final_value  = array();
                foreach ($user_widgets as $key => $value) {
                    if ($value['class_name'] === 'url') {
                        $final_value[] = $value['custom_value_1'];
                    } else {
                        $final_value[] = $value['class_name'];
                    }
                }
                $checkedvalues = array_diff($checkedvalues, $final_value);
            } else {
                $response = $this->mdl_dashboard->delete_user_widget($user_widgets, $uncheckedvalues);
            }
            $response = $this->mdl_dashboard->insert_widget($checkedvalues, $uncheckedvalues, $user_id);
            if ($response) {
                $result['msg']       = 'widgets area successfully changed';
                $result['msg_class'] = 'alert-success';
            } else {
                $result['msg']       = 'failed to changed widgets area';
                $result['msg_class'] = 'alert-danger';
            }
            echo json_encode($result);
            die;
        }
    }
}
