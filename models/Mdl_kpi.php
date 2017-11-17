<?php

class Mdl_kpi extends CI_Model
{

    public function get_herd_composion($reprod_status = '', $prod_status = '', $lact_no = '', $is_table = false)
    {
        $sql = "SELECT `animals`.`idanimal`,`animals`.`number_animal`,`animals`.`lactation`,`animals`.`velos_reproduction_status`,`animals`.`velos_production_status`,`groups`.`idgroup`,`groups`.`name`, `animals`.lactation as total_lactation, x.DIM AS DIM
                FROM `animals`
                LEFT JOIN (
                    SELECT `animals_events`.`idanimal`, DATEDIFF( CURRENT_TIMESTAMP , MAX( animals_events.timestamp ) ) AS DIM
                            FROM  `animals_events`
                            WHERE `animals_events`.`idevent` = ".CalvingEvent."
                            GROUP BY  `animals_events`.`idanimal`
                            ORDER BY  `animals_events`.`timestamp` DESC
                    ) as x  ON `x`.`idanimal`=`animals`.`idanimal`";

        $sql .= " LEFT JOIN `animals_groups` ON `animals_groups`.`idanimal` = `animals`.`idanimal` LEFT JOIN `groups` ON `animals_groups`.`idgroup` = `groups`.`idgroup`";

        $sql .= " WHERE  `animals`.`is_present` = 'Y' AND ";

        $where = '';

        // Production Status Filter.
        if ($prod_status) {
            if ($prod_status=='INMILK') {
                $where .= "  ( `animals`.`velos_production_status` != 'DRY' ) AND  ( `animals`.`velos_reproduction_status` != 'DRY' ) AND ";
            } elseif ($prod_status=='DRY') {
                $where .= "  ( `animals`.`velos_production_status` = 'DRY' ) AND ";
            }
        }

        // Reproduction Status Filter.
        if ($reprod_status) {
            $where .= " `animals`.`velos_reproduction_status` = '".$reprod_status."' AND ";
        }

        // Lactation Filter from Array.
        if ($lact_no) {
            if (is_array($lact_no)) {
                $lact = array();
                foreach ($lact_no as $key => $value) {
                    if ($value == 3) {
                        $lact[] = ' `animals`.lactation >= '.$value;
                    } else {
                        $lact[] = ' `animals`.lactation = '.$value;
                    }
                }
                if ($lact) {
                    $where .= ' ( '.implode(' OR ', $lact).' ) AND ';
                }
            } else {
                if ($lact_no == 3) {
                    if (isset($_GET['single']) && ($_GET['single'])) {
                        $where .= ' `animals`.lactation = '.$lact_no.' AND ';
                    } else {
                        $where .= ' `animals`.lactation >= '.$lact_no.' AND ';
                    }
                } else {
                    $where .= ' `animals`.lactation = '.$lact_no.' AND ';
                }
            }
        } else {
            $where .= ' `animals`.lactation > 0 AND ';
        }

        $sql .= $where;
        $sql .= ' 1 order by `animals`.`lastupdate_animal` DESC';
        //echo $sql; die;
        $data = $this->db->query($sql);
        $animal_data_db = $data->result_array();
        $animal_data = array();


        $animals_getevents = array();
        foreach ($animal_data_db as $animal) {
            $animal_data[$animal['idanimal']] = $animal;
            // Generate list of animals for table data.
            if ($is_table &&
                $animal['velos_reproduction_status'] == 'PREGNANT' ||
                $animal['velos_reproduction_status'] == 'INSEMINATED') {

                $animals_getevents[] = $animal['idanimal'];
            }
        }

        // Get data for table fields.
        if ($animal_data && $animals_getevents) {

            $lastevents = $this->get_last_events($animals_getevents, array(InseminationEvent,DryoffEvent));
            $no_inseminations = $this->get_no_inseminations($animals_getevents);

            foreach ($animal_data as $idanimal => $animal) {

                if (array_key_exists($idanimal, $lastevents)) {

                    $last_dryoff        = isset($lastevents[$idanimal][DryoffEvent]['last_timestamp']) ? $lastevents[$idanimal][DryoffEvent]['last_timestamp'] : '';
                    //$count_insemination = isset($lastevents[$idanimal][InseminationEvent]['count_events']) ? $lastevents[$idanimal][InseminationEvent]['count_events'] : '';
                    $last_insemination  = isset($lastevents[$idanimal][InseminationEvent]['last_timestamp']) ? $lastevents[$idanimal][InseminationEvent]['last_timestamp'] : '';

                    switch ($animal['velos_reproduction_status']) {
                        case 'PREGNANT':
                            $animal_data[$idanimal]['last_dryoff'] = $last_dryoff;
                            //$animal_data[$idanimal]['count_insemination'] = $count_insemination;
                            $animal_data[$idanimal]['last_insemination'] = $last_insemination;
                            break;

                        case 'INSEMINATED':
                           // $animal_data[$idanimal]['count_insemination'] = $count_insemination;
                            $animal_data[$idanimal]['last_insemination'] = $last_insemination;
                            break;
                    }

                }

                if (array_key_exists($idanimal, $no_inseminations)) {
                    $animal_data[$idanimal]['count_insemination'] = $no_inseminations[$idanimal];
                } else {
                    $animal_data[$idanimal]['count_insemination'] = 0;
                }
            }
        }

        return $animal_data;
    }

