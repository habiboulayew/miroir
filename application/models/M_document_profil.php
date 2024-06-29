<?php

class M_document_profil extends MY_Model
{
    public $id_document_profil;
    public $id_document;
    public $id_profil;

    public function get_db_table()
    {
        return 'document_profil';
    }

    public function get_db_table_pk()
    {
        return 'id_document_profil';
    }

    public function delete_all($id_document)
    {
        $sql = "DELETE FROM document_profil WHERE id_document='$id_document'";
        $this->db->query($sql);
    }

    public function get_profil_by_document($id_document)
    {
        $sql = "SELECT * FROM document_profil WHERE id_document='$id_document'";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function get_document_profil($id_profil)
    {
        $sql = "SELECT dp.*, d.titre, d.date_creation, d.password  FROM document_profil dp
                INNER JOIN document d ON d.id_document = dp.id_document AND d.etat='1'
                WHERE id_profil='$id_profil' ORDER BY d.id_document DESC";
        $query = $this->db->query($sql);
        return $query->result();
    }
}
