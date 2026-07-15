<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_connexion extends CI_Controller
{
	public function __construct()
    {
		parent::__construct();
        $this->load->model('M_personnel', 'personnel');
        $this->load->model('M_document_profil', 'document_profil');
    }

    public function con_sso()
    {
        $provider = new TheNetworg\OAuth2\Client\Provider\Azure(array(
                        'clientId' => '9a970dcd-9f88-4fb3-9aa7-11addb0186db',
                        'clientSecret' => 'FbH.i3-QCarFzyol0yx6a.j7-5rpNn_DmF',
                        'redirectUri' => 'https://mirroir.education.sn'
                    ));

        if (!isset($_GET['code'])) {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: ' . $authUrl);
            exit;
        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            header('Location: ' . base_url());
            exit('Invalid state');
        } else {

            // Try to get an access token (using the authorization code grant)

            try {
				$token = $provider->getAccessToken('authorization_code', array(
                            'code' => $_GET['code'],
                            'resource' => 'https://graph.windows.net',
                ));
			}
			catch (Exception $e) {
				header('Location: '.base_url());
				exit('Failed to get access token: '.$e->getMessage());
			}

            // Optional: Now you have a token you can look up a users profile data
            try {


                // We got an access token, let's now get the user's details
                //----new code---//
                $resourceOwner = $provider->getResourceOwner($token);
                $email = $resourceOwner->getUpn();
                //$row = file_get_contents_custom('https://apps.education.sn/get_IEN?email_pro='.$email);
				$row = $this->personnel->get_IEN($email);
                if (empty($row))
                   $ien = NULL;
                else
                   $ien = $row->ien;

				if(empty($ien))
                    header('Location:https://education.sn');
                //----new code---//

                $user = $this->personnel->get_user_ien($ien, $email);
                if(empty($user)){
                    header("Location:https://apps.education.sn");
                    exit();
                }
                else{
                    $this->session->set_userdata('id_personnel', $user->id_personnel);
                    $this->session->set_userdata('ien', $user->ien);
                    $this->session->set_userdata('sso', true);
                    $this->session->set_userdata('matricule', $user->matricule);
                    $this->session->set_userdata('prenom', $user->prenom);
                    $this->session->set_userdata('nom', $user->nom);
                    $this->session->set_userdata('email', $user->email);
                    $this->session->set_userdata('email_pro', $user->email_pro);
                    $this->session->set_userdata('fonction', $user->fonction);
                    $this->session->set_userdata('id_profil', $user->id_profil);
				    $this->session->set_userdata('email_connexion', $email);
                    header("Location:".site_url("acceuil"));
                    exit();
                }

            } catch (Exception $e) {
                // Failed to get user details
                header('Location: '.base_url());
                exit('Oh dear...');
            }
        }
    }

    public function con_login()
    {

        if(empty($_POST)){
            if(!empty($this->session->id_personnel))
                header("Location:".site_url("acceuil"));
            else
                $this->load->view('V_login');
        }
        else{
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $this->personnel->attempt_login($email, $password);

    if ($result['status'] !== 'success') {
        $data['email'] = $email;
        $data['message'] = $result['message'];
        $this->load->view('V_login', $data);
    }
    else {
        $user = $result['user'];
        $this->session->set_userdata('id_personnel', $user->id_personnel);
        $this->session->set_userdata('ien', $user->ien);
        $this->session->set_userdata('sso', false);
        $this->session->set_userdata('matricule', $user->matricule);
        $this->session->set_userdata('prenom', $user->prenom);
        $this->session->set_userdata('nom', $user->nom);
        $this->session->set_userdata('email', $user->email);
        $this->session->set_userdata('email_connexion', $email);
        $this->session->set_userdata('email_pro', $user->email_pro);
        $this->session->set_userdata('fonction', $user->fonction);
        $this->session->set_userdata('id_profil', $user->id_profil);

        session_write_close();
        header("Location:".site_url("acceuil"));
        exit();
    }
}
    }

    public function con_login1()
    {

        if(empty($_POST)){
            if(!empty($this->session->id_personnel))
                header("Location:".site_url("acceuil"));
            else
                $this->load->view('V_login');
        }
       else{
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. On interroge TOUJOURS la base en premier, jamais la session seule
    $user = $this->personnel->get_by_email($email);

    if (empty($user)) {
        $data['email'] = $email;
        $data['message'] = "Nom d'utilsateur ou mot de passe incorrect.";
        $this->load->view('V_login', $data);
        return;
    }

    // 2. La base est la seule source de vérité pour le verrouillage
    if ($user->etat == 0) {
        $data['email'] = $email;
        $data['message'] = "Compte verrouillé ou inactif. Veuillez contacter l'administrateur.";
        $this->load->view('V_login', $data);
        return;
    }

    // 3. Vérification du mot de passe (compte actif, etat=1)
    // ⚠️ TODO : password_verify() une fois les mots de passe hashés
    if ($password === $user->password) {
        // Succès : on efface le compteur d'échecs de cet email
        $this->session->unset_userdata('login_attempts_' . md5($email));

        $this->session->set_userdata('id_personnel', $user->id_personnel);
        $this->session->set_userdata('ien', $user->ien);
        $this->session->set_userdata('sso', false);
        $this->session->set_userdata('matricule', $user->matricule);
        $this->session->set_userdata('prenom', $user->prenom);
        $this->session->set_userdata('nom', $user->nom);
        $this->session->set_userdata('email', $user->email);
        $this->session->set_userdata('email_connexion', $email);
        $this->session->set_userdata('email_pro', $user->email_pro);
        $this->session->set_userdata('fonction', $user->fonction);
        $this->session->set_userdata('id_profil', $user->id_profil);

        session_write_close();
        header("Location:".site_url("acceuil"));
        exit();
    }
    else {
        // Mauvais mot de passe : on incrémente le compteur (juste pour décider QUAND verrouiller)
        $tentatives = $this->session->userdata('login_attempts_' . md5($email));
        $tentatives = $tentatives ? $tentatives + 1 : 1;
        $this->session->set_userdata('login_attempts_' . md5($email), $tentatives);

        if ($tentatives >= 4) {
            $this->personnel->verrouiller_compte($user->id_personnel);
            $data['message'] = "Compte verrouillé après 4 tentatives échouées. Veuillez contacter l'administrateur.";
        } else {
            $restantes = 4 - $tentatives;
            $data['message'] = "Nom d'utilsateur ou mot de passe incorrect. Il vous reste $restantes tentative(s) avant verrouillage.";
        }
        $data['email'] = $email;
        $this->load->view('V_login', $data);
    }
}
    }

    public function con_edu()
    {
        if(empty($_POST)){
            if(!empty($this->session->id_personnel))
                header("Location:".site_url("acceuil"));
            else
                $this->load->view('V_login');
        }
        else{
            $email = $_POST['email'];
            $password = $_POST['password'];
            $user = $this->personnel->get_user_login_edu($email, $password);
            if(empty($user)){
                $data['email'] = $email;
                $data['message'] = "Nom d'utilsateur ou mot de passe incorrect. Veuillez contacter l'administateur.";
                $this->load->view('V_login',$data);
            }
            else{
                $this->session->set_userdata('id_personnel', $user->id_personnel);
				$this->session->set_userdata('ien', $user->ien);
				$this->session->set_userdata('sso', false);
				$this->session->set_userdata('matricule', $user->matricule);
				$this->session->set_userdata('prenom', $user->prenom);
				$this->session->set_userdata('nom', $user->nom);
				$this->session->set_userdata('email', $user->email);
				$this->session->set_userdata('email_connexion', $email);
				$this->session->set_userdata('email_pro', $user->email_pro);
				$this->session->set_userdata('fonction', $user->fonction);
				$this->session->set_userdata('id_profil', $user->id_profil);
                header("Location:".site_url("acceuil"));
            }
        }
    }

    public function logout()
    {
        if($this->session->sso == true){
            $this->session->sess_destroy();
             //header("Location:https://apps.education.sn");
            $provider = new TheNetworg\OAuth2\Client\Provider\Azure(array(
                'clientId' => '9a970dcd-9f88-4fb3-9aa7-11addb0186db',
                'clientSecret' => 'FbH.i3-QCarFzyol0yx6a.j7-5rpNn_DmF',
                'redirectUri' => 'https://mirroir.education.sn'
            ));
            $logoutUrl = $provider->getLogoutUrl('https://www.education.sn');
            header('Location: '.$logoutUrl);
        }
        else if($this->session->sso == false){
            $this->session->sess_destroy();
            header("Location:".site_url('C_connexion/con_login'));
        }
    }

}
