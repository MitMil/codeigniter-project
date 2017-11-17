<?php
class Alv extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_user', '', true);
        //If no session, redirect to login page
        if (!$this->mdl_user->check_login()) {
            redirect('login?url='.$_SERVER['REQUEST_URI'], 'refresh');
        }
        $this->load->model('mdl_role', '', true);
        $this->load->model('mdl_module', '', true);
        $this->load->model('mdl_alv', '', true);
    }

    public function index()
    {
        $data = array();
        $data['msg_class'] = '';
        $data['msg'] = '';
        $group_name = _('All');
        $data['type'] = 'hour';
        $data['min_date'] = $this->mdl_alv->get_alv_mindate(); ///for calender to disable dates
        $data['max_date'] = $this->mdl_alv->get_alv_maxdate(); ///for calender to disable dates

        if (isset($_POST) && $_POST) {

            if (isset($_POST['selected_date']) && $_POST['selected_date']) {
                $selected_date = system_date_format($_POST['selected_date']);
                $data['week'] = date("W", strtotime('+1 Day '.$selected_date));
                $data['year'] = date("o", strtotime('+1 Day '.$selected_date));
            } else {
                $data['week'] = $_POST['week'];
                $data['year'] = $_POST['year'];
            }

            $data['idgroup'] = $_POST['idgroup'];
            $data['week_range'] = x_week_range($data['week'], $data['year']);
            $data['type'] = $_POST['type'];
            //p($data);
            $group_data=$this->mdl_alv->get_groups($data['idgroup']);
            if ($group_data) {
                $group_name = $group_data[0]['name'];
            }
        } else {
            $data['week'] = date('W', strtotime($data['max_date']));
            $data['year'] = date('o', strtotime($data['max_date']));
            $data['week_range'] = x_week_range($data['week'], $data['year']);
            $data['idgroup'] = '';
        }

        $data['group_name'] = $group_name;

        $data['hourly_data'] = array();
        $data['periodically_data'] = array();

        if ($data['type'] == 'hour') {
            $data['alv_csv']=$this->mdl_alv->get_alv_hourly($data);

            if ($data['alv_csv']) {
                $formatted_data = array();
                foreach ($data['alv_csv'] as $record) {
                    $data['hourly_data'][$record['velos_code']][$record['HOUR']] = $record;
                }
            } else {
                $data['msg_class'] = "alert-success";
                $data['msg'] = _("No result found.");
            }

        } else {

            $data['alv_csv']=$this->mdl_alv->get_alv_periodically($data);
            // p($data['alv_csv']); die;
            if ($data['alv_csv']) {

                $periodically_data = array();
                foreach ($data['alv_csv'] as $record) {
                    $arr_keys = array_keys($record);
                    foreach ($arr_keys as $value) {
                        if (strpos($value, 'avg_') !== false) {
                            $periodically_data[$value][$record['period']][] = $record[$value];
                        }
                    }
                }
                $data['periodically_data'] = $periodically_data;

            } else {

                $data['msg_class'] = "alert-success";
                $data['msg'] = _("No result found.");
            }
        }
        //p($data); die;
        $data['group_data']=$this->mdl_alv->get_groups();
        load_views('alv/'.$data['type'].'_data_graphs', $data);
    }

    public function csv()
    {
        $data = array();
        $data['msg_class'] = '';
        $data['msg'] = '';
        $data['min_date'] = $this->mdl_alv->get_alv_mindate(); ///for calender to disable dates
        $data['max_date'] = $this->mdl_alv->get_alv_maxdate(); ///for calender to disable dates

        if (isset($_POST) && $_POST) {

            if (isset($_POST['selected_date']) && $_POST['selected_date']) {
                $selected_date = system_date_format($_POST['selected_date']);
                $data['week'] = date("W", strtotime('+1 Day '.$selected_date));
                $data['year'] = date("o", strtotime('+1 Day '.$selected_date));
            } else {
                $data['week'] = $_POST['week'];
                $data['year'] = $_POST['year'];
            }

            $data['idgroup'] = $_POST['idgroup'];
            $data['week_range'] = x_week_range($data['week'], $data['year']);

            $type = $_POST['submit'];
            if ($type == 'hour') {
                $data['alv_csv']=$this->mdl_alv->get_alv_hourly($data);
                if ($data['alv_csv']) {
                    $this->hourly_download($data);
                    die;
                } else {
                    $data['msg_class'] = "alert-success";
                    $data['msg'] = _("No result found.");
                }
            } else if ($type == 'period') {
                $data['alv_csv']=$this->mdl_alv->get_alv_periodically($data);
                if ($data['alv_csv']) {
                    $this->periodically_download($data);
                    die;
                } else {
                    $data['msg_class'] = "alert-success";
                    $data['msg'] = _("No result found.");
                }
            }


        } else {
            $data['week'] = date('W', strtotime($data['max_date']));
            $data['year'] = date('Y', strtotime($data['max_date']));
            $data['week_range'] = x_week_range($data['week'], $data['year']);
            $data['idgroup'] = '';
        }

        $data['group_data']=$this->mdl_alv->get_groups();

        load_views('alv/csv', $data);
    }

    public function hourly_download($data)
    {

        $group = _('All');
        if ($data['idgroup']) {
            $group_data=$this->mdl_alv->get_groups($data['idgroup']);
            if ($group_data) {
                $group = $group_data[0]['name'];
            }
        }
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Hourly_'.$group.'_'.display_datetime($data['week_range']['start'], true).'_'.display_datetime($data['week_range']['end'], true).'.csv');

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');
        // output the main Header
        $head_str = _('Hourly Report');
        fputcsv($output, array(0=>$head_str));
        // output the main Header
        $head_str = _('Week').'  ( '.display_datetime($data['week_range']['start'], true).' - '.display_datetime($data['week_range']['end'], true).' )';
        fputcsv($output, array(0=>$head_str));

        $column_array = array(_('DateTime (date and hour)'), 'LEG_ACTIVITY', 'LYING_TO_STANDING_COUNT', 'LYING_TIME', 'WALKING_TIME', 'STANDING_TIME');
        fputcsv($output, $column_array);

        if ($data['alv_csv']) {
            $formatted_data = array();
            foreach ($data['alv_csv'] as $record) {
                $formatted_data[$record['DATE']][$record['HOUR']][$record['velos_code']] = $record;
            }
           // echo '<pre>'; print_r($formatted_data); die;
            foreach ($formatted_data as $date => $hour_data) {

                foreach ($hour_data as $hr => $activity) {

                    $row = array();
                    $row[]  = display_datetime($date);
                    $i = 0;
                    $act_keys = array_keys($activity);
                    //p($act_keys);
                    foreach ($column_array as $column) {
                        if ($i != 0) {//echo $column;
                            if (in_array($column, $act_keys)) {
                                if (strpos(strtolower($column), 'time') !== false) {
                                    $row[] = gmtime_format($activity[$column]['AVGT'] * 60);
                                } else {
                                    $row[] = number_format($activity[$column]['AVGT'], 2);
                                }
                            } else {
                                if (strpos(strtolower($column), 'time') !== false) {
                                    $row[] = gmtime_format(0);
                                } else {
                                    $row[] = 0.00;
                                }
                            }
                        }
                        $i++;
                    }
                    // loop over the rows, outputting them
                    fputcsv($output, $row);
                }

            }
        }
    }
    public function periodically_download($data)
    {

        $group = _('All');
        if ($data['idgroup']) {
            $group_data=$this->mdl_alv->get_groups($data['idgroup']);
            if ($group_data) {
                $group = $group_data[0]['name'];
            }
        }
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Periodically_'.$group.'_'.display_datetime($data['week_range']['start'], true).'_'.display_datetime($data['week_range']['end'], true).'.csv');

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');
        // output the main Header
        $head_str = _('Periodically Report');
        fputcsv($output, array(0=>$head_str));
        // output the main Header
        $head_str = _('Week').'  ( '.display_datetime($data['week_range']['start'], true).' - '.display_datetime($data['week_range']['end'], true).' )';
        fputcsv($output, array(0=>$head_str));

        // output the column headings
        $columns =array();
        $columns['date'] = _('DateTime (Periods)');
        $columns['idanimal'] = _('idanimal');
        $columns['animalNumber'] = _('animalNumber');
        $columns['production_status'] = _('Production status');
        $columns['legactivity_count'] = _('Legactivity count');
        $columns['avg_legactivity_count'] = _('Avg Legactivity count');
        $columns['lying2standing_count'] = _('Lying2standing count');
        $columns['avg_lying2standing_count'] = _('Avg Lying2standing count');
        $columns['lying_time'] = _('Lying time');
        $columns['avg_lying_time'] = _('Avg Lying time');
        $columns['walking_time'] = _('Walking time');
        $columns['avg_walking_time'] = _('Avg Walking time');
        $columns['standing_time'] = _('Standing time');
        $columns['avg_standing_time'] = _('Avg Standing time');

        fputcsv($output, $columns);

        $periods = get_periods();
        if ($data['alv_csv']) {
            $periodically_data = array();
            foreach ($data['alv_csv'] as $record) {
                $row = array();
                foreach ($columns as $field => $column) {
                    if ($field == 'date') {
                        $row[] = implode(' - ', period_star_end_datetime($record['date'], $record['period'], $periods));
                    } else {
                        if (strpos($field, 'time') !== false) {
                            $row[] = gmtime_format($record[$field] * 60);
                        } else {
                            $row[] = $record[$field];
                        }
                    }
                }
                // loop over the rows, outputting them
                fputcsv($output, $row);
            }
        }
    }
}
