<?php

class Mdl_dashboard extends CI_Model
{
    /**
     * insert selected widget.
     *
     * @param Array  $checkedvalues   array of selected widget
     * @param Array  $uncheckedvalues array of unselected widget
     * @param int    $user_id         logged in user id
     *
     * @return Boolean
     * @todo Optimize Code
     */
    public function insert_widget($checkedvalues, $uncheckedvalues, $user_id)
    {
        $sql = "DELETE FROM users_has_widgets WHERE user_id = '".$user_id."' AND idwidgets <= 7";
        $response = $this->db->query($sql);
        foreach ($checkedvalues as $key => $value) {
            $custom_value_1 = 0;
            $custom_value_2 = 0;
            if ($key === 'url') {
                $arr = explode('~', $value);
                $value = 8;
                $custom_value_1 = $arr[0];
                $custom_value_2 = $arr[1];
            }
            $data['user_id']        = $user_id;
            $data['idwidgets']      = $value;
            $data['custom_value_1'] = $custom_value_1;
            $data['custom_value_2'] = $custom_value_2;
            $this->db->insert('users_has_widgets', $data);
        }
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Loggedin User widgets data.
     */
    public function get_user_widget($user_id, $param = '')
    {
        $this->db->select('w.*,u_w.*');
        $this->db->from('widgets w');
        $this->db->join('users_has_widgets u_w', 'u_w.idwidgets = w.idwidgets', 'left');
        $this->db->where('u_w.user_id', $user_id);
        if (isset($param) && $param) {
            $this->db->where('w.class_name', $param);
        }
        $this->db->order_by('w.class_name asc');
        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * delete widget.
     */
    public function delete_user_widget($user_widgets, $uncheckedvalues, $widgets_type = '')
    {
        $param = (isset($widgets_type) && $widgets_type) ? 'custom_value_1' : 'idwidgets';
        foreach ($user_widgets as $key => $value) {
            if (in_array($value[$param], $uncheckedvalues)) {
                $sql = "DELETE FROM users_has_widgets WHERE ".$param."='".$value[$param]."'";
                $response = $this->db->query($sql);
            }
        }

        return true;
    }

    /**
     * get_service_messages will return service type messages
     * @return Array
     */
    public function get_service_messages()
    {
        $this->db->select('m.*,m_l.*,COUNT(`m`.`idmessage`) AS m_count');
        $this->db->from('messages_log m_l');
        $this->db->join('messages m', 'm_l.idmessage = m.idmessage', 'left');
        // $this->db->where('m.role_id', $role_id);
        $this->db->where('m.type', 'service');
        $this->db->group_by('m.idmessage');
        $this->db->order_by('m.descrizione asc');
        return $this->db->get()->result_array();
    }

    /**
     * get_information will return inforamtions with calculated values
     * @param  int $user_id    logged in user id
     * @return Array
     */
    public function get_information($user_id)
    {
        $this->db->select('i.*,u_i.*');
        $this->db->from('informations i');
        $this->db->join('users_informations u_i', 'u_i.idinfo = i.idinfo', 'left');
        $this->db->where('u_i.user_id', $user_id);
        $this->db->order_by('i.idinfo asc');
        $query     = $this->db->get();
        $info_data = $query->result_array();
        foreach ($info_data as $key => $value) {
            $info_data[$key]['calc_data'] = $this->get_milking_info($value);
        }
        return $info_data;
    }

    /**
     * all_information will return all informarion for information list
     * @return Array
     */
    public function all_information()
    {
        $this->db->select('i.*');
        $this->db->from('informations i');
        $this->db->order_by('i.idinfo asc');
        $query     = $this->db->get();
        $info_data = $query->result_array();
        foreach ($info_data as $key => $value) {
            $info_data[$key]['custom_value_1'] = 0;
            $info_data[$key]['custom_value_2'] = 0;
            $info_data[$key]['calc_data']      = 0;
        }
        return $info_data;
    }

    /**
     * load_information_message will insert all information for logged in user
     * @param  int $user_id logged in user id
     * @return Boolean
     */
    public function load_information_message($user_id)
    {
        $_values = $this->db->get('informations')->result_array();
        foreach ($_values as $key => $value) {
            $data['user_id']        = $user_id;
            $data['idinfo']         = $value['idinfo'];
            $data['custom_value_1'] = 0;
            $data['custom_value_2'] = 0;
            $this->db->insert('users_informations', $data);
        }
        return true;
    }

    /**
     * StoreCheckedInfo will checked infrormation from list
     * @param  int $user_id
     * @param  string $selected
     * @param  string $period
     * @param  string $groups
     * @return Boolean
     */
    public function StoreCheckedInfo($user_id, $selected = '', $period = '', $groups = '')
    {
        $where = '';
        if ($selected) {
            $values_checked = implode(",", $selected);
            $where = 'AND idinfo NOT IN ("'.$values_checked.'") ';
        }
        $sql      = "DELETE FROM users_informations WHERE user_id = '".$user_id."'".$where."";
        $response = $this->db->query($sql);
        $arr      = array(8, 9, 10, 11);
        $data     = array('custom_value_1' => 0, 'custom_value_2' => 0);
        if ($selected) {
            foreach ($selected as $key => $value) {

                if (in_array($value, $arr)) {
                    $data['idinfo']         = $value;
                    $data['custom_value_1'] = $this->get_milking_info($data);
                    $this->InsertandUpdateInfo($user_id, $data);
                }
            }
        }

        if ($period && $selected) {
            foreach ($period as $key => $value) {
                $data = array('custom_value_1' => 0, 'custom_value_2' => 0);
                if (in_array($key, $selected)) {
                    if (array_key_exists($key, $groups)) {
                        $data['custom_value_2'] = $groups[$key];
                    } else {
                        $data['custom_value_2'] = 0;
                    }
                    $data['custom_value_1'] = $value;
                    $data['idinfo']         = $key;
                    $this->InsertandUpdateInfo($user_id, $data);
                }
            }
        }
    }

    /**
     * InsertandUpdateInfo will insert and update custom values as per given values
     * @param int $user_id
     * @param int $value
     */
    public function InsertandUpdateInfo($user_id, $value)
    {
        $this->db->select('*');
        $this->db->from('users_informations');
        $this->db->where('user_id', $user_id);
        $this->db->where('idinfo', $value['idinfo']);
        $response = $this->db->get()->result_array();

        if ($response) {
            $this->db->where('user_id', $user_id);
            $this->db->where('idinfo', $value['idinfo']);
            $this->db->update('users_informations', $value);
        } else {
            $data['user_id']        = $user_id;
            $data['idinfo']         = $value['idinfo'];
            $data['custom_value_1'] = $value['custom_value_1'];
            $data['custom_value_2'] = $value['custom_value_2'];
            $this->db->insert('users_informations', $data);
        }
    }

    /**
     * get_milking_info will return calculated data as per information
     * @param  $value contains custom values arr
     * @param  string $other exstra parameter
     * @return Interger
     */
    public function get_milking_info($value, $other = '')
    {
        $sql    = '';
        $group  = '';
        $hours  = '';
        if ($value) {
            $hours  = $value['custom_value_1'];
            $group  = $value['custom_value_2'];
        }
        $idinfo = ($other) ? $other : $value['idinfo'];
        switch ($idinfo) {
            case 1:
                $sql   = "select SUM(produzione) AS sum from milking_data WHERE data_end > DATE_SUB( NOW(), INTERVAL '".$hours."' HOUR )";
                $query = $this->db->query($sql);
                $data  = $query->row();
                return $data->sum;
                break;
            case 2:
                $sql = "select SUM(produzione) AS sum from milking_data  LEFT JOIN `animals_groups` ON `animals_groups`.`idanimal` = `milking_data`.`idanimal` WHERE data_end > DATE_SUB( NOW(), INTERVAL '".$hours."' HOUR ) AND `animals_groups`.idgroup = '".$group."'";
                $query = $this->db->query($sql);
                $data  = $query->row();
                return $data->sum;
                break;
            case 3:
                $sql = "select AVG (UNIX_TIMESTAMP(data_findat)-UNIX_TIMESTAMP(data_enter)) as average from milking_data WHERE data_end > DATE_SUB( NOW(), INTERVAL '".$hours."' HOUR )";
                $query = $this->db->query($sql);
                $data  = $query->row();
                return $data->average;
                break;
            case 4:
                $sql = "select SUM(produzione) from milking_data WHERE data_end > DATE_SUB( NOW(), INTERVAL '".$hours."' HOUR )";
                return $hours;
                break;
            case 5:
                $sql = "select AVG (UNIX_TIMESTAMP(data_findat)-UNIX_TIMESTAMP(data_enter)) AS average, count(*)  AS count from milking_data WHERE data_end > DATE_SUB( NOW(), INTERVAL '".$hours."' HOUR )";

                $query = $this->db->query($sql);
                $data  = $query->row();
                if ($data->average == 0) {
                    return 0;
                }
                $return_val = $data->average/$data->count;
                return $return_val;
                break;
            case 6:
                $sql = "select SUM(produzione) from milking_data WHERE data_end > DATE_SUB( NOW(), INTERVAL '".$hours."' HOUR )";
                return 0;
                break;
            case 7:
                $sql = "select COUNT(idanimal) AS count from milking_data WHERE data_end > DATE_SUB( NOW(), INTERVAL '".$hours."' HOUR )";
                $query = $this->db->query($sql);
                $data  = $query->row();
                return $data->count;
                break;
            case 8:
                $sql = "select count(if(velos_production_status = 'LACTATING',1,null))  as animal_lact from animals";
                $query = $this->db->query($sql);
                $data  = $query->row();
                return $data->animal_lact;
                break;
            case 9:
                $sql = "select count(idanimal) as total_animal from animals";
                $query = $this->db->query($sql);
                $data  = $query->row();
                return $data->total_animal;
                break;
            case 10:
                $sql = "select count(if(lactation=0,1,null)) as lact_zero from animals";
                $query = $this->db->query($sql);
                $data  = $query->row();
                return $data->lact_zero;
                break;
            case 11:
                $sql = "select count(idanimal) as total_animal, count(if(velos_reproduction_status = 'PREGNANT',1,null)) as count_pregnant from animals";
                $query = $this->db->query($sql);
                $data  = $query->row();
                if ($data->count_pregnant != 0) {
                    return round($data->count_pregnant/$data->total_animal, 2);
                }
                return 0;
                break;
            default:
                return $hours;
                break;
        }
    }

    /**
     * get animal message
     */
    public function getAnimalMessages($param = '')
    {
        $this->db->select('m.*,m_l.*,a.number_animal');
        $this->db->from('messages_log m');
        $this->db->join('messages m_l', 'm_l.idmessage = m.idmessage', 'left');
        $this->db->join('animals a', 'm.idanimal = a.idanimal', 'left');
        // $this->db->where('m_l.role_id', $role_id);
        $this->db->where('m_l.type', 'animal');
        if ($param) {
            $this->db->where('m.checked', 0);
        }
        $this->db->order_by('a.number_animal asc');
        return $this->db->get()->result_array();
    }

    /**
     * [insert_service_message description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function insert_service_message($data)
    {
        $this->db->insert('messages', $data);
        return true;
    }
    /**
     * update_checkbox_value
     * @param  [type] $selected [description]
     * @return Boolean
     */
    public function update_checkbox_value($selected)
    {
        $selected_data = array();
        $selected_data = $this->get_service_messages();

        $data = array();
        foreach ($selected_data as $key => $value) {
            $var = 0;
            if ($selected) {
                if (in_array($value['idmsglog'], $selected)) {
                    $var = 1;
                }
            }
            $data[] = array(
                    'checked' => $var,
                    'idmsglog'=> $value['idmsglog']
                    );
        }
        $this->db->update_batch('messages_log', $data, 'idmsglog');
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * insert_KPI_service_message method call from Cron every day
     * @param  Array $data
     * @return Boolean
     */
    public function insert_KPI_service_message($data)
    {
        foreach ($data as $key => $value) {
            $this->db->select('*');
            $this->db->from('messages');
            $this->db->where('descrizione', $value['descrizione']);
            $this->db->where('category', 'KPI');
            $query = $this->db->get()->row();
            if ($query) {
                $this->db->select('*');
                $this->db->from('messages_log');
                $this->db->where('idmessage', $query->idmessage);
                $_query = $this->db->get()->row();
                if ($_query) {
                    $sql ="DELETE FROM messages_log WHERE idmessage = '".$query->idmessage."'";
                    $response = $this->db->query($sql);
                }
                $msg_logdata['idmessage']      = $query->idmessage;
                $msg_logdata['ts']             = date('Y-m-d H:i:s');
                $msg_logdata['checked'] = 0;
                $this->db->insert('messages_log', $msg_logdata);
            }
        }
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * GetMessageAttentionStatus will call when animal messages values was empty
     */
    public function GetMessageAttentionStatus()
    {
        $datas = $this->get_attention_data();
        foreach ($datas as $key => $value) {
            $data_message = self::check_messages($value['type']);
            if ($data_message) {
                $msg_logdata['idmessage'] = $value['type'];
                $msg_logdata['idanimal']  = $value['idanimal'];
                $msg_logdata['ts']        = date('Y-m-d H:i:s');
                $msg_logdata['checked']   = 0;
                $this->db->insert('messages_log', $msg_logdata);
            }
        }
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * load_default_message can load default service message
     * @param  int $role_id
     * @return Boolean
     */
    public function load_default_message()
    {
        $_data = array();
        $default_arr = array(
                1001 => array('category' => 'Settings','descrizione' => 'General not set', 'type' => 'service'),
                1002 => array('category' => 'Settings','descrizione' => 'Networking not set', 'type' => 'service'),
                1003 => array('category' => 'Settings','descrizione' => 'KPI not set', 'type' => 'service'),
                1004 => array('category' => 'Settings','descrizione' => 'Parlour configurator not set', 'type' => 'service')
                );
        foreach ($default_arr as $key => $value) {
            $msg_logdata['idmessage']      = $key;
            $msg_logdata['ts']             = date('Y-m-d H:i:s');
            $msg_logdata['checked'] = 0;
            $this->db->insert('messages_log', $msg_logdata);
        }
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get_group_details return groups details
     */
    public function get_group_details()
    {
        $this->db->select('g.*');
        $this->db->from('groups g');
        $query = $this->db->get();
        $data = $query->result_array();
        return $data;
    }

    /**
     * sync_animals_messages will insert data in messages_log if new data found in animals_attentions
     * @return Array
     */
    public function sync_animals_messages()
    {
        $datas = $this->get_attention_data();
        foreach ($datas as $_key => $_value) {
            $res = $this->check_attention_data($_value['idanimal'], $_value['type']);
        }
        $msg_log = $this->get_messages_data();
        foreach ($msg_log as $m_key => $m_value) {
            $res = $this->delete_messages_log($m_value['idanimal'], $m_value['idmessage']);
        }
        return $this->getAnimalMessages(true);
    }

    /**
     * get_attention_data return animals attentions table data
     */
    public function get_attention_data()
    {
        $datas = $this->db->get('animals_attentions')->result_array();
        return $datas;
    }

    /**
     * get_messages_data return messages log table data
     */
    public function get_messages_data()
    {
        $datas = $this->db->where('idanimal IS NOT NULL', null, false)->get('messages_log')->result_array();
        return $datas;
    }

    /**
     * check_attention_data call when any new data found in animals_attention table
     * @param  $idanimal
     * @param  $idmessage
     * @return Boolean
     */
    public function check_attention_data($idanimal, $idmessage)
    {
        $data         = $this->db->select('*')->where('idanimal', $idanimal)->where('idmessage', $idmessage)->get('messages_log')->row();
        $data_message = self::check_messages($idmessage);
        if (!$data && $data_message) {
            $msg_logdata['idmessage'] = $idmessage;
            $msg_logdata['idanimal']  = $idanimal;
            $msg_logdata['ts']        = date('Y-m-d H:i:s');
            $msg_logdata['checked']   = 0;
            $this->db->insert('messages_log', $msg_logdata);
        }
        return true;
    }

    /**
     * check_messages check message exists or not in messges table
     * @param  $idmessage
     * @return Boolean
     */
    public function check_messages($idmessage)
    {
        $data_message = $this->db->select('*')->where('idmessage', $idmessage)->get('messages')->row();

        if ($data_message) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * delete from messages log table if that was not found in animal attentions table
     */
    public function delete_messages_log($idanimal, $idmessage)
    {
        $data  = $this->db->select('*')->where('idanimal', $idanimal)->where('type', $idmessage)->get('animals_attentions')->row();
        if (!$data) {
            $sql = "DELETE FROM messages_log WHERE idanimal = '$idanimal' AND idmessage = '$idmessage'";
            $return = $this->db->query($sql);
            if ($this->db->affected_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }
}
