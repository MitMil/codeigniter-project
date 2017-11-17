<?php

class Kpi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_role', '', true);
        $this->load->model('mdl_module', '', true);
        $this->load->model('mdl_kpi', '', true);
    }

    public function index()
    {
        redirect('kpi/herd_composition');
    }

    public function herd_composition($graph_type = '')
    {
        $data = array();
        $data['graph_type'] = $graph_type;

        // Get lactation filter or set default
        if (isset($_GET['lactation']) && $_GET['lactation']) {
            $data['lactation'] = $_GET['lactation'];
        } else if (isset($_GET['is_filter'])) {
            $data['lactation'] = array();
        } else {
            $data['lactation'] = array(1, 2, 3);
        }

        $flag_table_data = false;

        $reprod_status = '';
        if (isset($_GET['reprod_status']) && $_GET['reprod_status']) {
            $flag_table_data = true;
            if ($_GET['reprod_status']!='ALL') {
                $reprod_status = $_GET['reprod_status'];
            }
        }

        $prod_status = '';
        if (isset($_GET['prod_status']) && ($_GET['prod_status'])) {
            $prod_status = $_GET['prod_status'];
            $flag_table_data = true;
        }

        $data['reprod_status'] = $reprod_status;
        $data['prod_status']   = $prod_status;


        if ($graph_type) {
            switch ($graph_type) {
                case 'dim':
                    // Only load table data if clicked on graph.
                    $flag_table_data = false;
                    if (isset($_GET['dim']) && $_GET['dim']) {
                        $flag_table_data = true;
                    }
                    $data['dim_data'] = $this->mdl_kpi->get_dim($data['lactation'], $flag_table_data);
                    load_views('kpi/herd_composition_dim', $data);
                    break;

                default:
                    # code...
                    break;
            }
        } else {

            if ($flag_table_data) {
                $data['herd_composition_data'] = $this->mdl_kpi->get_herd_composion($reprod_status, $prod_status, $data['lactation'], true);
                $data['page_name'] = 'Herd Composition';
                load_views('kpi/herd_composition_event', $data);
            } else {
                $data['herd_composition_data'] = $this->mdl_kpi->get_herd_composion($reprod_status, $prod_status, $data['lactation']);
                $data['average_lactations'] = $this->mdl_kpi->get_average_lactations($data['lactation']);
                load_views('kpi/herd_composition', $data);
            }
        }
    }

    public function culling_youngstock()
    {
        $data = array();
        load_views('kpi/culling_youngstock', $data);
    }

    public function lactation()
    {

        if (isset($_GET['lactation'][0])) {

            $data = array();
            $data['lactation'] = $_GET['lactation'];

            $reprod_status = '';
            if (isset($_GET['reprod_status']) && ($_GET['reprod_status'])) {
                $reprod_status = $_GET['reprod_status'];
            }

            $prod_status = '';
            if (isset($_GET['prod_status']) && ($_GET['prod_status'])) {
                $prod_status = $_GET['prod_status'];
            }

            $data['reprod_status'] = $reprod_status;
            $data['prod_status']   = $prod_status;

            $data['herd_composition_data'] = $this->mdl_kpi->get_herd_composion($reprod_status, $prod_status, $data['lactation'], true);
            $data['page_name'] = 'Lactation';
            load_views('kpi/herd_composition_event', $data);
        } else {
            $data['herd_composition_lactation_only'] = $this->mdl_kpi->get_lactation();
            $data['reproductive_status_by_lactations_data'] = $this->mdl_kpi->get_reproductive_bar_chart_data();
            load_views('kpi/herd_lactation', $data);
        }

    }

    public function fertility_kpis()
    {

        // To store all graph data
        $data = array();

        // Fetch all events of all animals
        $all_events = $this->mdl_kpi->get_all_events();

        $aspp_data = array();
        $service_per_pregnancy = array();
        $do_animal = array();
        $adfc_data = array();
        $ado_data = array();

        foreach ($all_events as $event) {
            //  the inseminations in the in progress lactation
            // if ($event['velos_production_status'] == 'DRY') {
            //     continue;
            // }

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


                            // To calculate average of days for First inseminsation event after VWP.
                            // if ($adfs_temp[$idanimal][$temp]==1) {
                            //     $adfs_temp[$idanimal][$temp]['diff']=($adfs_temp[$idanimal][$temp]['diff'] - BBWEB_VWP1);

                            // } else if ($adfs_temp[$idanimal][$temp]==2) {
                            //     $adfs_temp[$idanimal][$temp]['diff']=($adfs_temp[$idanimal][$temp]['diff'] - BBWEB_VWP2);

                            // } else if ($adfs_temp[$idanimal][$temp]>=3) {
                            //     $adfs_temp[$idanimal][$temp]['diff']=($adfs_temp[$idanimal][$temp]['diff'] - BBWEB_VWP3);
                            // }
                            // if ($adfs_temp[$idanimal][$temp]['diff'] < 0) {
                            //     $adfs_temp[$idanimal][$temp]['diff'] = 0;
                            // }
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

        $data = extend_range($data);
        load_views('kpi/fertility_kpis', $data);
    }
	
	public function milking_kpis()
    {
        $data = array();
		$data['milking_efficiency'] = array();
		$data['average_milking_time']	= array();
		$data['average_time_final_tream'] = array();
		$data['medium_milk_first_2_minutes'] = array();
		$data['amount_milk_stall_per_hour'] = array();
		$data['amount_animal_stall_per_hour'] = array();
		load_views('kpi/milking_kpis', $data);
    }
}
