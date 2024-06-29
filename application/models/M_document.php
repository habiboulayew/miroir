<?php
class M_document extends MY_Model
{
    public $id_document;
    public $titre;
    public $description;
    public $file_name;
    public $date_creation;
    public $id_personnel;
    public $etat;
    public $password;
    public $filigrane;
    public $filigrane_indentification;
    public $filigrane_texte;
    public $filigrane_confidentiel;

    public function get_db_table()
    {
        return 'document';
    }

    public function get_db_table_pk()
    {
        return 'id_document';
    }
}