    public function get_herd_composion_by_lactation($reprod_status = '')
    {
        $sql = 'SELECT  `animals`.idanimal,  `animals`.velos_reproduction_status, `animals`.lactation as total_lactation
                FROM  `animals` ';
        if ($reprod_status) {
            $sql .= " WHERE `velos_reproduction_status` = '".$reprod_status."'";
        }

        $sql .= ' ORDER BY  `animals`.`velos_reproduction_status` ASC ';

        $data = $this->db->query($sql);

        return $data->result_array();
    }

    public function get_calving_data($event = '')
    {
        $sql = 'SELECT COUNT( * )  as total , idanimal
                            FROM  `animals_events`
                            ';
        $sql .= ' WHERE `idevent` = '.CalvingEvent;
        $sql .= ' GROUP BY  `idanimal`  order by `timestamp` asc';

        $data = $this->db->query($sql);

        return $data->result_array();
    }

    public function get_lactation()
    {
        $sql = "SELECT COUNT( * ) AS count_lactation,  `lactation`
                FROM  `animals` WHERE  `animals`.`is_present` = 'Y' AND `animals`.lactation > 0 ";
        $sql .= " GROUP BY  `lactation`  order by `lactation` asc ";
        $data = $this->db->query($sql);
        return $data->result_array();
    }

    public function get_reproductive_bar_chart_data()
    {
        $sql = "SELECT count(*) AS total_lactation_by_status,`velos_reproduction_status`,`lactation`  FROM  `animals` WHERE  `animals`.`is_present` = 'Y' AND `animals`.lactation > 0 GROUP BY  `velos_reproduction_status`  ,`lactation` ";
        $data = $this->db->query($sql);
        return $data->result_array();
    }

    public function get_average_lactations()
    {
        $sql = "SELECT sum(`lactation`) as total_lactation,  count(*) as total_animals, (sum(`lactation`)/count(*)) as avg_nr_of_lactations
                FROM  `animals`
                WHERE lactation > 0 and `is_present` = 'Y'";
        $data = $this->db->query($sql);
        $result = $data->result_array();
        $avg_nr_of_lactations = $result[0]['avg_nr_of_lactations'];

        return $avg_nr_of_lactations;
    }

    public function get_dim($lact_no = '', $extra = false)
    {
        $sql = "SELECT  `animals`.*,`groups`.*, MAX(animals_events.timestamp) , DATEDIFF(
                CURRENT_TIMESTAMP , MAX( animals_events.timestamp ) ) AS DIM
                FROM  `animals_events`
                LEFT JOIN  `animals` ON  `animals`.`idanimal` =  `animals_events`.`idanimal`
                LEFT JOIN  `events` ON  `events`.`idevent` =  `animals_events`.`idevent`
                LEFT JOIN `animals_groups` ON `animals_groups`.`idanimal` = `animals`.`idanimal`
                LEFT JOIN `groups` ON `animals_groups`.`idgroup` = `groups`.`idgroup`";

            $sql .= "WHERE  `animals`.`is_present` =  'Y' AND ( `animals`.`velos_reproduction_status` != 'DRY' AND `animals`.`velos_production_status` != 'DRY' )";

