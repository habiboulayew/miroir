<?php
require_once 'application/core/MY_Model_Interface.php';

abstract class MY_Model extends CI_Model implements MY_Model_Interface{

    function __construct()
    {
        parent::__construct();
    }

    private function insert($without_null)
    {
        try {
            if($without_null == true){
                foreach($this as $key=>$value){
                    if($value ==  null && $key != $this->get_db_table_pk())
                        unset($this->$key);
                }
            }
            unset($this->{$this->get_db_table_pk()});
            $insert = $this->db->insert($this->get_db_table(), $this);
            //en cas d'erreur
            if (!$insert) {
                $db_error = $this->db->error();
                $d['status'] = "error";
                $d['message'] = "Database error! Error Code [" . $db_error['code'] . "] Error: " . $db_error['message'];
                return $d;
            }
            $this->{$this->get_db_table_pk()} = $this->db->insert_id();
            return $this->{$this->get_db_table_pk()};
        }
        catch (Exception $e) {
            $d['status'] = "error";
            $d['message'] = $e->getMessage();
            return $d;
        }
    }

    private function update($without_null)
    {
        try {
            if($without_null == true){
                foreach($this as $key=>$value){
                    if($value ==  null && $key != $this->get_db_table_pk())
                        unset($this->$key);
                }
            }
            $insert_id = $this->{$this->get_db_table_pk()};
            unset($this->{$this->get_db_table_pk()});
            $update = $this->db->update($this->get_db_table(), $this, array(
                $this->get_db_table_pk() => $insert_id
            ));
            $this->{$this->get_db_table_pk()} = $insert_id;
            //en cas d'erreur
            if (!$update) {
                $db_error = $this->db->error();
                $d['status'] = "error";
                $d['message'] = "Database error! Error Code [" . $db_error['code'] . "] Error: " . $db_error['message'];
                return $d;
            }
            return $this->{$this->get_db_table_pk()};
        }
        catch (Exception $e) {
            $d['status'] = "error";
            $d['message'] = $e->getMessage();
            return $d;
        }
    }

    public function save($without_null = false)
    {
        if(isset($this->{$this->get_db_table_pk()}))
            $result = $this->update($without_null);
        else
            $result = $this->insert($without_null);

        if(is_array($result)){
            return $result;
        }

        if ($this->db->trans_status()) {
            $d['status'] = "success";
            $d['message'] = 'Enregistrement effectué avec succées.';
        }
        else {
            $d['status'] = "error";
            $d['message'] = "Erreur d'enregistrement.";
        }
        $d['id'] = $result;
        return $d;
    }

    public function delete(){
        $this->db->delete($this->get_db_table(), array($this->get_db_table_pk() => $this->{$this->get_db_table_pk()}));
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

    public function get_data(){
        return $this->db->select('*')
                        ->from($this->get_db_table())
                        ->get()
                        ->result();
    }

    public function get_record(){
        $row = $this->db->select('*')
                        ->from($this->get_db_table())
                        ->where($this->get_db_table_pk(), $this->{$this->get_db_table_pk()})
                        ->get()
                        ->result();
        $row = reset($row);
        if($row == null)
            $this->{$this->get_db_table_pk()} = null;
        else{
           foreach ($row as $param => $value){
                $this->{$param} = $value;
           }
        }
    }

}