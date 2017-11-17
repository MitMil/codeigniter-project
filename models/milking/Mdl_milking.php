<?php

class Mdl_milking extends CI_Model
{
    private $tbl_milking_sessions = 'milking_sessions';

    public function get_sessions()
    {
        $this->db->select('*');
        $this->db->from($this->tbl_milking_sessions);
        $query = $this->db->get();
        $data = $query->result_array();
        return $data;
    }

    public function get_last_milking_data($idanimal, $num = 20)
    {
        $sql = "SELECT *, DATE_FORMAT(data_enter, '%Y%m%d') AS dataf 
                    FROM milking_data 
                    WHERE idanimal= ? 
                    ORDER BY data_enter DESC LIMIT 0, ?";
        $sth = $this->db->query($sql, array($idanimal, $num));
        return $sth->result_object();
    }

}