        $sql .=  "AND  `events`.`idevent` = ".CalvingEvent." AND ";

        // $sql .= " LEFT JOIN `animals_groups` ON `animals_groups`.`idanimal` = `animals`.`idanimal` LEFT JOIN `groups` ON `animals_groups`.`idgroup` = `groups`.`idgroup`";
        $where = '';
        if ($lact_no) {
            if (is_array($lact_no)) {
                $lact = array();
                foreach ($lact_no as $key => $value) {
                    if ($value == 3) {
                        $lact[] = ' `animals`.lactation >= '.$value;
                    } else {
                        $lact[] = ' `animals`.lactation = '.$value;
                    }
                }
                if ($lact) {
                    $where .= ' ( '.implode(' OR ', $lact).' ) AND ';
                }
            } else {
                if ($lact_no == 3) {
                    $where .= ' `animals`.lactation >= '.$lact_no.' AND ';
                } else {
                    $where .= ' `animals`.lactation = '.$lact_no.' AND ';
                }
            }
        }
        $sql .= $where;

        $sql .= '1 GROUP BY  `animals_events`.`idanimal`
                ORDER BY  `animals_events`.`timestamp` DESC ';
        //echo $sql; die;
        $data = $this->db->query($sql);
        $animal_data_db = $data->result_array();
        $animal_data = array();

        $pregnant_animals = array();
        foreach ($animal_data_db as $animal) {
            $animal_data[$animal['idanimal']] = $animal;
            if ($animal['velos_reproduction_status'] == 'PREGNANT' ||
                $animal['velos_reproduction_status'] == 'INSEMINATED') {
                $pregnant_animals[] = $animal['idanimal'];
            }
        }

