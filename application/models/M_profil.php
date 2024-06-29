<?php
class M_profil extends MY_Model
{
    public $id_profil;
    public $libelle_profil;

    public function get_db_table()
    {
        return 'profil';
    }

    public function get_db_table_pk()
    {
        return 'id_profil';
    }
}
