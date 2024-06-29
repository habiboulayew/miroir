<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_personnel extends MY_Controller
{
	public function __construct()
    {
		parent::__construct();
        $this->load->model('M_personnel', 'personnel');
        $this->load->model('M_profil', 'profil');
    }

    public function index()
    {
        $all_data = $this->personnel->get_data();
        $data['all_data'] = $all_data;

        $all_data_profil = $this->profil->get_data();
        $data['select_profil'] = create_select_list($all_data_profil, 'id_profil','libelle_profil');

        $data['id'] = 'personnel';
        $this->load->view('V_personnel', $data);
    }

    public function get_record()
    {
        $args = func_get_args();
        $this->personnel->id_personnel = $args[0];
        $this->personnel->get_record();
        show_json($this->personnel);
    }

    public function delete()
    {
        $args = func_get_args();
        $this->personnel->id_personnel = $args[0];
        show_json($this->personnel->delete());
    }

    public function save()
    {
		$post_id_personnel = $this->input->post('id_personnel');
		if($post_id_personnel != '')
		{
			$this->personnel->id_personnel = $this->input->post('id_personnel');
			$this->personnel->get_record();
		}


        check_unique_field('personnel', 'ien', $this->input->post('ien'),'id_personnel',$post_id_personnel);
        check_unique_field('personnel', 'matricule', $this->input->post('matricule'),'id_personnel',$post_id_personnel);
        check_unique_field('personnel', 'email', $this->input->post('email'),'id_personnel',$post_id_personnel);
        check_unique_field('personnel', 'email_pro', $this->input->post('email_pro'),'id_personnel',$post_id_personnel);


        $this->personnel->ien = $this->input->post('ien');
        $this->personnel->matricule = $this->input->post('matricule');
        $this->personnel->prenom = $this->input->post('prenom');
        $this->personnel->nom = $this->input->post('nom');
        $this->personnel->telephone = $this->input->post('telephone');
        $this->personnel->email = $this->input->post('email');
        $this->personnel->email_pro = $this->input->post('email_pro');
        $this->personnel->code_str = $this->input->post('code_str');
        $this->personnel->fonction = $this->input->post('fonction');
        $this->personnel->id_profil = $this->input->post('id_profil');
        $this->personnel->password = $this->input->post('password');
        $this->personnel->etat = $this->input->post('etat');

        show_json($this->personnel->save());
    }

    public function getSearchIEN()
    {
        $ien = $this->input->post('value');
        $data = apiGetData("apps","C_personnel_api/getIEN_info_all?ien=$ien");
        show_json($data);
    }

    public function password()
    {
        $data['id'] = 'password';
        $this->load->view('V_password', $data);
    }

    public function save_password()
    {
        $id_personnel = $this->session->id_personnel;
        $oldPassword = $this->input->post('oldPassword');
        $newPassword = $this->input->post('newPassword');
        $result = $this->personnel->save_password($id_personnel,$oldPassword,$newPassword);
        show_json($result);
    }
}
