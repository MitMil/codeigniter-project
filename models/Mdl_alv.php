<?php

class Mdl_alv extends CI_Model
{

    public function get_groups($id = '')
    {
        $this->db->select('*');
        if ($id) {
            $this->db->where('idgroup', $id);
        }
        $this->db->from('groups');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_alv()
    {

        $sql = "SELECT alv.idlabel_data_type, ldt.velos_code, DATE( alv.timestamp ) AS DATE, HOUR( alv.timestamp ) AS HOUR , AVG( alv.value ) AS AVGT,SUM( alv.value ) AS TOTAL, COUNT( * ) AS RECORD
                FROM animals_label_values AS alv
                LEFT JOIN label_data_type AS ldt ON ldt.idlabel_data_type = alv.idlabel_data_type
                GROUP BY DATE( alv.timestamp ) , HOUR( alv.timestamp ) , alv.idlabel_data_type
                ORDER BY DATE( alv.timestamp ) DESC, HOUR( alv.timestamp ) DESC";

        $datas = $this->db->query($sql);
        return $datas->result_array();
    }


    public function get_alv_mindate()
    {

        $sql = "SELECT DATE(timestamp) as MIN_DATE
                FROM animals_label_values order by timestamp ASC limit 1";

        $datas = $this->db->query($sql);
        $data = $datas->row_array();
        //echo $this->db->last_query(); die;
        return $data['MIN_DATE'];
    }

    public function get_alv_maxdate()
    {

        $sql = "SELECT DATE(timestamp) as MAX_DATE
                FROM animals_label_values order by timestamp DESC limit 1";

        $datas = $this->db->query($sql);
        $data = $datas->row_array();
        //echo $this->db->last_query(); die;
        return $data['MAX_DATE'];
    }


    public function get_alv_hourly($data)
    {

        $sql = "SELECT alv.idlabel_data_type,ldt.velos_code, DATE( alv.timestamp ) AS DATE , HOUR( alv.timestamp ) AS HOUR , AVG( alv.value ) AS AVGT, COUNT( * ) AS Records
                FROM animals_label_values AS alv
                LEFT JOIN label_data_type AS ldt ON ldt.idlabel_data_type = alv.idlabel_data_type ";

        if (array_key_exists('idgroup', $data) && $data['idgroup']) {
            $sql .= "LEFT JOIN animals_groups AS agrp ON agrp.idanimal = alv.idanimal
                     WHERE agrp.idgroup = ".$data['idgroup']." AND";
        } else {
            $sql .= "WHERE";
        }

        $sql .= " alv.timestamp
                BETWEEN DATE(  '".$data['week_range']['start']."' )
                AND DATE(  '".$data['week_range']['end']."' )
                GROUP BY DATE( alv.timestamp ) ,HOUR( alv.timestamp ) ,  alv.idlabel_data_type";

        $datas = $this->db->query($sql);
        //echo $this->db->last_query(); die;
        return $datas->result_array();
    }

    public function get_alv_periodically($data)
    {

        $sql = "SELECT alp.*,anml.number_animal as animalNumber
                FROM animals_label_periods AS alp
                LEFT JOIN animals AS anml ON anml.idanimal = alp.idanimal ";

        if (array_key_exists('idgroup', $data) && $data['idgroup']) {
            $sql .= "LEFT JOIN animals_groups AS agrp ON agrp.idanimal = alp.idanimal
                     WHERE agrp.idgroup = ".$data['idgroup']." AND";
        } else {
            $sql .= "WHERE";
        }

        $sql .= " alp.date
                BETWEEN '".$data['week_range']['start']."'
                AND '".$data['week_range']['end']."'
                ORDER BY alp.date DESC, period DESC";

        $datas = $this->db->query($sql);
        //echo $this->db->last_query(); die;
        return $datas->result_array();
    }

    public function fill_animals_label_periods()
    {

        set_time_limit(0);
        /*
            8:00 - 10:00
            10:00 - 18:00
            18:00 - 20:00
            20:00 - 8:00 //
        */
        $periods = get_periods();
        //p($periods);
        //Fetch Max date from animals_labels_periods and calculate periods after that.

        $sql = "SELECT * FROM animals_label_periods  order by data DESC,period DESC limit 1";
        $old_data = $this->db->query($sql)->row_array();

        if ($old_data) {
            $last_update_date   = $old_data['data'];
            $last_period        = $old_data['period'];
        } else {
            $sql = "SELECT * FROM animals_label_values  order by timestamp limit 1";
            $start_data = $this->db->query($sql)->row_array();
            $last_update_date   = ($start_data['timestamp']) ? system_date_format($start_data['timestamp']) : '';
            $last_period        = 0;
        }

        if (!$last_update_date) {
            echo "<br />=====================\n\r";
            echo "<br />Error : No data found\n\r";
            echo "<br />=====================\n\r";
            return false;
        }

        $range_start_timestamp = strtotime($last_update_date);
        $range_end_timestamp   = time();
        while ($range_start_timestamp <= $range_end_timestamp) {

            $current_date = date('Y-m-d', $range_start_timestamp);
            foreach ($periods as $key => $p_data) {

                $insert = true;
                if (($current_date == $last_update_date) && $last_period >= $p_data['id']) { //check last inserted period with loop period
                    continue;
                }

                if (strtotime($p_data['start_time']) > strtotime($p_data['end_time'])) {
                    $start = $current_date.' '.$p_data['start_time'];
                    $end   = date('Y-m-d', strtotime('+1 day', strtotime($current_date))).' '.$p_data['end_time'];
                } else {
                    $start = $current_date.' '.$p_data['start_time'];
                    $end   = $current_date.' '.$p_data['end_time'];
                }

                if (($current_date == date('Y-m-d')) && time() <= strtotime($end)) { //check current date with time if today period remain
                    continue;
                }

                $return = $this->insert_period_query($start, $end, $p_data['id']);
                if ($return > 0) {
                    echo "<br />=====================<br />\n\r";
                    echo "<br />Time : ".$p_data['name']. "\n\r";
                    echo "<br />Start : ".$start. "\n\r";
                    echo "<br />end : ".$end. "\n\r";
                    echo "<br />Period : ".$p_data["id"]. "\n\r";
                    echo "<br />Record : ".$return. "\n\r";
                    echo "<br />=====================<br />\n\r";
                } else {
                    echo "<br />=====================<br />\n\r";
                    echo "<br />Time : ".$p_data['name']. "\n\r";
                    echo "<br />Start : ".$start. "\n\r";
                    echo "<br />end : ".$end. "\n\r";
                    echo "<br />Period : ".$p_data["id"]. "\n\r";
                    echo "<br />Record : ".$return. "\n\r";
                    echo "<br />=====================<br />\n\r";
                }
            }

            $range_start_timestamp = strtotime("+1 day", strtotime(date('Y-m-d', $range_start_timestamp).' 00:00:00'));
        }
    }

    public function insert_period_query($start_Date, $end_Date, $period)
    {

        $sql_query = "
                    INSERT INTO `animals_label_periods` (`idanimal`, `data`, `period`, `dim`, `production_status`, `legactivity_count`, `avg_legactivity_count`, `lying2standing_count`, `avg_lying2standing_count`, `lying_time`, `avg_lying_time`, `walking_time`, `avg_walking_time`, `standing_time`, `avg_standing_time`)

                      SELECT
                          label_pivot.idanimal,
                          label_pivot.DATE,
                          ".$period." AS period,
                          DATEDIFF( CURRENT_TIMESTAMP , MAX( animals_events.timestamp ) ) AS DIM,
                          'NULL' AS production_status,
                          MAX(CASE WHEN label_pivot.velos_code = 'LEG_ACTIVITY' THEN label_pivot.total ELSE NULL END) AS legactivity_count,
                          MAX(CASE WHEN label_pivot.velos_code = 'LEG_ACTIVITY' THEN label_pivot.average ELSE NULL END) AS avg_legactivity_count,
                          MAX(CASE WHEN label_pivot.velos_code = 'LYING_TO_STANDING_COUNT' THEN label_pivot.total ELSE NULL END) AS lying2standing_count,
                          MAX(CASE WHEN label_pivot.velos_code = 'LYING_TO_STANDING_COUNT' THEN label_pivot.average ELSE NULL END) AS avg_lying2standing_count,
                          MAX(CASE WHEN label_pivot.velos_code = 'LYING_TIME' THEN label_pivot.total ELSE NULL END) AS lying_time,
                          MAX(CASE WHEN label_pivot.velos_code = 'LYING_TIME' THEN label_pivot.average ELSE NULL END) AS avg_lying_time,
                          MAX(CASE WHEN label_pivot.velos_code = 'WALKING_TIME' THEN label_pivot.total ELSE NULL END) AS walking_time,
                          MAX(CASE WHEN label_pivot.velos_code = 'WALKING_TIME' THEN label_pivot.average ELSE NULL END) AS avg_walking_time,
                          MAX(CASE WHEN label_pivot.velos_code = 'STANDING_TIME' THEN label_pivot.total ELSE NULL END) AS standing_time,
                          MAX(CASE WHEN label_pivot.velos_code = 'STANDING_TIME' THEN label_pivot.average ELSE NULL END) AS avg_standing_time

                      FROM (
                        SELECT label_values.idanimal,DATE( label_values.TIMESTAMP ) AS DATE, data_type.velos_code,  SUM( label_values.value ) AS total, AVG( label_values.value ) AS average, COUNT( * ) AS Records
                        FROM animals_label_values AS label_values
                        LEFT JOIN label_data_type AS data_type ON data_type.idlabel_data_type = label_values.idlabel_data_type
                        WHERE label_values.TIMESTAMP BETWEEN  '".system_date_format($start_Date).' '.system_time_format($start_Date)."' AND '".system_date_format($end_Date).' '.system_time_format($end_Date)."'
                        GROUP BY label_values.idanimal, label_values.idlabel_data_type
                        ORDER BY  label_values.idanimal ASC
                      ) AS label_pivot
                      LEFT JOIN  `animals_events` ON  `animals_events`.`idanimal` =  label_pivot.idanimal AND  `animals_events`.`idevent` = -3
                      GROUP BY label_pivot.idanimal
                    ";
        $data = $this->db->query($sql_query);
        return $num_inserts = $this->db->affected_rows();
    }
}
