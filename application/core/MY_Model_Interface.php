<?php

/**
 * Interface pour standardiser les modèles de Miroir
 * Tous les modèles qui l'implémentent DOIVENT définir ces deux méthodes
 */
interface MY_Model_Interface{
    
    /**
     * Retourne le nom de la table de la base de données
     * 
     * Exemple : 'personnel', 'document', 'document_profil', etc.
     * @return string Nom de la table
     */
    public function get_db_table();
    
    /**
     * Retourne le nom de la clé primaire (primary key) de la table
     * 
     * Exemple : 'id_personnel', 'id_document', 'id_document_profil', etc.
     * @return string Nom de la clé primaire
     */
    public function get_db_table_pk();
}