<?php

namespace App;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use  Crabbly\Fpdf\Fpdf;



class receiptpdf extends PDF_Rotate
{
    function Header()
    {
        //Put the watermark
        $this->SetFont('Arial','B',35);
        $this->SetTextColor(128,128,128);
        $this->RotatedText(12,120,'D U P L I C A T E',45);
    }

    function RotatedText($x, $y, $txt, $angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle,$x,$y);
        $this->Text($x,$y,$txt);
        $this->Rotate(0);
    }
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
//    function Footer()
//    {
//
//        $this->SetY(-15);
//        // Select Arial italic 8
//        $this->SetFont('Arial','I',10);
//        // Print centered page number
//        $this->Cell(160,2,'System Generated Report: Sabify',0,0,'L');
//        $this->SetFont('Arial','',10);
//        $this->Cell(30,2,'Page | '.$this->PageNo(),0,0,'R');
//
//    }
}