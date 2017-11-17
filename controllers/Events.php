<?php
class Events extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mdl_role', '', true);
        $this->load->model('mdl_module', '', true);
        $this->load->model('mdl_kpi', '', true);
        $this->load->model('mdl_events', '', true);

    }

    public function index()
    {

        $lactation['data']=$this->mdl_events->get_lactation();
        p($lactation);
        die;
        //load_views('kpi/herd_composition', $lactation);
    }
}
