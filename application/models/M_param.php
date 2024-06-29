<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_param extends CI_Model
{
    public function check_unique_field($table, $col_name, $val_to_search, $key_name, $valKey)
    {
         $this->db->select($col_name)->from($table);
         $val_to_search = empty($val_to_search) ? '???--???' : $val_to_search;
         $this->db->where($col_name, trim($val_to_search));
         if(!empty($key_name) && !empty($valKey)){
             $this->db->where($key_name.'!=', trim($valKey));
         }
         $result = $this->db->get()->result();

        if(!empty($result))
        {
            $d = array();
            $d['status'] = 'error';
            $d['message'] = "La valeur ".$val_to_search." existe déjà.";
            show_json_break($d);
        }
    }
}