        if ($extra) {
            if ($animal_data && $pregnant_animals) {

                $lastevents_data = $this->get_last_events($pregnant_animals, array(InseminationEvent,DryoffEvent));

                foreach ($animal_data as $idanimal => $animal) {
                    if (array_key_exists($idanimal, $lastevents_data)) {

                        $animal_data[$idanimal]['last_dryoff'] = isset($lastevents_data[$idanimal][DryoffEvent]['last_timestamp']) ? $lastevents_data[$idanimal][DryoffEvent]['last_timestamp'] : '';

                        $animal_data[$idanimal]['count_insemination'] = isset($lastevents_data[$idanimal][InseminationEvent]['count_events']) ? $lastevents_data[$idanimal][InseminationEvent]['count_events'] : '';
                        $animal_data[$idanimal]['last_insemination'] = isset($lastevents_data[$idanimal][InseminationEvent]['last_timestamp']) ? $lastevents_data[$idanimal][InseminationEvent]['last_timestamp'] : '';
                    }
                }
            }
        }
        return $animal_data;
    }

    public function get_all_events($event = '')
    {
        $sql = 'SELECT  `animals_events`.`idevent`, `animals_events`.`idanimal`,`animals_events`.`timestamp`,`animals_events`.`info`, `animals`.`velos_production_status`,`animals`.`velos_reproduction_status`,`animals`.`lactation` FROM animals_events LEFT JOIN animals ON animals.idanimal = animals_events.idanimal';
        if ($event) {
            $sql .= ' WHERE `animals_events`.`idevent` = '.$event.' ';
        }

        $sql .= ' ORDER BY `animals_events`.`timestamp`  ASC';

        $data = $this->db->query($sql);

        return $data->result_array();
    }

    public function get_expected_calving_data()
    {
        $sql = "SELECT  `animals_events`.*,  `pregnant`.*
                FROM  `animals_events`
                LEFT JOIN
                ( SELECT idanimal, max(timestamp) as time_last_pregnant  FROM `animals_events` WHERE `idevent` = ".PregnancyCheckEvent ." and `info` IN ('AUTOMATIC_YES', 'PREGNANT' )
                GROUP BY  `animals_events`.`idanimal`
                ORDER BY `animals_events`.`idanimal` ASC ) as pregnant
                ON  `pregnant`.`idanimal` =  `animals_events`.`idanimal` and `pregnant`.`time_last_pregnant` >= `animals_events`.`timestamp`
                WHERE `pregnant`.`time_last_pregnant` IS NOT NULL and `animals_events`.`idevent` IN (".CalvingEvent.",".InseminationEvent.") and `animals_events`.`idanimal` IN (SELECT `idanimal` FROM `animals` WHERE `velos_production_status` = 'LACTATING')
                ORDER BY `animals_events`.`idanimal`, `animals_events`.`timestamp`  ASC";

        $data = $this->db->query($sql);

        return $data->result_array();
    }

    public function get_aspp_data()
    {
        $sql = "SELECT  `animals_events`.*,  `pregnant`.*
                FROM  `animals_events`
                LEFT JOIN
                ( SELECT idanimal, max(timestamp) as time_last_pregnant  FROM `animals_events` WHERE `idevent` = ".PregnancyCheckEvent ." and `info` IN ('AUTOMATIC_YES', 'PREGNANT' )
                GROUP BY  `animals_events`.`idanimal`
                ORDER BY `animals_events`.`idanimal` ASC ) as pregnant
                ON  `pregnant`.`idanimal` =  `animals_events`.`idanimal` and `pregnant`.`time_last_pregnant` >= `animals_events`.`timestamp`
                WHERE `pregnant`.`time_last_pregnant` IS NOT NULL and `animals_events`.`idevent` IN (".InseminationEvent.", ".PregnancyCheckEvent.")  and `animals_events`.`idanimal` IN (SELECT `idanimal` FROM `animals` WHERE `velos_production_status` = 'LACTATING')
                ORDER BY `animals_events`.`idanimal`, `animals_events`.`timestamp`  ASC";

        $data = $this->db->query($sql);

        return $data->result_array();
    }

    public function get_conception_rate_data()
    {
        $sql = "SELECT  `animals_events`.*
                FROM  `animals_events`
                WHERE `animals_events`.`idevent` IN (".CalvingEvent.",".InseminationEvent.",".PregnancyCheckEvent.") and `animals_events`.`idanimal` IN (SELECT `idanimal` FROM `animals`)
                ORDER BY `animals_events`.`idanimal`, `animals_events`.`timestamp` DESC";

        $data = $this->db->query($sql);

        return $data->result_array();
    }

    public function get_last_events($animal_ids = array(), $events = array())
    {

        $sql = "SELECT count(*) as count_events, max(timestamp) as last_timestamp, idanimal, idevent FROM animals_events WHERE 1";

        if ($animal_ids) {
            $sql .= " AND `animals_events`.`idanimal` IN (".implode(',', $animal_ids).") ";
        }

        if ($events) {
            $sql .= " AND `animals_events`.`idevent` IN (".implode(',', $events).") ";
        }

        $sql .= "GROUP BY idanimal, idevent ORDER BY `animals_events`.`idanimal` DESC";

        $data = $this->db->query($sql);
        $db_data = $data->result_array();

        $lastevents_data = array();
        foreach ($db_data as $row) {
            $lastevents_data[$row['idanimal']][$row['idevent']] = $row;
        }
        return $lastevents_data;
    }

    public function get_no_inseminations($animal_ids = array())
    {
        $sql = "SELECT  `animals_events`.*
                FROM  `animals_events`
                WHERE `animals_events`.`idevent` IN (".CalvingEvent.",".InseminationEvent.")";

        if ($animal_ids) {
            $sql .= " AND `animals_events`.`idanimal` IN (".implode(',', $animal_ids).") ";
        }

        $sql .= " ORDER BY `animals_events`.`idanimal`, `animals_events`.`timestamp` DESC";

        $data = $this->db->query($sql);

        $data = $data->result_array();
        $inseminations = array();
        $animals_events = array();
        foreach ($data as $key => $value) {
            $animals_events[$value['idanimal']][] = $value;
        }

        foreach ($animals_events as $idanimal => $animal_events) {

            foreach ($animal_events as $value) {

                if ($value['idevent'] == CalvingEvent) { // calving event.
                    break;
                }
                if ($value['idevent'] == InseminationEvent) {
                    if (!array_key_exists($value['idanimal'], $inseminations)) {
                        $inseminations[$value['idanimal']] = 1;
                    } else {
                        $inseminations[$value['idanimal']] += 1;
                    }
                }
            }
        }

        return $inseminations;
    }

}
