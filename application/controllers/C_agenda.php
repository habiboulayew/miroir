<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_agenda extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_agenda', 'agenda');
        $this->load->model('M_type_activite', 'type_activite');


    }

    public function index()
    {
        $all_data = $this->agenda->get_data_active();
        $data['all_data'] = $all_data;

        $data['id'] = 'agenda';
        //$this->load->view('V_agenda', $data);
        $this->load->view('V_test_rachit', $data);
    }

    public function saisie()
    {
        $all_data = $this->agenda->get_data();
        $data['all_data'] = $all_data;

        $all_data_type_activite = $this->type_activite->get_data();
        $data['select_type_activite'] = create_select_list($all_data_type_activite, 'id_type_activite','libelle_type_activite');

        $data['id'] = 'agenda_saisie';
        $this->load->view('V_agenda_saisie', $data);
    }
    
    public function get_record()
    {
        $args = func_get_args();
        $this->agenda->id_agenda = $args[0];
        $this->agenda->get_record();
        $agenda = (array)$this->agenda;
        $agenda['date_debut'] = str_replace(" ","T",$agenda['date_debut']);
        $agenda['date_fin'] = str_replace(" ","T",$agenda['date_fin']);
        show_json($agenda);
    }

    public function delete()
    {
        $args = func_get_args();
        $this->agenda->id_agenda = $args[0];
        show_json($this->agenda->delete());
    }

    public function save()
    {
		$post_id_agenda = $this->input->post('id_agenda');
		if($post_id_agenda != '')
		{
			$this->agenda->id_agenda = $this->input->post('id_agenda');
			$this->agenda->get_record();
		}
		
        $this->agenda->objet = $this->input->post('objet');
        $this->agenda->description = empty2null($this->input->post('description'));
        $this->agenda->responsable = empty2null($this->input->post('responsable'));
        $this->agenda->lieu = empty2null($this->input->post('lieu'));
        $this->agenda->date_debut = date_heure_parse_fr2en($this->input->post('date_debut'));
        $this->agenda->date_fin = date_heure_parse_fr2en($this->input->post('date_fin'));
        $this->agenda->etat = $this->input->post('etat');
        $this->agenda->id_type_activite = $this->input->post('id_type_activite');

        show_json($this->agenda->save());
    }
    
}
