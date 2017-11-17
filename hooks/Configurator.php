<?php
/**
 * Define BB Settings from Database
 */
class Configurator
{
    private $bb_table   = 'bb_settings'; // Database setting table
    private $env        = array('BBWEB', 'IMILK', 'VELOS', 'SYSTEM'); // Get environments
    private $db;

    public function load()
    {

        require_once(BASEPATH.'database/DB.php');
        $this->db = DB(); // getting hold of a DAO instance

        self::initSettings();
        self::defineEvents();
    }

    /**
     * Get settings from Database
     */
    public function initSettings()
    {
        // Get BBWEB environment values with code
        $this->db->where_in('env', $this->env);
        $query = $this->db->get($this->bb_table);
        $bb_settings = $query->result_array();

        if ($bb_settings) {
            foreach ($bb_settings as $setting) {

                $var_code = trim($setting['var_code']);

                // set default value
                $value = trim($setting['default_value']);

                // check custom value
                if (trim($setting['value']) != '') {
                    $value = trim($setting['value']);
                }

                // check type for unwanted values
                switch ($setting['type_values']) {
                    case 'URL':
                    case 'PATH':
                        $value = rtrim($value, '/').'/';
                        break;

                    case 'STRING':
                        // Check if KPI Graph Thresholds
                        $data_default = @unserialize($setting['default_value']);
                        if ($data_default !== false) {
                            $data = ($setting['value']) ? unserialize($setting['value']) : '';
                            $i = 0;
                            foreach ($data_default as $value_default) {
                                if (isset($data[$i]) && $data[$i]) {
                                    if (is_array($data[$i]) && isset($data[$i]['V']) && $data[$i]['V']) {
                                        $data_default[$i]['V'] = $data[$i]['V'];
                                    }
                                }
                                $i++;
                            }
                            $value = serialize($data_default);
                        }
                        break;
                }

                // define variable code
                define($var_code, $value);
            }
        }
    }

    /**
     * Get events from database & define `velos_class_name` e.g. BirthEvent = -1
     */
    public function defineEvents()
    {
        $this->db->select('*');
        $this->db->from('events');
        $query = $this->db->get();
        $events = $query->result_array();
        if ($events) {
            foreach ($events as $event) {
                if (!defined($event['velos_class_name'])) {
                    define($event['velos_class_name'], $event['idevent']);
                }
            }
        }
    }
}
