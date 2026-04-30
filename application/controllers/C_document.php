<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_document extends MY_Controller
{
	public function __construct()
    {
		parent::__construct();
        $this->load->model('M_document', 'document');
        $this->load->model('M_document_profil', 'document_profil');
        $this->load->model('M_profil', 'profil');
    }

    public function index()
    {
        $all_data = $this->document->get_data();
        $data['all_data'] = $all_data;

        $data['all_data_profil'] = $this->profil->get_data();

        $data['id'] = 'document';
        $this->load->view('V_document', $data);
    }

    public function get_record()
    {
        $args = func_get_args();
        $this->document->id_document = $args[0];
        $this->document->get_record();
        $document = (array)$this->document;

        $data = $this->document_profil->get_profil_by_document($args[0]);
        foreach ($data as $row){
            $document['profil'.$row->id_profil] = 1;
        }
        show_json($document);
    }

    public function delete()
    {
        $args = func_get_args();
        $this->document->id_document = $args[0];
        $this->document_profil->delete_all($args[0]);
        show_json($this->document->delete());
    }

    public function save()
    {
		$post_id_document = $this->input->post('id_document');
		if($post_id_document != '')
		{
			$this->document->id_document = $this->input->post('id_document');
			$this->document->get_record();
		}
		else{
		    $this->document->file_name = 'temp';
        }

		
        $this->document->titre = $this->input->post('titre');
        $this->document->description = $this->input->post('description');
        $this->document->id_personnel = $this->session->id_personnel;
        $this->document->etat = $this->input->post('etat');
        $this->document->password = $this->input->post('password');
        $this->document->filigrane = empty($_POST['filigrane']) ? '0' : '1';
        $this->document->filigrane_indentification = empty($_POST['filigrane_indentification']) ? '0' : '1';
        $this->document->filigrane_confidentiel = empty($_POST['filigrane_confidentiel']) ? '0' : '1';
        $this->document->filigrane_texte = empty2null($this->input->post('filigrane_texte'));

        $result = $this->document->save();

        if($post_id_document == '' && $result['status'] == 'success'){
            $file_name = $result['id'].'.pdf';
            //conf_upload
            $conf_upload['upload_path'] = './DATA/';
            $conf_upload['allowed_types'] = 'pdf|PDF';
            $conf_upload['overwrite'] = true;
            $conf_upload['file_name'] = $file_name;
            $this->load->library('upload', $conf_upload);
            if (!$this->upload->do_upload('file_name')) {
                $result['status'] = 'error';
                $result['message'] = $this->upload->display_errors();
                $this->document->delete();
            }
            else{
                $this->document->file_name = $result['id'].'.pdf';
                $this->document->save();
            }
        }

        if($result['status'] == 'success'){
            $this->document_profil->delete_all($result['id']);
            if (!empty($_POST['lst_id_profil'])) {
                foreach ($_POST['lst_id_profil'] as $id_profil) {
                    $this->document_profil->id_document_profil = null;
                    $this->document_profil->id_document = $result['id'];
                    $this->document_profil->id_profil = $id_profil;
                    $this->document_profil->save();
                }
            }
        }

        show_json($result);
    }

}
