<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;



class MdfClass extends Mpdf
{
    function Footer()
    {

        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial', 'I', 10);
        // Print centered page number
        $this->Cell(160, 2, 'System Generated Report: Sabify', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(30, 2, 'Page | ' . $this->PageNo(), 0, 0, 'R');
    }

    // Function to add image in cell
    function CellImage($imagePath, $width, $height, $border = 0, $ln = 0, $align = '', $fill = false)
    {
        $this->Cell($width, $height, '', $border, $ln, $align, $fill);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->Image($imagePath, $x, $y, $width, $height);
    }

    // Function to add image in cell and center it on the page
    function CellImageCenter($imagePath, $width, $height, $border = 0, $ln = 0, $fill = false)
    {
        // Calculate centered position
        $x = ($this->GetPageWidth() - $width) / 2;
        $this->SetX($x);

        // Add the image
        $this->Image($imagePath, $x, $this->GetY(), $width, $height);

        // Move Y position for next content
        $this->Ln($ln);
    }

    // Function to create multi-line text in a cell
    function MultiCellText($width, $height, $text)
    {
        $this->SetY(-75);
        // MultiCell(float w, float h, string txt [, mixed border [, string align [, boolean fill]]])
        $this->MultiCell($width, $height, $text, 0, 'L');
    }

    // Function to add centered image and comments
    function AddImageWithComments($imagePath, $width, $height, $comments)
    {
        $x = ($this->GetPageWidth() - $width) / 2;
        $this->SetX($x);
        // Center image
        $this->Image($imagePath, 0, 0, 100); // Adjust image size and position as needed

        // Set position for comments
        $this->SetXY(10, 110); // Adjust X and Y coordinates for comments
        $this->MultiCell(0, 10, $comments);
    }
}
