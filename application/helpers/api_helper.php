<?php

    function api_base_url($base)
    {
        if($base == 'ien')
            return 'https://ien.education.sn/';
        else if($base == 'apps')
            return 'https://apps.education.sn/';
        else if($base == 'codeco')
            return 'https://codeco.education.sn/';
        else if($base == 'management')
            return 'https://management.education.sn/';
        else if($base == 'planete')
            return 'https://planete.education.sn/';
    }

    //API Post generic
    function apiPostData($base_url, $link_url, $array, $type = 'json'){
        try{
            $url = api_base_url($base_url).$link_url;
            if($type == 'array') //send array POST
            {
                 try{
                    $options = array(
                        'ssl'=>array( "verify_peer"=>false,"verify_peer_name"=>false),
                        'http' => array(
                            'method' => 'POST',
                            'content' => $array,
                            'header'  => 'Content-type: application/x-www-form-urlencoded'
                        )
                    );
                    $context = stream_context_create($options);
                    $result = file_get_contents($url, false, $context);
                    return json_decode($result);
                }
                catch(Exception $e){
                     //var_dump($e);
                     return array();
                }
            }
            else if($type == 'json') //send json POST
            {
                try{
                    $json = json_encode($array);
                    $options = array(
                        'ssl'=>array( "verify_peer"=>false,"verify_peer_name"=>false),
                        'http' => array(
                            'method' => 'POST',
                            'content' => $json,
                            'header' => "Content-Type: application/json\r\n" .
                                "Accept: application/json\r\n"
                        )
                    );
                    $context = stream_context_create($options);
                    $result = file_get_contents($url, false, $context);
                    return json_decode($result);
                }
                catch(Exception $e){
                    //var_dump($e);
                    return array();
                }
            }
            else
                return array();
        }
        catch(Exception $e){
            return array();
        }
    }

    function apiGetData($base_url, $link_url, $type = 'json'){
        try{
            $url = api_base_url($base_url).$link_url;
            $arrContextOptions=array("ssl"=>array( "verify_peer"=>false,"verify_peer_name"=>false));
            $json_content = file_get_contents($url, false, stream_context_create($arrContextOptions));
            if($type == 'array')
                return json_decode(remove_utf8_bom($json_content), true);
            else if($type == 'json')
                return json_decode(remove_utf8_bom($json_content));
            else
                return null;
        }
        catch(Exception $e){
            //var_dump($e);
            return array();
        }
    }