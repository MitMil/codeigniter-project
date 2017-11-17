<?php
class Mdl_settings extends CI_Model
{
    private $tbl_settings = 'bb_settings';

    public function get_next_insertid()
    {
        $table_name = $this->tbl_settings;
        $query = $this->db->query("SHOW TABLE STATUS WHERE name='$table_name'");
        $row = $query->row_array();
        return $row["Auto_increment"];
    }

    public function get_settings()
    {

        $this->db->from($this->tbl_settings);
        $settings = $this->db->get();
        $data = $settings->result_array();

        return $data;
    }

    public function settings_get_by_id($id, $login = false)
    {

        $this->db->from($this->tbl_settings);
        $this->db->where('id', $id);
        $settings = $this->db->get();
        $data = $settings->row_array();

        return $data;
    }

    public function update_batch($data, $field)
    {

        $upd = $this->db->update_batch($this->tbl_settings, $data, $field);
        if (!$upd) {
           // there was an error
            return false;
        } else if (!$this->db->affected_rows()) {
           // no error, but nothing changed
            return true;
        } else {
           // record changed
            return true;
        }

        return false;
    }

    public function get_milkingdevice($ip = '')
    {
        $sql = "select * from `milking_devices`";
        $sql .= " WHERE 1 ";
        if ($ip) {
            $sql .= " AND IP='".$ip."'";
        }
        $query = $this->db->query($sql);
        $row = $query->result_array();

        return $row;
    }

