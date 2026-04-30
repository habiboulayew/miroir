<?php
// Importe l'interface que tous les modèles doivent respecter
require_once 'application/core/MY_Model_Interface.php';

/**
 * Classe abstraite MY_Model
 * 
 * Classe de base pour tous les modèles de Miroir
 * Fournit des méthodes CRUD (Create, Read, Update, Delete) réutilisables
 * Les classes enfants DOIVENT implémenter MY_Model_Interface
 */
abstract class MY_Model extends CI_Model implements MY_Model_Interface{

    // Constructeur de la classe
    function __construct()
    {
        // Appelle le constructeur parent pour initialiser CI_Model
        parent::__construct();
    }

    /**
     * PRIVATE - Insère un nouvel enregistrement dans la base de données
     * 
     * @param boolean $without_null Si true, exclut les propriétés null de l'insertion
     * @return int|array L'ID inséré en cas de succès, ou un tableau d'erreur en cas d'échec
     */
    private function insert($without_null)
    {
        try {
            // Si on veut exclure les valeurs null
            if($without_null == true){
                // Parcourt toutes les propriétés de l'objet
                foreach($this as $key=>$value){
                    // Si la valeur est null ET ce n'est pas la clé primaire
                    if($value ==  null && $key != $this->get_db_table_pk())
                        // Supprime cette propriété (elle ne sera pas insérée)
                        unset($this->$key);
                }
            }
            
            // Supprime la clé primaire (on la veut pas en insertion)
            // Elle sera générée automatiquement par la base de données
            unset($this->{$this->get_db_table_pk()});
            
            // Insère l'objet complet comme nouvel enregistrement
            $insert = $this->db->insert($this->get_db_table(), $this);
            
            // Vérifie s'il y a eu une erreur lors de l'insertion
            if (!$insert) {
                // Récupère les détails de l'erreur
                $db_error = $this->db->error();
                $d['status'] = "error";
                $d['message'] = "Database error! Error Code [" . $db_error['code'] . "] Error: " . $db_error['message'];
                return $d;
            }
            
            // Récupère l'ID généré automatiquement par la base
            // et l'assigne à la clé primaire de l'objet
            $this->{$this->get_db_table_pk()} = $this->db->insert_id();
            
            // Retourne l'ID du nouvel enregistrement
            return $this->{$this->get_db_table_pk()};
        }
        catch (Exception $e) {
            // Capture toute exception PHP
            $d['status'] = "error";
            $d['message'] = $e->getMessage();
            return $d;
        }
    }

    /**
     * PRIVATE - Met à jour un enregistrement existant dans la base de données
     * 
     * @param boolean $without_null Si true, exclut les propriétés null de la mise à jour
     * @return int|array L'ID mis à jour en cas de succès, ou un tableau d'erreur en cas d'échec
     */
    private function update($without_null)
    {
        try {
            // Si on veut exclure les valeurs null
            if($without_null == true){
                // Parcourt toutes les propriétés de l'objet
                foreach($this as $key=>$value){
                    // Si la valeur est null ET ce n'est pas la clé primaire
                    if($value ==  null && $key != $this->get_db_table_pk())
                        // Supprime cette propriété (elle ne sera pas mise à jour)
                        unset($this->$key);
                }
            }
            
            // Sauvegarde l'ID avant de le supprimer
            // (on a besoin de l'ID pour la clause WHERE)
            $insert_id = $this->{$this->get_db_table_pk()};
            
            // Supprime la clé primaire temporairement
            // (on ne veut pas la mettre à jour)
            unset($this->{$this->get_db_table_pk()});
            
            // Met à jour l'enregistrement WHERE clé_primaire = $insert_id
            $update = $this->db->update($this->get_db_table(), $this, array(
                $this->get_db_table_pk() => $insert_id
            ));
            
            // Réassigne l'ID à l'objet
            $this->{$this->get_db_table_pk()} = $insert_id;
            
            // Vérifie s'il y a eu une erreur lors de la mise à jour
            if (!$update) {
                // Récupère les détails de l'erreur
                $db_error = $this->db->error();
                $d['status'] = "error";
                $d['message'] = "Database error! Error Code [" . $db_error['code'] . "] Error: " . $db_error['message'];
                return $d;
            }
            
            // Retourne l'ID de l'enregistrement mis à jour
            return $this->{$this->get_db_table_pk()};
        }
        catch (Exception $e) {
            // Capture toute exception PHP
            $d['status'] = "error";
            $d['message'] = $e->getMessage();
            return $d;
        }
    }

