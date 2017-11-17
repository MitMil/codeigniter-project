<?php
class Mdl_animal extends CI_Model
{
    /**
     * @var string Table name for animals.
     */
    private $tblAnimal = 'animals';

    private $animalID;

    private $number_animal;

    private $velos_production_status;

    private $velos_reproduction_status;

    private $life_number;

    private $lactation;

    private $DIM;

    private $groupname;

    private $count_insemination;

    private $last_insemination;

    private $last_dryoff;

    private $total_lactation;

    private $count_lactation;

    private $total_lactation_by_status;

    private $groupid;

    /**
     * Set animalID and its information.
     * @param $animalID
     * @return true|false
     */
    public function initbyAnimalID($animalID)
    {
        // initiate with $animalID
        $this->animalID = $animalID;

        // Get Animal Information
        $this->db->select('*');
        $this->db->where('idanimal', $animalID);
        $this->db->from($this->tblAnimal);
        $query = $this->db->get();
        $animal = $query->row_array();
        if ($animal) {
            $animal['DIM'] = $animal['lactation_days'];
            $this->initbyAnimalArray($animal);
            return $this;
        }

        return false;
    }

    /**
     * Get AnimalArray and its information.
     * @return mixed
     */
    public function initbyAnimalArray($animal_Array)
    {
        if ($animal_Array) {

            $this->animalID                  = getArrayVal('idanimal', $animal_Array);
            $this->number_animal             = getArrayVal('number_animal', $animal_Array);
            $this->velos_production_status   = getArrayVal('velos_production_status', $animal_Array);
            $this->velos_reproduction_status = getArrayVal('velos_reproduction_status', $animal_Array);
            $this->life_number               = getArrayVal('life_number', $animal_Array);
            $this->lactation                 = getArrayVal('lactation', $animal_Array);
            $this->DIM                       = getArrayVal('DIM', $animal_Array);
            $this->groupname                 = getArrayVal('name', $animal_Array);
            $this->count_insemination        = getArrayVal('count_insemination', $animal_Array);
            $this->last_insemination         = getArrayVal('last_insemination', $animal_Array);
            $this->last_dryoff               = getArrayVal('last_dryoff', $animal_Array);
            $this->total_lactation           = getArrayVal('total_lactation', $animal_Array);
            $this->count_lactation           = getArrayVal('count_lactation', $animal_Array);
            $this->total_lactation_by_status = getArrayVal('total_lactation_by_status', $animal_Array);
            $this->groupid                   = getArrayVal('idgroup', $animal_Array);
            return $this;
        }

        return false;
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
        return $this->DIM;
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

    /**
     * Get animal Group Name.
     * @return mixed
     */
    public function getGroupName()
    {
        return $this->groupname;
    }

    /**
     * Get total insemination.
     * @return mixed
     */
    public function getCountInsemination()
    {
        return $this->count_insemination;
    }

    /**
     * Get animal last insemination.
     * @return mixed
     */
    public function getLastInsemination()
    {
        return $this->last_insemination;
    }

    /**
     * Get animal last dryoff.
     * @return mixed
     */
    public function getLastDryOff()
    {
        return $this->last_dryoff;
    }

    /**
     * Get animal total lactation.
     * @return mixed
     */
    public function getTotalLactation()
    {
        return $this->total_lactation;
    }

    /**
     * Get animal count lactation.
     * @return mixed
     */
    public function getCountLactation()
    {
        return $this->count_lactation;
    }

    /**
     * Get animal total lactation by status.
     * @return mixed
     */
    public function getTotalLactationByStatus()
    {
        return $this->total_lactation_by_status;
    }

    /**
     * Get animal group id.
     * @return mixed
     */
    public function getIdGroup()
    {
        return $this->groupid;
    }
}
