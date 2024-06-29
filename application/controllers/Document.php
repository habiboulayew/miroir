<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use setasign\Fpdi\Fpdi;
use setasign\FpdiProtection\FpdiProtection;

class Document extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_document_profil', 'document_profil');
        $this->load->model('M_document', 'document');
    }

    public function index()
    {
        $args = func_get_args();
        $id = !empty($args[0]) ? $args[0] : '';
        $data['id'] = $id;

        if (empty($id)) {
            $data = $this->document_profil->get_document_profil($this->session->id_profil);
            $this->session->set_userdata('data_menu', $data);
            $file_path = base_url("DATA/default.pdf?12345");
        } else {

            $this->document->id_document = $id;
            $this->document->get_record();

            $data['password'] = $this->document->password;
            $id_personnel = $this->session->id_personnel;

            $file = "./DATA/$id.pdf";

            if ($this->document->password != '0' || $this->document->filigrane != '0') {
                $file_user = "./DATA/" . ucfirst(to_tolower($this->document->titre)) . "_$id_personnel" . "_$id.pdf";
                $file_path = base_url("DATA/" . ucfirst(to_tolower($this->document->titre)) . "_$id_personnel" . "_$id.pdf");
            } else {
                $file_user = "./DATA/" . ucfirst(to_tolower($this->document->titre)) . ".pdf";
                $file_path = base_url("DATA/" . ucfirst(to_tolower($this->document->titre)) . ".pdf");
            }


            if (!file_exists($file_user)) {
                ini_set('memory_limit', -1);

                $_SESSION['filigrane_texte'] = null;
                $_SESSION['filigrane_indentification'] = null;
                $_SESSION['filigrane_confidentiel'] = null;
                $_SESSION['filigrane_fonction'] = null;

                if ($this->document->filigrane == '1') {
                    $_SESSION['filigrane_texte'] = $this->document->filigrane_texte;
                    if ($this->document->filigrane_indentification == '1'){
                        $_SESSION['filigrane_indentification'] = $_SESSION['prenom'] . ' ' . $_SESSION['nom'];
                        $_SESSION['filigrane_fonction'] = $_SESSION['fonction'];
                    }

                    if ($this->document->filigrane_confidentiel == '1'){
                        $_SESSION['filigrane_confidentiel'] = "Veuillez respecter l'obligation de confidentialité de ce document.";
                    }
                }

                $pdf = new PDF_extend();

                $pageCount = $pdf->setSourceFile($file);
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $_SESSION['size_orientation'] = $size['orientation'];
                    $pdf->AddPage($size['orientation'], $size);
                    $pdf->useTemplate($templateId);
                }

                $pdf->Output($file_user, 'F');
                //protection

                if ($this->document->password != '0') {
                    if ($this->document->password == '1')
                        $userPass = "sA95HF:m@#w#1!è$ %e_ yvzm{}[]()/\'`~,;:.ee129$id@#$%Gj9@#$%";
                    else if ($this->document->password == '2')
                        $userPass = $this->session->email_connexion;
                    else
                        $userPass = "";

                    $ownerPass = "sA95HF:m@#$%@#$%eyvz@#$%Gj9@#$%";
                    $pdf = new FpdiProtection();
                    $pdf->setProtection(array(), $userPass, $ownerPass, 3);
                    $pageCount = $pdf->setSourceFile($file_user);
                    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        $id = $pdf->importPage($pageNo);
                        $size = $pdf->getTemplateSize($id);
                        $pdf->AddPage($size['orientation'], $size);
                        $pdf->useTemplate($id);
                    }
                }

                $pdf->Output('F', $file_user);
            }
        }

        $data['file_path'] = $file_path;
        $this->load->view('acceuil', $data);
    }
}


class PDF_extend extends Fpdi
{
    var $angle = 0;

    function Rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1)
            $x = $this->x;
        if ($y == -1)
            $y = $this->y;
        if ($this->angle != 0)
            $this->_out('Q');
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }

    function _endpage()
    {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }

    function Header()
    {
        if ($_SESSION['size_orientation'] == 'P') {
            //Put the watermark
            if (!empty($_SESSION['filigrane_texte'])) {
                $this->SetFont('Arial', 'B', 65);
                $this->SetTextColor(255, 192, 203);
                $this->RotatedText(40, 180, $_SESSION['filigrane_texte'], 45);
            }

            if (!empty($_SESSION['filigrane_indentification'])) {
                $this->SetFont('Arial', 'B', 55);
                $this->SetTextColor(255, 192, 203);
                $this->RotatedText(60, 190, $_SESSION['filigrane_indentification'], 45);
            }

            if (!empty($_SESSION['filigrane_fonction'])) {
                $this->SetFont('Arial', 'B', 45);
                $this->SetTextColor(255, 192, 203);
                $this->RotatedText(80, 200, $_SESSION['filigrane_fonction'], 45);
            }

        } else if ($_SESSION['size_orientation'] == 'L') {
            //Put the watermark
            if (!empty($_SESSION['filigrane_texte'])) {
                $this->SetFont('Arial', 'B', 65);
                $this->SetTextColor(255, 192, 203);
                $this->RotatedText(45, 170, $_SESSION['filigrane_texte'], 45);
            }

            if (!empty($_SESSION['filigrane_indentification'])) {
                $this->SetFont('Arial', 'B', 55);
                $this->SetTextColor(255, 192, 203);
                $this->RotatedText(100, 170, $_SESSION['filigrane_indentification'], 45);
            }

            if (!empty($_SESSION['filigrane_fonction'])) {
                $this->SetFont('Arial', 'B', 45);
                $this->SetTextColor(255, 192, 203);
                $this->RotatedText(150, 170, $_SESSION['fonction'], 45);
            }
        }
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(255, 0, 0);


        if (!empty($_SESSION['filigrane_confidentiel']))
            $this->Cell(0, 0, utf8_decode($_SESSION['filigrane_confidentiel']), 0, 0, 'C');
    }

    function RotatedText($x, $y, $txt, $angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate(0);
    }
}
