<?php

// Vérifie que ce fichier n'est pas accédé directement via l'URL
defined('BASEPATH') OR exit('No direct script access allowed');

// Classe de contrôleur personnalisée qui étend CI_Controller
// Tous les contrôleurs peuvent hériter de cette classe pour avoir les mêmes fonctionnalités
class MY_Controller extends CI_Controller {

	// Constructeur de la classe - appelé automatiquement à chaque instanciation
	public function __construct()
	{
		// Appelle le constructeur parent pour initialiser CI_Controller
		parent::__construct();
		
		// Vérifie que la session est toujours valide
		$this->the_session_expired();
	}

	/**
	 * Méthode privée : vérifie si l'utilisateur est encore connecté
	 * Une session est considérée comme valide si id_personnel est défini
	 */
	private function the_session_expired()
	{
        // Si l'ID du personnel n'existe pas en session (utilisateur pas connecté)
		if(empty($this->session->id_personnel))
		{
			// Détruit complètement la session
			$this->session->sess_destroy();
			
			// Redirige vers la page d'accueil
			header("Location:".site_url());
			
			// Arrête l'exécution du script
			exit();
		}
		else
			// Si la session est valide, retourne true
			return true;
	}

}