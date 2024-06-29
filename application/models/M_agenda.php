<?php

class M_agenda extends MY_Model
{
    public $id_agenda;
    public $objet;
    public $description;
    public $responsable;
    public $lieu;
    public $date_debut;
    public $date_fin;
    public $etat;
    public $id_type_activite;

    public function get_data()
    {
        $sql = "SELECT a.*, t.libelle_type_activite FROM agenda a
                INNER JOIN type_activite t ON t.id_type_activite = a.id_type_activite";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function get_data_active()
    {
        $sql = "SELECT a.*, t.libelle_type_activite FROM agenda a
                INNER JOIN type_activite t ON t.id_type_activite = a.id_type_activite WHERE a.etat='1'";
        $query = $this->db->query($sql);
        return $query->result();
    }


    public function get_db_table()
    {
        return 'agenda';
    }

    public function get_db_table_pk()
    {
        return 'id_agenda';
    }
}
