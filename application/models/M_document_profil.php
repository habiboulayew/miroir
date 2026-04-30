<?php

// ====================================================================
//  MODÈLE M_document_profil
//  Gère la table de liaison entre les documents et les profils
//  utilisateurs. Permet de définir quels documents sont accessibles
//  par quel profil.
// ====================================================================
class M_document_profil extends MY_Model
{
    // ================================================================
    //  PROPRIÉTÉS — correspondent aux colonnes de la table
    //  document_profil en base de données
    // ================================================================
    public $id_document_profil; // Clé primaire de la liaison
    public $id_document;        // Référence vers la table document
    public $id_profil;          // Référence vers la table profil

    // ================================================================
    //  get_db_table()
    //  Retourne le nom de la table gérée par ce modèle.
    //  Utilisé par MY_Model pour construire les requêtes génériques
    //  (get_record, save, delete, etc.)
    // ================================================================
    public function get_db_table()
    {
        return 'document_profil';
    }

    // ================================================================
    //  get_db_table_pk()
    //  Retourne le nom de la clé primaire de la table.
    //  Utilisé par MY_Model pour identifier un enregistrement
    //  lors des opérations de lecture ou suppression unitaire.
    // ================================================================
    public function get_db_table_pk()
    {
        return 'id_document_profil';
    }

    // ================================================================
    //  delete_all($id_document)
    //  Supprime TOUTES les liaisons document↔profil pour un document
    //  donné. Typiquement appelé avant de recréer les associations
    //  (pattern : supprimer tout puis réinsérer).
    //
    //  @param string $id_document  L'identifiant du document concerné
    // ================================================================
    public function delete_all($id_document)
    {
        $sql = "DELETE FROM document_profil WHERE id_document='$id_document'";
        $this->db->query($sql);
    }

    // ================================================================
    //  get_profil_by_document($id_document)
    //  Retourne tous les profils ayant accès à un document donné.
    //  Utilisé côté administration pour afficher ou modifier
    //  les droits d'accès d'un document.
    //
    //  @param  string $id_document  L'identifiant du document
    //  @return array  Tableau d'objets représentant les liaisons
    // ================================================================
    public function get_profil_by_document($id_document)
    {
        $sql = "SELECT * FROM document_profil WHERE id_document='$id_document'";
        $query = $this->db->query($sql);
        return $query->result();
    }

    // ================================================================
    //  get_document_profil($id_profil)
    //  Retourne tous les documents accessibles par un profil donné,
    //  en joignant la table document pour récupérer les informations
    //  complètes (titre, date, protection).
    //
    //  Conditions appliquées :
    //    - d.etat = '1'  → uniquement les documents actifs/publiés
    //    - Triés par id_document DESC → les plus récents en premier
    //
    //  Utilisé dans Document::index() pour construire le menu latéral
    //  de navigation affiché à l'utilisateur connecté.
    //
    //  @param  string $id_profil  L'identifiant du profil connecté
    //  @return array  Tableau d'objets avec les champs :
    //                 dp.*           → colonnes de document_profil
    //                 d.titre        → titre du document
    //                 d.date_creation → date de publication
    //                 d.password     → type de protection (0, 1 ou 2)
    // ================================================================
    public function get_document_profil($id_profil)
    {
        $sql = "SELECT dp.*, d.titre, d.date_creation, d.password
                FROM document_profil dp
                INNER JOIN document d
                    ON d.id_document = dp.id_document
                    AND d.etat = '1'        -- exclut les documents désactivés
                WHERE id_profil = '$id_profil'
                ORDER BY d.id_document DESC -- plus récents en premier
               ";
        $query = $this->db->query($sql);
        return $query->result();
    }
}