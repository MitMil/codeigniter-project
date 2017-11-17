<?php
class Mdl_events extends CI_Model
{

    public function get_lactation()
    {
        echo "dgdg";
        die;
        $this->db->select('*');
        $this->db->from('animals_events');
        $this->db->join("animals_events.idevent=events.idevent");
        $query=$this->db->get();
        $data=$query->result_array();
        return $data;
    }
}
