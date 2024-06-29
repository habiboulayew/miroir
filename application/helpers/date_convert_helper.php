<?php

    //echo date_pasre_fr2en('15/10/2015');
    //echo date_parse_en2fr('2010-11-10');
    // dd/mm/yyyy ==> yyyy-mm-dd // a suprrimer

    function date_parse_fr2en($date, $sep='/')
    {
        if($date == null || $date == '')
            return '';
        else
        {
            $dateNaiss = $date == '' ? NULL : date('Y-m-d', strtotime(str_replace($sep, '-', $date)));
            return $dateNaiss;
        }
    }
    function date_heure_parse_fr2en($date, $sep='/')
    {
        if($date == null || $date == '')
            return NULL;
        else
        {
            $dateConvert = $date == '' ? NULL : date('Y-m-d H:i:s', strtotime(str_replace($sep, '-', $date)));
            return $dateConvert;
        }
    }

    // yyyy-mm-dd ==>  dd/mm/yyyy
    function date_parse_en2fr($date)
    {
        if($date == null || $date == '')
            return '';
        else
        {
            $new_date = date('d/m/Y',strtotime($date));
            return $new_date;
        }
    }
    function date_heure_parse_en2fr($date)
    {
        if($date == null || $date == '')
            return '';
        else
        {
            $new_date = date('d/m/Y H:i:s',strtotime($date));
            return $new_date;
        }
    }

    function getJour_fr($date = null)
    {
        if($date == null)
            $day = date('N');
        else{
            $day = date("N", strtotime($date));
        }

        switch ($day) {
            case 1:
                return "lundi";
                break;
            case 2:
                return "mardi";
                break;
            case 3:
                return "mercredi";
                break;
            case 4:
                return "jeudi";
                break;
            case 5:
                return "vendredi";
                break;
            case 6:
                return "samedi";
                break;
            case 7:
                return "dimanche";
                break;
            default:
                return "inexistant";
                break;
        }
    }