    public function insert_milkingdevice($db_data)
    {
        $this->db->insert('milking_devices', $db_data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function delete_milkindevice($ip)
    {
        $data = $this->get_milkingdevice($ip);
        if ($data) {
            $iddevice = $data[0]['iddevice'];
            $deleted_id = $this->delete_milkingroom($iddevice);
            $this->db->where('IP', $ip);
            $deleted_row = $this->db->delete('milking_devices');
            return true;
        }
    }

    public function insert_milkingroom($room)
    {
        $this->db->insert('milking_rooms', $room);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function delete_milkingroom($iddevice)
    {

        $this->db->where('iddevice', $iddevice);
        $deleted_row = $this->db->delete('milking_rooms');
        return $deleted_row;
    }

    /**
     * Insert/device devices and rooms data in DB
     * @param  [array] $data
     * @return [string]
     */
    public function pconfigurator($data)
    {
        $devicesWithRoomsdata = array();
        foreach ($data as $k => $v) {
            $devicesWithRoomsdata[$v['ip']][] = $v;
        }

        foreach ($devicesWithRoomsdata as $ip => $roomdata) {
            $device = $this->get_milkingdevice($ip);
            if ($device) {
                $iddevice = $device[0]['iddevice'];
                $this->delete_milkingroom($iddevice);
            } else {
                $insert_device = array();
                $insert_device['iddevice'] = '' ;
                $insert_device['model'] = '';
                $insert_device['name'] = '';
                $insert_device['IP'] = $ip;
                $insert_device['firmware_version'] = '';
                $iddevice = $this->insert_milkingdevice($insert_device);
            }

            foreach ($roomdata as $key => $room) {
                if (isset($room['action']) && $room['action']) {
                    if ($room['action'] == 'delete') {
                        $this->delete_milkindevice($room['ip']);
                        continue;
                    }
                }
                $insert_room = array();
                $insert_room['iddevice'] = $iddevice;
                $insert_room['positions_available'] = '';
                $insert_room['tipo_sala'] = $room['tipoSala'];
                $insert_room['top_left'] = isset($room['top_left']) ? $room['top_left'] : '';
                $insert_room['bottom_left'] = isset($room['bottom_left']) ? $room['bottom_left'] : '';
                $insert_room['top_right'] = isset($room['top_right']) ? $room['top_right'] : '';
                $insert_room['bottom_right'] = isset($room['bottom_right']) ? $room['bottom_right'] : '';
                $insert_room['init'] = isset($room['init']) ? $room['init'] : '';
                $insert_room['offset'] = isset($room['offset']) ? $room['offset'] : 0;

                $this->insert_milkingroom($insert_room);
            }
        }
        return true;
    }

    public function select_pconfigurator($iddevice = '')
    {
        $sql = "SELECT `milking_rooms`.* , `milking_devices`.* 
                FROM  `milking_devices`,  `milking_rooms`
                WHERE milking_devices.iddevice = milking_rooms.iddevice";
        if ($iddevice) {
            $sql .=  " and milking_devices.iddevice = ".$iddevice;
        }
        $query = $this->db->query($sql);
        $row = $query->result_array();
        return $row;
    }
    /**
     * insert_language_data
     * @param  Array $msgid array of strings
     * @return Boolean
     * @todo optimize code
     */
    public function insert_language_data($msgid, $new_code, $lang_id, $from_po_file = '', $scan = '')
    {
        $exist_data   = $this->select_language_data_byid($lang_id);
        $exist_msgid  = $exist_msgstr = $new_msgid = $new_msgstr = $data = $assign_data = $assigned_msgstr = array();
        $flag         = $flag_msgstr = false;
        if (empty($exist_data) || ($scan)) {
            $assign_data = $msgid;
            $flag        = true;
        } else {
            if (!$from_po_file) {
                return false;
            }
            foreach ($exist_data as $key => $value) {
                $exist_msgid[]                 = $value['msgid'];
                $exist_msgstr[$value['msgid']] = $value['msgstr'];
            }
            foreach ($msgid as $key => $value) {
                if ($from_po_file && $key==='') {
                    continue;
                }
                $new_msgid[]      = ($from_po_file) ? $key : $value;
                $new_msgstr[$key] = $value;
            }
            $new_msgid  = array_diff($new_msgid, $exist_msgid);
            $new_msgstr = array_diff_assoc($new_msgstr, $exist_msgstr);
            if (!empty($new_msgid)) {
                $flag        = true;
                $assign_data = $new_msgid;
            }
            if (!empty($new_msgstr)) {
                $flag_msgstr = true;
                $assigned_msgstr = $new_msgstr;
            }
        }
        if ($flag && $assign_data) {
            foreach ($assign_data as $key => $value) {
                if ($key==='') {
                    continue;
                }
                $data[] = array(
                        'msgid' => ($from_po_file && empty($exist_data)) ?  $key : $value,
                        'msgstr' => ($from_po_file && empty($exist_data)) ? $value : '',
                        'idlang' => $lang_id
                    );
            }
            $this->db->insert_batch('language_strings', $data);
        }
        if ($flag_msgstr && $assigned_msgstr) {
            foreach ($assigned_msgstr as $key => $value) {
                $data1[] = array(
                        'msgid' => $key,
                        'msgstr' =>$value,
                        'idlang' => $lang_id
                    );
            }
            $this->db->update_batch('language_strings', $data1, 'msgid');

        }
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Get selected language code data.
    */
    public function select_language_data($selected_lang_code, $lang_id)
    {
        $this->db->select('l.*,l_s.*');
        $this->db->from('languages l');
        $this->db->join('language_strings l_s', 'l_s.idlang = l.idlang', 'left');
        $this->db->where('l.code', $selected_lang_code);
        $this->db->where('l.idlang', $lang_id);
        return $this->db->get()->result_array();
        $all_language_data = $this->db->select('*')->where('language', $selected_lang_code)->get('language_strings')->result_array();
        return $all_language_data;
    }

    public function select_language_data_byid($idlang)
    {
        $all_language_data = $this->db->select('*')->where('idlang', $idlang)->get('language_strings')->result_array();
        return $all_language_data;
    }

    /**
     * select all languages
     */
    public function select_all_languages()
    {
        $all_language = $this->db->select('*')->get('languages')->result_array();
        return $all_language;
    }

    /**
     * Insert new Language
     */
    public function insert_language($code, $lang_name, $po_file, $other_param)
    {
        $db_data = array();
        $db_data['code'] = $code;
        $db_data['lang'] = $lang_name;
        $db_data['filename'] = $po_file;
        $db_data['is_default'] = 0;
        $this->db->insert('languages', $db_data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }
    /**
     * insert translated string in database and po file
     */
    public function translated_string($str_arr, $lang, $filename, $lang_id)
    {
        $data = array();
        $ids = array();
        foreach ($str_arr as $key => $value) {
            $ids[] = $key;
            $data[] = array(
                    'id' => $key,
                    'msgstr' => $value,
                    'idlang' => $lang_id
                );
        }
        $this->db->update_batch('language_strings', $data, 'id');
        if ($this->db->affected_rows() > 0) {
            $u_d = $this->select_language_data_byid($lang_id);
            $write_arr = array();
            foreach ($u_d as $value) {
                $write_arr[$value['msgid']] = $value['msgstr'];
            }
            $new_file        = $filename;
            unlink($filename);
            $fp = fopen($new_file, "w");
            fclose($fp);
            $file = fopen($new_file, "r+") or exit("Unable to open file!");
            $data =   "msgid \"\"\nmsgstr \"\"\n"
                . "\"Project-Id-Version: bbweb\\n\"\n"
                . "\"POT-Creation-Date: ".date("y-m-d H:i:s")."\\n\"\n"
                . "\"PO-Revision-Date: ".date("y-m-d H:i:s")."\\n\"\n"
                . "\"Last-Translator: \\n\"\n"
                . "\"Language-Team: \\n\"\n"
                . "\"Language: ".$lang."\\n\"\n"
                . "\"Content-Type: text/plain; charset=UTF-8\\n\"\n"
                . "\"MIME-Version: 1.0\\n\"\n"
                . "\"Content-Transfer-Encoding: 8bit\\n\"\n";
            fwrite($file, $data."\n");
            foreach ($write_arr as $key => $value) {
                fwrite($file, 'msgid "' . $key . '"' . "\n" . 'msgstr "'.$value.'"' . "\n\n");
            }
            fclose($file);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update Language
     */
    public function update_language($new_code, $new_name, $filepath, $id)
    {
        $this->db->set('code', '"'.$new_code.'"', false);
        $this->db->set('lang', '"'.$new_name.'"', false);
        $this->db->set('filename', '"'.$filepath.'"', false);
        $this->db->where('idlang', $id);
        $this->db->update('languages');
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update Language Path
     */
    public function update_language_path($lang_id, $new_path)
    {
        $this->db->set('filename', '"'.$new_path.'"', false);
        $this->db->where('idlang', $lang_id);
        $this->db->update('languages');
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * language_delete is delete language from languages table
     * @param  $code
     * @return Boolean
     */
    public function language_delete($lang_id)
    {
        $this->db->delete('language_strings', array('idlang' => $lang_id));
        $this->db->delete('languages', array('idlang' => $lang_id));
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get language code data
     */
    public function data_by_lang_code($code, $lang_name = '')
    {
        $lang_data = $this->db->select("*")->where('code', $code)->or_where('lang', $lang_name)->get('languages')->row();
        return $lang_data;
    }

     /**
     * Get language id data
     */
    public function data_by_lang_id($id, $lang_name = '')
    {
        $lang_data = $this->db->select("*")->where('idlang', $id)->or_where('lang', $lang_name)->get('languages')->row();
        return $lang_data;
    }

    /**
     * check_lang_name exists or not exists
     * @return boolean
     */
    public function check_lang_name_exists($lang_name)
    {
        $lang_data = $this->db->select("*")->where('lang', $lang_name)->get('languages')->row();
        if ($lang_data) {
            return true;
        }
            return false;
    }
}
