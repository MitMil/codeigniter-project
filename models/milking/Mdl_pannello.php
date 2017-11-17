<?php

class Mdl_pannello extends CI_Model
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

    public function get_sessionsbypannello($idPannello)
    {
        $this->db->select('*');
        $this->db->where('idpannello', $idPannello);
        $this->db->from($this->tbl_milking_sessions);
        $query = $this->db->get();
        $data = $query->row_array();
        return $data;
    }

    private $idPannello;

    private $animalID;
    private $number_animal;
    private $velos_production_status;
    private $velos_reproduction_status;
    private $life_number;
    private $lactation;
    private $lactation_days;

    /**
     * Set animalID and its information.
     * @param $animalID
     * @return true|false
     */
    public function initbyPannelloAnimal($idPannello, $animalID)
    {
        // initiate with idPannello
        $this->idPannello = $idPannello;

        $this->db->select('*');
        $this->db->where('idanimal', $animalID);
        $this->db->from('animals');
        $query = $this->db->get();
        $animal = $query->row_array();

        if ($animal) {
            //var_dump($animal);
            $this->number_animal = $animal['number_animal'];
            $this->velos_production_status = Mdl_status::getProductionStatus($animal['velos_production_status']);
            $this->velos_reproduction_status = Mdl_status::getReproductionStatus($animal['velos_reproduction_status']);
            $this->life_number = $animal['life_number'];
            $this->lactation = $animal['lactation'];
            $this->lactation_days = $animal['lactation_days'];
            return true;
        }
        return false;
    }

    /**
     * Get produzione formatted for Pannello
     * @return mixed
     */
    public function getProduzione($produzione)
    {
        return str_replace('.', ',', number_format($produzione/1000, 2)) . 'kg';
    }

    /**
     * Get produzione formatted for Pannello
     * @return mixed
     */
    public function getMilkingTime($sec)
    {
        return gmtime_format($sec, 'i:s');
    }

    /**
     * Get animalID for Pannello
     * @return mixed
     */
    public function getAnimalID()
    {
        return $this->animalID;
    }

    /**
     * Get animal number
     * @return mixed
     */
    public function getAnimalNumber()
    {
        return $this->number_animal;
    }

    /**
     * animal lactation
     * @return mixed
     */
    public function getLactation()
    {
        return $this->lactation;
    }

    /**
     * animal Days in Milking
     * @return mixed
     */
    public function getDIM()
    {
        return $this->lactation_days;
    }

    /**
     * Get Animal Production Status
     * @return mixed
     */
    public function getProductionStatus()
    {
        return $this->velos_production_status;
    }

    /**
     * Get animal Reproduction Status.
     * @return mixed
     */
    public function getReproductionStatus()
    {
        return $this->velos_reproduction_status;
    }

}
