<?php 

namespace App\Services;

use Response;
use Codedge\Fpdf\Fpdf\Fpdf;
use Codedge\Fpdf\Fpdf\Exception;

class PurchaseOrderPdf extends Fpdf {

    protected $order;

    public function __construct($order)
    {
        parent::__construct();
        $this->order = $order;
        $this->printPdf();
    }

    public function Header()
    {
        $border = false;
        $order = $this->order;
        $vendor = $order->vendor()->first();

        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, 'Orden de Compra', $border, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $this->Cell(15, 4, 'Folio:', $border, 0, '');
        $this->SetTextColor(200, 0, 0);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20, 4, str_pad((string)$order->id, 7, "0", STR_PAD_LEFT), $border, 0, '');
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(107, 4, '', $border, 0, '');
        $this->Cell(19, 4, 'Elaboración:', $border, 0, 'R');
        $this->Cell(0, 4, $this->formatDate($order->order_date), $border, 1, 'R');

        $this->Cell(142, 4, '', $border, 0, '');
        $this->Cell(19, 4, 'Impresión:', $border, 0, 'R');
        $this->Cell(0, 4, $this->formatDate(date('Y-m-d H:i:s')), $border, 1, 'R');
        
        $this->Ln(1);

        $this->Cell(16, 4, 'Proveedor:', $border, 0, '');
        $this->Cell(96, 4, $vendor->name, $border, 1, '');

        $this->Cell(16, 4, 'Contacto:', $border, 0, '');
        $this->Cell(96, 4, $vendor->contact, $border, 0, '');
        $this->SetFont('Arial', 'BU', 7);
        $this->Cell(0, 4, 'DATOS DE FACTURACIÓN:', $border, 1, '');
        $this->SetFont('Arial', '', 8);

        $this->Cell(16, 4, 'Teléfonos:', $border, 0, '');
        $this->Cell(96, 4, $vendor->phone.' / '.$vendor->mobile, $border, 0, '');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 4, 'R.F.C. '.($vendor->rfc ?: '-'), $border, 1, '');
        $this->SetFont('Arial', '', 8);

        $this->Cell(16, 4, 'Email:', $border, 0, '');
        $this->Cell(96, 4, $vendor->email, $border, 0, '');
        $this->MultiCell(0, 4, $vendor->address, $border, '');

        $this->Ln(2);
        

        $this->SetFont('Arial', 'B', 8);
        $border = 'B';

        $this->Cell(12, 4, 'Cant.', $border, 0, 'C');
        $this->Cell(25, 4, 'Clave', $border, 0, 'L');
        $this->Cell(117, 4, 'Descripción', $border, 0, 'L');
        $this->Cell(16, 4, 'Precio', $border, 0, 'C');
        $this->Cell(20, 4, 'Importe', $border, 0, 'C');
        $this->Cell(0,  4, '', $border, 1);
    }

    public function printPdf()
    {
        $border = false;
        $fill = false;
        $order = $this->order;
        
        $this->AddPage();
        
        // print details
        $this->SetFont('Helvetica', '', 8);
        $this->SetFillColor(240, 240, 240);
        
        $details = $order->purchase_order_details()->get();
        foreach ($details as $item) {
            $this->Cell(12, 5, $item->quantity, $border, 0, 'R', $fill);
            $this->Cell(25, 5, $item->product()->first()->code, $border, 0, 'L', $fill);
            $this->Cell(117, 5, substr($item->product()->first()->description, 0, 65), $border, 0, 'J', $fill);
            $this->Cell(16, 5, number_format($item->price, 2), $border, 0, 'R', $fill);
            $this->Cell(20, 5, number_format($item->total, 2), $border, 0, 'R', $fill);
            $this->Cell(0,  5, '', $border, 1);

            $fill = !$fill;
        }

        $this->Ln(1);

        $border = 'T';
        $this->SetFont('Helvetica', 'B', 8);
        $this->Cell(154, 5, '', false, 0);
        $this->Cell(16, 5, 'Total: ', $border, 0, 'R');
        $this->Cell(20, 5, number_format($order->total, 2), $border, 0, 'R');
        $this->Cell(0,  5, '', false, 1);
    }

    public function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Arial','I',7);
        $this->Cell(0, 10, 'Página '.$this->PageNo()." de {nb}", 0, 0, 'C');
    }

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        parent::Cell($w, $h, utf8_decode($txt), $border, $ln, $align, $fill, $link);
    }


    private function formatDate($date_time)
    {
        $dt = explode(' ', $date_time);
        $date = explode('-', $dt[0]);
        return implode("/", array_reverse($date)) .' '. $dt[1];
    }

}
