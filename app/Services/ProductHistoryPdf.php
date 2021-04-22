<?php 

namespace App\Services;

use App\Models\PurchaseOrderDetail;

use Response;
use Codedge\Fpdf\Fpdf\Fpdf;
use Codedge\Fpdf\Fpdf\Exception;
use Carbon;

class ProductHistoryPdf extends Fpdf {

    protected $product, $vendor;

    public function __construct($product, $vendor)
    {
        parent::__construct();
        $this->product = $product;
        $this->vendor  = $vendor;
        $this->getDetails();
        $this->printPdf();
    }

    public function Header()
    {
        $border = false;

        $this->SetFont('Arial', 'B', 16);
        $this->Cell(40, 8, '', $border, 0);
        $this->Cell(110, 8, 'Historial de Producto', $border, 0, 'C');
        $this->Cell(40, 8, '', $border, 1, 'R');
        
        $this->Ln(2);
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(22, 4, 'Producto:', $border, 0, 'L');
        
        $this->SetFont('Arial', '', 10);
        $txt = ($this->product->code) ? $this->product->code.' - ' : '';
        $txt.= $this->product->description;
        $this->Cell(0, 4, $txt, $border, 1, 'L');
        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(22, 4, 'Proveedor:', $border, 0, 'L');

        $this->SetFont('Arial', '', 10);
        $txt = $this->vendor->name;
        $txt.= ($this->vendor->rfc) ? ' ('.$this->vendor->rfc.')' : '';
        $this->Cell(0, 4, $txt, $border, 1, 'L');

        $this->Ln(3);
        $border = 'B';

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, 'Orden #', $border, 0, 'R');
        $this->Cell(25, 5, 'Fecha', $border, 0, 'C');
        $this->Cell(25, 5, 'Status', $border, 0, 'C');
        $this->Cell(20, 5, 'Cantidad ', $border, 0, 'R');
        $this->Cell(22, 5, 'Precio ', $border, 0, 'R');
        $this->Cell(22, 5, 'Total ', $border, 1, 'R');

        $this->Ln(1);
    }

    public function printPdf()
    {
        $border = false;
        $fill = false;
        $data = $this->details;
        
        $this->AddPage();
        
        // print details
        $this->SetFont('Helvetica', '', 10);
        $this->SetFillColor(230, 230, 230);
        
        foreach ($data as $item) {
            $order = $item->purchase_order()->first();
            $order_date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->order_date);

            switch ($order->status) {
                case 'N' : $status = 'Nueva'; break;
                case 'C' : $status = 'Cancelada'; break;
                case 'P' : $status = 'Pagada'; break;
            }

            $this->Cell(20, 5, $order->id, $border, 0, 'R', $fill);
            $this->Cell(25, 5, $order_date->format('d/M/Y'), $border, 0, 'C', $fill);
            
            $this->Cell(25, 5, $status, $border, 0, 'C', $fill);
            
            $this->Cell(20, 5, number_format($item->quantity), $border, 0, 'R', $fill);
            $this->Cell(22, 5, number_format($item->price, 2), $border, 0, 'R', $fill);
            $this->Cell(22, 5, number_format($item->total, 2), $border, 0, 'R', $fill);

            $this->Cell(0,  5, '', $border, 1);

            $fill = !$fill;
        }

        $this->Ln(1);

        /*$border = 'T';
        $this->SetFont('Helvetica', 'B', 9);
        $this->Cell(148, 5, '', false, 0);
        $this->Cell(21, 5, 'Total: ', $border, 0, 'R');
        $this->Cell(21, 5, number_format($cotization->total, 2), $border, 0, 'R');
        $this->Cell(0,  5, '', false, 1);*/
    }

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        parent::Cell($w, $h, utf8_decode($txt), $border, $ln, $align, $fill, $link);
    }

    private function getDetails()
    {
        $product = $this->product->id;
        $vendor = $this->vendor->id;

        $data = PurchaseOrderDetail::whereHas('purchase_order', function ($query) use ($product, $vendor) {
            $query->where('vendor_id', $vendor);
        })->where('product_id', $product)->get();
        
        $this->details = $data;
    }

}
