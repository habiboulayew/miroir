<?php

function btn_add_action($smenu_code = null, $attr = null)
{
    /*$tab_smrole = $this->session->smenu_roles;
	if($tab_smrole[$smenu_code]['d_add'] == 1)
	{*/
    echo '<div class="row">
                <div class="col-sm-12" style="margin-bottom: 10px">
                    <button type="button" id="btn_add" class="btn btn-primary" '.$attr.'>Ajouter <span lass="m-l-5"><i
                                class="fa fa-plus-square"></i></span></button>
                </div>
            </div>';
    //}
}


function btn_edit_action($id, $smenu_code = null, $attr = null)
{
    /*$tab_smrole = $this->session->smenu_roles;
	if($tab_smrole[$smenu_code]['d_upd'] == 1)
	{
    echo '<a href="#" class="on-default btn_edit"
          id="' . $id . '"><i class="fa fa-pencil"></i></a>&nbsp;';
    }*/
    echo '<button class="btn btn-white btn-sm btn_edit"
          id="' . $id . '" '.$attr.'><i class="fa fa-pencil" style="color: #337ab7;"></i> Modifier</button>&nbsp;';
}


function btn_delete_action($id, $smenu_code = null, $attr = null)
{
    /*$tab_smrole = $this->session->smenu_roles;
    if($tab_smrole[$smenu_code]['d_del'] == 1)
    {
    echo '<a href="#" class="on-default btn_delete"
          id="' . $id . '"><i class="fa fa-trash-o" style="color:red"></i></a>&nbsp;';
    }*/
    echo '<button class="btn btn-white btn-sm btn_delete"
          id="' . $id . '" '.$attr.'><i class="fa fa-trash-o" style="color:red"></i> Supprimer</button>&nbsp;';
}

function btn_show_action($id, $smenu_code = null, $attr = null)
{
    /*$tab_smrole = $this->session->smenu_roles;
	if($tab_smrole[$smenu_code]['d_read'] == 1)
	{
    echo '<a href="#" class="on-default btn_show"
           id="' . $id . '"><i class="fa fa-eye" style="color:#CCCCCC"></i></a>';
    }*/
    echo '<button class="on-default btn_show"
           id="' . $id . '"  '.$attr.'><i class="fa fa-eye" style="color:#CCCCCC"></i></button>';
}

function empty2null($value)
{
    if (trim($value) == '')
        return null;
    else
        return $value;
}

function to_upercase($text, $toupper = true)
{
    $search = array('à', 'À', 'é', 'É', 'è', 'È', 'ï', 'Ï', 'ç', 'Ç');
    $replace = array('a', 'A', 'e', 'E', 'e', 'E', 'i', 'I', 'c', 'C');
    $text = str_replace($search, $replace, trim($text));
    $text_preg = preg_replace("/[^a-z0-9 ]/i", '', $text);

    if ($toupper == true)
        return strtoupper($text_preg);
    else
        return $text_preg;
}

function to_tolower($text, $tolower = true)
{
    $search = array('à', 'À', 'é', 'É', 'è', 'È', 'ï', 'Ï', 'ç', 'Ç', 'ô', 'Ô');
    $replace = array('a', 'A', 'e', 'E', 'e', 'E', 'i', 'I', 'c', 'C', 'o', 'O');
    $text = str_replace($search, $replace, trim($text));
    $text_preg = preg_replace("/[^a-z0-9 ]/i", '', $text);

    if ($tolower == true)
        return strtolower($text_preg);
    else
        return $text_preg;
}

//swap data
function data_swap($tab, $id, $valeur, $type = 'O', $obj_value = false)
{
    $t_swap = array();
    $tab = json_decode(json_encode($tab));

    if ($obj_value == false)
        foreach ($tab as $val) {
            $t_swap['id' . $val->{$id}] = $val->{$valeur};
        }
    else {
        if ($type == 'O' || $type == 'o')
            foreach ($tab as $val)
                $t_swap['id' . $val->{$id}] = (object)$val;
        else
            foreach ($tab as $val)
                $t_swap['id' . $val->{$id}] = (array)$val;
    }

    if ($type == 'O' || $type == 'o')
        return (object)$t_swap;
    else
        return (array)$t_swap;
}


//fonction de stat recap
function show_swap_value($data_swap, $key)
{
    if (is_object($data_swap) && !empty($data_swap->{'id' . $key}))
        return $data_swap->{'id' . $key};
    else if (is_array($data_swap) && !empty($data_swap['id' . $key]))
        return $data_swap['id' . $key];
    else
        return '';
}


function show_json($data)
{
    echo json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
}

function show_json_break($d)
{
    echo json_encode($d, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    die(); exit();
}

function check_unique_field($table, $col_name, $val_to_search, $key_name = null, $valKey = null)
{
    $db = get_instance();
    $db->load->model('M_param', 'param');
    $db->param->check_unique_field($table, $col_name, $val_to_search, $key_name, $valKey);
}


//*****Bakary SANE****//

//Suppresion des accents
function majus($str, $encoding = 'utf-8')
{
    $str = rtrim(ltrim(trim(str_replace(array("'", ",", ".", "_", " - ", " -", "- "),
        array('', '', '', '', '-', '', ''),
        $str)), '-'), '-');
    // transformer les caractères accentués en entités HTML
    $str = htmlentities($str, ENT_NOQUOTES, $encoding);

    // remplacer les entités HTML pour avoir juste le premier caractères non accentués
    // Exemple : "&ecute;" => "e", "&Ecute;" => "E", "Ã " => "a" ...
    $str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml|\');#', '\1', $str);

    // Remplacer les ligatures tel que : Œ, Æ ...
    // Exemple "Å"" => "oe"
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
    // Supprimer tout le reste
    $str = preg_replace('#&[^;]+;#', '', $str);

    return strtoupper($str);
}

//Suppresion des accents
function del_accent($str, $encoding = 'utf-8')
{
    $str = htmlentities($str, ENT_NOQUOTES, $encoding);
    $str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
    // Supprimer tout le reste
    $str = preg_replace('#&[^;]+;#', '', $str);
    return $str;
}


//test l'existence d'un repertoire
function check_folder($chemin)
{
    if (!file_exists($chemin))
        mkdir($chemin, 0777, true);
    return file_exists($chemin);
}

function remove_utf8_bom($text)
{
    $text = mb_convert_encoding($text, 'UTF-8', 'ASCII,UTF-8,ISO-8859-1');
    if(substr($text, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) $text = substr($text, 3);
    return $text;
}


function file_get_contents_custom($url){
    $arrContextOptions=array("ssl"=>array( "verify_peer"=>false,"verify_peer_name"=>false));
	return file_get_contents($url, false, stream_context_create($arrContextOptions));
}