<?php

class Mdl_service extends CI_Model
{
    // empty all values of bb_settings table excluded BBWEB_IFC_REGISTRATION
    public function resetSettingsdata()
    {
        $data = array('value' => '');
        $this->db->where('var_code NOT LIKE', '%BBWEB_IFC_REGISTRATION%');
        $upd = $this->db->update('bb_settings', $data);
        if (!$upd) {
            // there was an error
            return false;
        } elseif (!$this->db->affected_rows()) {
            // no error, but nothing changed
            return true;
        } else {
            // record changed
            return true;
        }

        return false;
    }
    //empty SYSTEM ENVs values of bb_settings table included BBWEB_IFC_REGISTRATION
    public function resetSettingsReg()
    {
        $data = array('value' => '');
        $this->db->where('env', 'BBWEB');
        $upd = $this->db->update('bb_settings', $data);
        if (!$upd) {
            // there was an error
            return false;
        } elseif (!$this->db->affected_rows()) {
            // no error, but nothing changed
            return true;
        } else {
            // record changed
            return true;
        }

        return false;
    }

    //empty values of bb_settings table of SYSTEM_CLIENT_NAME
    public function resetSettingsSn()
    {
        $data = array('value' => '');
        $this->db->where('var_code', 'SYSTEM_CLIENT_NAME');
        $upd = $this->db->update('bb_settings', $data);
        if (!$upd) {
            // there was an error
            return false;
        } elseif (!$this->db->affected_rows()) {
            // no error, but nothing changed
            return true;
        } else {
            // record changed
            return true;
        }

        return false;
    }

    //empty other tables excluded 'bb_settings','users','roles','modules'
    public function resetOnlyData()
    {
        $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA LIKE '"._DB_Database."'";
        $excluded_table = array('bb_settings' => '', 'users' => '', 'roles' => '', 'modules' => '');
        $query = $this->db->query($sql);
        $tables = $query->result_array();
        foreach ($tables as $table) {
            if (array_key_exists($table['TABLE_NAME'], $excluded_table)) {
                continue;
            } else {
                $sql = 'SET FOREIGN_KEY_CHECKS = 0;';
                $sql .= ' TRUNCATE TABLE `'.$table['TABLE_NAME'].'` ;';
                $sql .= 'SET FOREIGN_KEY_CHECKS = 1;';
                $return = $this->db->query($sql);
            }
        }

        return true;
    }

    /**
     * GetDefaultIP is select default value of SYSTEM Environment
     */
    public function GetDefaultValue()
    {
        $sql = "SELECT `default_value`,`var_code` FROM bb_settings WHERE env LIKE 'SYSTEM'";
        $query = $this->db->query($sql);
        $data = $query->result_array();
        $values = array();
        if ($data) {
            foreach ($data as $key) {
                $values[$key['var_code']]=$key['default_value'];
            }
            return $values;
        }
        return false;
    }
}
