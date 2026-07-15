<?php
class M_personnel extends MY_Model
{
    public $id_personnel;
    public $ien;
    public $matricule;
    public $prenom;
    public $nom;
    public $telephone;
    public $email;
    public $email_pro;
    public $code_str;
    public $fonction;
    public $id_profil;
    public $password;
    public $etat;
    public $tentatives_echouees;


    public function get_db_table()
    {
        return 'personnel';
    }

    public function get_db_table_pk()
    {
        return 'id_personnel';
    }

    public function get_user_ien($ien, $email_pro)
    {
        $query = $this->db->get_where('personnel', array('ien' => $ien,'email_pro' => $email_pro,'etat' => '1'));
        return $query->row();
    }


    public function get_IEN($email)
    {
        $sql = "SELECT ien FROM personnel WHERE email_pro='$email'";
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function get_user_login($email, $password)
    {
        $sql = "SELECT * FROM personnel WHERE email='$email' or email_pro='$email' AND password='$password' AND password!='' AND etat='1'";
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function get_user_login_edu($email, $password)
    {
        $sql = "SELECT * FROM personnel WHERE email_pro='$email' AND password='$password' AND password!='' AND etat='1'";
        $query = $this->db->query($sql);
        return $query->row();
    }

    /*public function get_user_login($email, $password)
    {
        $query = $this->db->get_where('personnel', array('email' => $email,'password' => $password,'password!=' => '','etat' => '1'));
        return $query->row();
    }*/

    public function save_password($id_personnel,$oldPassword,$newPassword)
    {
        $query = $this->db->get_where('personnel', array('id_personnel' => $id_personnel,'password' => $oldPassword));
        $exist = $query->row();
        if(empty($exist)){
            $result['status'] = "error";
            $result['message'] = "Ancien mot de passe incorrect !!!";
        }
        else {
            $sql = "UPDATE personnel SET  password='$newPassword' WHERE id_personnel='$id_personnel'";
            $this->db->query($sql);
            $result['status'] = "success";
            $result['message'] = "Votre mot de passe a été modifié avec succés.";
        }
        return $result;
    }


        /*
          Récupère un utilisateur par email, peu importe son etat.
         
        public function get_by_email($email)
        {
            $this->db->select('*');
            $this->db->from('personnel');
            $this->db->group_start();
                $this->db->where('email', $email);
                $this->db->or_where('email_pro', $email);
            $this->db->group_end();
            $this->db->where('password !=', '');
            $query = $this->db->get();
            return $query->row();
        }
*/


/**
 * Authentifie un utilisateur avec verrouillage automatique après 4 échecs,
 * compteur stocké en base (persistant, pas en session).
 */
public function attempt_login($email, $password)
{
    $this->db->select('*');
    $this->db->from('personnel');
    $this->db->group_start();
        $this->db->where('email', $email);
        $this->db->or_where('email_pro', $email);
    $this->db->group_end();
    $this->db->where('password !=', '');
    $query = $this->db->get();
    $user = $query->row();

    // Aucun compte trouvé
    if (empty($user)) {
        return ['status' => 'invalid', 'user' => null, 'message' => "Nom d'utilisateur ou mot de passe incorrect."];
    }

    // Compte inactif ou verrouillé (etat = 0)
    if ($user->etat == 0) {
        return ['status' => 'locked', 'user' => null, 'message' => "Compte inactif ou verrouillé. Veuillez contacter l'administrateur."];
    }

    // Vérification du mot de passe
    if ($password === $user->password) {
        // Succès : réinitialiser le compteur
        $this->db->where('id_personnel', $user->id_personnel);
        $this->db->update('personnel', ['tentatives_echouees' => 0]);
        return ['status' => 'success', 'user' => $user, 'message' => ''];
    }

    // Échec : incrémenter le compteur EN BASE
    $nouvelles_tentatives = $user->tentatives_echouees + 1;
    $update_data = ['tentatives_echouees' => $nouvelles_tentatives];

    if ($nouvelles_tentatives >= 4) {
        $update_data['etat'] = '0'; // verrouillage
    }

    $this->db->where('id_personnel', $user->id_personnel);
    $this->db->update('personnel', $update_data);

    if ($nouvelles_tentatives >= 4) {
        return ['status' => 'locked', 'user' => null, 'message' => "Compte verrouillé après 4 tentatives échouées. Veuillez contacter l'administrateur."];
    }

    $restantes = 4 - $nouvelles_tentatives;
    return ['status' => 'invalid', 'user' => null, 'message' => "Nom d'utilisateur ou mot de passe incorrect. Il vous reste $restantes tentative(s) avant verrouillage."];
}

        /**
         * Verrouille un compte (etat = 0).
         */
        public function verrouiller_compte($id_personnel)
        {
            $this->db->where('id_personnel', $id_personnel);
            $this->db->update('personnel', ['etat' => '0']);
        }


        
        /**
 * Génère un nouveau mot de passe aléatoire, le sauvegarde et l'envoie par SMS.
 */
public function reinitialiser_password($id_personnel)
{
    $this->db->where('id_personnel', $id_personnel);
    $user = $this->db->get('personnel')->row();

    if (empty($user)) {
        return ['status' => 'error', 'message' => "Utilisateur introuvable."];
    }

    if (empty($user->telephone)) {
        return ['status' => 'error', 'message' => "Aucun numéro de téléphone enregistré pour cet utilisateur."];
    }

    // 1. Authentification auprès de l'API pour obtenir un token JWT
    $token = $this->authentifierAPI();

    if (empty($token)) {
        return ['status' => 'error', 'message' => "Impossible de contacter le service SMS (authentification échouée). Le mot de passe n'a pas été modifié."];
    }

    // 2. Génère le nouveau mot de passe AVANT de le sauvegarder
    $nouveau_password = $this->generer_password_aleatoire(8);
    $message = "Bonjour {$user->prenom}, votre nouveau mot de passe Miroir est : $nouveau_password";

    // 3. Envoi du SMS D'ABORD -- on ne modifie la base que si le SMS part bien
    $sms_result = $this->envoyerSMS($user->telephone, $message, $token);

    if ($sms_result['statut'] != '1') {
        return ['status' => 'error', 'message' => "Échec de l'envoi du SMS (" . ($sms_result['erreur'] ?? 'erreur inconnue') . "). Le mot de passe n'a pas été modifié."];
    }

    // 4. SMS envoyé avec succès : on met à jour le mot de passe en base
    $update_data = ['password' => $nouveau_password];

    // réactive automatiquement le compte s'il était inactif/verrouillé ---
    $compte_etait_inactif = ($user->etat == 0);
    if ($compte_etait_inactif) {
        $update_data['etat'] = 1;
        $update_data['tentatives_echouees'] = 0; 
    }
    
    $this->db->where('id_personnel', $id_personnel);
    $this->db->update('personnel', $update_data);

    //message adapté selon si le compte a été réactivé ---
    $message_retour = "Mot de passe réinitialisé et envoyé par SMS avec succès.";
    if ($compte_etait_inactif) {
        $message_retour .= " Le compte, qui était inactif/verrouillé, a été réactivé automatiquement.";
    }

    return ['status' => 'success', 'message' => $message_retour];
}

/*
  Génère un mot de passe aléatoire.
 
private function generer_password_aleatoire($longueur = 8)
{
    $caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789'; // évite 0/O/1/l ambigus
    $password = '';
    for ($i = 0; $i < $longueur; $i++) {
        $password .= $caracteres[random_int(0, strlen($caracteres) - 1)];
    }
    return $password;
}

*/

private function generer_password_aleatoire($longueur = 8)
{
    // Caractères ambigus (0/O, 1/l/I) exclus de chaque catégorie
    $majuscules = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    $minuscules = 'abcdefghjkmnpqrstuvwxyz';
    $chiffres   = '23456789';
    $speciaux   = '!@#$%&*-_+=';

    if ($longueur < 4) {
        $longueur = 4; // minimum requis pour satisfaire les 4 catégories
    }

    // 1. On garantit une occurrence de chaque catégorie
    $password_chars = [
        $majuscules[random_int(0, strlen($majuscules) - 1)],
        $minuscules[random_int(0, strlen($minuscules) - 1)],
        $chiffres[random_int(0, strlen($chiffres) - 1)],
        $speciaux[random_int(0, strlen($speciaux) - 1)],
    ];

    // 2. On complète le reste de la longueur avec un mélange de toutes les catégories
    $tous_caracteres = $majuscules . $minuscules . $chiffres . $speciaux;
    for ($i = count($password_chars); $i < $longueur; $i++) {
        $password_chars[] = $tous_caracteres[random_int(0, strlen($tous_caracteres) - 1)];
    }

    // 3. On mélange l'ordre pour ne pas toujours avoir majuscule/minuscule/chiffre/spécial en premier
    for ($i = count($password_chars) - 1; $i > 0; $i--) {
        $j = random_int(0, $i);
        [$password_chars[$i], $password_chars[$j]] = [$password_chars[$j], $password_chars[$i]];
    }

    return implode('', $password_chars);
}

/**
 * Envoie un SMS via l'API education.sn (endpoint unitaire /sms/sendSms).
 */
private function envoyerSMS($telephone, $message, $token)
{
    try {
        $baseUrl = "https://api.education.sn/v1";
        log_message('info', "📤 Envoi SMS à $telephone...");

        $ch = curl_init($baseUrl . '/sms/sendSms');

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'telephone' => $telephone,
                'message'   => $message
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ]
        ]);

        $smsResponse = curl_exec($ch);
        $smsHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_errno($ch);
        curl_close($ch);

        if ($smsResponse && !$curlError && ($smsHttpCode == 200 || $smsHttpCode == 201)) {
            log_message('info', "✅ SMS envoyé avec succès à $telephone");
            return [
                'statut' => '1',
                'id_externe' => $telephone . '_' . time()
            ];
        }
        else {
            log_message('error', "❌ Échec envoi: HTTP $smsHttpCode");
            return [
                'statut' => '-1',
                'erreur' => "HTTP $smsHttpCode"
            ];
        }
    } catch (Exception $e) {
        log_message('error', "❌ Exception envoi SMS: " . $e->getMessage());
        return [
            'statut' => '-1',
            'erreur' => $e->getMessage()
        ];
    }
}

/**
 * S'authentifie auprès de l'API education.sn et récupère un token JWT.
 */
private function authentifierAPI()
{
    try {
        $baseUrl = "https://api.education.sn/v1";

        $login = getenv('SMS_API_LOGIN') ?: 'mensms';
        $password = getenv('SMS_API_PASSWORD') ?: 'ss@api*2026';

        log_message('info', "🔐 Authentification API SMS...");

        $ch = curl_init($baseUrl . '/login');

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'login'    => $login,
                'password' => $password
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json']
        ]);

        $authResponse = curl_exec($ch);
        $authHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$authResponse || $authHttpCode != 200) {
            log_message('error', "❌ Échec authentification: HTTP $authHttpCode");
            return null;
        }

        $authResult = json_decode($authResponse, true);

        if (!isset($authResult['token'])) {
            log_message('error', "❌ Token non reçu dans la réponse");
            return null;
        }

        log_message('info', "✅ Token obtenu avec succès");
        return $authResult['token'];

    } catch (Exception $e) {
        log_message('error', "❌ Exception authentification: " . $e->getMessage());
        return null;
    }
}

}


