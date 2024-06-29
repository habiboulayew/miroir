<?php
class M_type_activite extends MY_Model
{
    public $id_type_activite;
    public $libelle_type_activite;

    public function get_db_table()
    {
        return 'type_activite';
    }

    public function get_db_table_pk()
    {
        return 'id_type_activite';
    }
}