<?php

/**
 * Classe M_document
 * 
 * Modèle pour gérer les documents dans Miroir
 * Hérite de MY_Model pour accéder aux méthodes CRUD (save, delete, get_record, etc.)
 * Implémente MY_Model_Interface (via MY_Model)
 */
class M_document extends MY_Model
{
    // ============================================
    // PROPRIÉTÉS DE L'ENREGISTREMENT
    // ============================================
    
    // Identifiant unique du document (clé primaire)
    public $id_document;
    
    // Titre du document
    public $titre;
    
    // Description ou résumé du document
    public $description;
    
    // Nom du fichier stocké sur le serveur
    public $file_name;
    
    // Date de création du document
    public $date_creation;
    
    // ID du personnel qui a créé/possède le document
    public $id_personnel;
    
    // État du document (exemple : 'brouillon', 'publie', 'archive', etc.)
    public $etat;
    
    // Mot de passe pour protéger le document
    public $password;
    
    // Indique si le document a un filigrane
    public $filigrane;
    
    // Filigrane contenant l'identification (nom, ID personnel, etc.)
    public $filigrane_indentification;
    
    // Texte personnalisé du filigrane
    public $filigrane_texte;
    
    // Filigrane indiquant que le document est confidentiel
    public $filigrane_confidentiel;

    // ============================================
    // MÉTHODES DE L'INTERFACE MY_Model_Interface
    // ============================================
    
    /**
     * Retourne le nom de la table de la base de données
     * 
     * @return string 'document'
     */
    public function get_db_table()
    {
        return 'document'; 
    }

    /**
     * Retourne le nom de la clé primaire de la table
     * 
     * @return string 'id_document'
     */
    public function get_db_table_pk()
    {
        return 'id_document';
    }
}