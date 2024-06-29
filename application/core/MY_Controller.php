<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->the_session_expired();
	}

	private function the_session_expired()
	{
        if(empty($this->session->id_personnel))
		{
			$this->session->sess_destroy();
			header("Location:".site_url());
			exit();
		}
		else
			return true;
	}

}

