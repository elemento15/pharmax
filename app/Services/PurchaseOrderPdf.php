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

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(40, 8, substr($order->order_date, 0, 10), $border, 0);

        $this->SetFont('Arial', 'B', 16);
        $this->Cell(110, 8, 'Orden de Compra', $border, 0, 'C');
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20, 8, 'Folio: ', $border, 0, 'R');

        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(200, 0, 0);
        $this->Cell(0, 8, str_pad((string)$order->id, 7, "0", STR_PAD_LEFT), $border, 1, 'R');
        
        $this->Ln(3);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 10);

        $name_rfc = $vendor->name;
        $name_rfc.= ($vendor->rfc) ? ' ('.$vendor->rfc.')' : '';
        

        $phone = ($vendor->phone) ? $vendor->phone : '';
        if ($vendor->mobile) {
            $phone = ($phone) ? $phone.' - '.$vendor->mobile : $vendor->mobile;
        }

        $this->Cell(20, 6, 'Proveedor: ', $border, 0);
        $this->Cell(0,  6, $name_rfc, 'B', 1);
        
        $this->Cell(20, 6, 'Contacto: ', $border, 0);
        $this->Cell(80, 6,  $vendor->contact, 'B', 0);
        $this->Cell(18, 6, ' Teléfono: ', $border, 0);
        $this->Cell(0,  6,  $phone, 'B', 1);

        $this->Cell(20, 6, 'Dirección: ', $border, 0);
        $this->Cell(0,  6, $vendor->address, 'B', 1);
        
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 9);
        $border = 'B';

        $this->Cell(18, 5, 'Cantidad', $border, 0, 'C');
        $this->Cell(40, 5, 'Código', $border, 0, 'L');
        $this->Cell(90, 5, 'Descripción', $border, 0, 'L');
        $this->Cell(21, 5, 'Precio', $border, 0, 'C');
        $this->Cell(21, 5, 'Total', $border, 0, 'C');
        $this->Cell(0,  5, '', $border, 1);
    }

    public function printPdf()
    {
        $border = false;
        $fill = false;
        $order = $this->order;
        
        $this->AddPage();
        
        // print details
        $this->SetFont('Helvetica', '', 9);
        $this->SetFillColor(230, 230, 230);
        
        $details = $order->purchase_order_details()->get();
        foreach ($details as $item) {
            $this->Cell(18, 5, $item->quantity, $border, 0, 'R', $fill);
            $this->Cell(40, 5, $item->product()->first()->code, $border, 0, 'L', $fill);
            $this->Cell(90, 5, substr($item->product()->first()->description, 0, 55), $border, 0, 'L', $fill);
            $this->Cell(21, 5, number_format($item->price, 2), $border, 0, 'R', $fill);
            $this->Cell(21, 5, number_format($item->total, 2), $border, 0, 'R', $fill);
            $this->Cell(0,  5, '', $border, 1);

            $fill = !$fill;
        }

        $this->Ln(1);

        $border = 'T';
        $this->SetFont('Helvetica', 'B', 9);
        $this->Cell(148, 5, '', false, 0);
        $this->Cell(21, 5, 'Total: ', $border, 0, 'R');
        $this->Cell(21, 5, number_format($order->total, 2), $border, 0, 'R');
        $this->Cell(0,  5, '', false, 1);
    }

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        parent::Cell($w, $h, utf8_decode($txt), $border, $ln, $align, $fill, $link);
    }

}
