<?php

namespace App;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use  Crabbly\Fpdf\Fpdf;



class pdfClass extends Fpdf
{
    // Page header
//    function Header()
//    {
//        // Logo
//        $this->Image('logo.png',10,6,30);
//        // Arial bold 15
//        $this->SetFont('Arial','B',15);
//        // Move to the right
//        $this->Cell(80);
//        // Title
//        $this->Cell(30,10,'Title',1,0,'C');
//        // Line break
//        $this->Ln(20);
//    }



// Page footer
    function Footer()
    {

        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial','I',10);
        // Print centered page number
        $this->Cell(160,2,'System Generated Report: Sabify',0,0,'L');
        $this->SetFont('Arial','',10);
        $this->Cell(30,2,'Page | '.$this->PageNo(),0,0,'R');

    }
}