    /**
     * PUBLIC - Sauvegarde un enregistrement (insertion ou mise à jour automatique)
     * 
     * Détecte automatiquement s'il faut insérer ou mettre à jour :
     * - Si la clé primaire est définie → UPDATE
     * - Si la clé primaire est vide → INSERT
     * 
     * @param boolean $without_null Si true, exclut les propriétés null
     * @return array Tableau avec ['status', 'message', 'id']
     */
    public function save($without_null = false)
    {
        // Vérifie si la clé primaire existe
        if(isset($this->{$this->get_db_table_pk()}))
            // Si oui : met à jour l'enregistrement existant
            $result = $this->update($without_null);
        else
            // Si non : crée un nouvel enregistrement
            $result = $this->insert($without_null);

        // Si $result est un tableau, c'est une erreur → la retourne
        if(is_array($result)){
            return $result;
        }

        // Vérifie que la transaction s'est bien déroulée
        if ($this->db->trans_status()) {
            $d['status'] = "success";
            $d['message'] = 'Enregistrement effectué avec succées.';
        }
        else {
            $d['status'] = "error";
            $d['message'] = "Erreur d'enregistrement.";
        }
        
        // Ajoute l'ID de l'enregistrement au tableau de réponse
        $d['id'] = $result;
        return $d;
    }

    /**
     * PUBLIC - Supprime l'enregistrement courant de la base de données
     * 
     * @return array Tableau avec ['status', 'message']
     */
    public function delete(){
        // Supprime WHERE clé_primaire = valeur_actuelle
        $this->db->delete($this->get_db_table(), array(
            $this->get_db_table_pk() => $this->{$this->get_db_table_pk()}
        ));
        
        // Vérifie si au moins une ligne a été affectée
        if ($this->db->affected_rows()) {
            $d['status'] = "success";
            $d['message'] = 'Suppression effectuée avec succées.';
        }
        else {
            $d['status'] = "error";
            $d['message'] = 'Error! ID ['.$this->{$this->get_db_table_pk()}.'] not found';
        }
        return $d;
    }

    /**
     * PUBLIC - Récupère TOUS les enregistrements de la table
     * 
     * @return array Tableau contenant tous les enregistrements
     */
    public function get_data(){
        // SELECT * FROM [table]
        return $this->db->select('*')
                        ->from($this->get_db_table())
                        ->get()
                        ->result();
    }

    /**
     * PUBLIC - Récupère UN enregistrement spécifique et remplit l'objet
     * 
     * Cherche l'enregistrement basé sur la clé primaire définie dans $this
     * Puis remplit toutes les propriétés de l'objet avec les colonnes trouvées
     * 
     * @return void Modifie directement les propriétés de l'objet
     */
    public function get_record(){
        // SELECT * FROM [table] WHERE clé_primaire = valeur_actuelle
        $row = $this->db->select('*')
                        ->from($this->get_db_table())
                        ->where($this->get_db_table_pk(), $this->{$this->get_db_table_pk()})
                        ->get()
                        ->result();
        
        // reset() retourne le premier élément du tableau résultat
        $row = reset($row);
        
        // Si aucun enregistrement n'a été trouvé
        if($row == null)
            // Réinitialise la clé primaire à null
            $this->{$this->get_db_table_pk()} = null;
        else{
            // Pour chaque colonne de l'enregistrement trouvé
            foreach ($row as $param => $value){
                // Assigne la valeur à la propriété correspondante de l'objet
                $this->{$param} = $value;
            }
        }
    }

}