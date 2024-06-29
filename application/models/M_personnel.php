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
}
