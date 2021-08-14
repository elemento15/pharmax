<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;
use App\Services\PurchaseOrderPdf;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Payment;
use App\Models\PurchasePayment;
use App\Models\VendorPrice;

class PurchaseOrdersController extends AppController
{
    protected $mainModel = 'App\Models\PurchaseOrder';

    // params needen for index
    protected $searchFields = ['id'];
    protected $indexPaginate = 10;
    protected $indexJoins = ['vendor', 'purchase_payments.payment.type'];
    protected $orderBy = ['field' => 'id', 'type' => 'DESC'];

    // params needer for show
    protected $showJoins = ['vendor', 'purchase_order_details', 'purchase_order_details.product'];
    
    // params needed for store/update
    protected $defaultNulls = [];
    protected $formRules = [
        'vendor_id'  => 'required'
    ];

    protected $allowDelete = false;
    protected $allowUpdate = false;
    protected $allowStore  = true;
    protected $except = [];

    protected $useTransactions = true;


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $subtotal = 0;
        $iva_amount = 0;
        $total = 0;

        if (! count($request->purchase_order_details)) {
            return Response::json(array('msg' => 'Agregue partidas'), 500);
        }
        
        try {
            $purchase = new PurchaseOrder;
            $purchase->vendor_id = $request->vendor_id;
            $purchase->comments = $request->comments;
            $purchase->order_date = date('Y-m-d H:i:s');
            $purchase->save();

            foreach ($request->purchase_order_details as $item) {
                if (isset($item['_deleted'])) continue;

                $dt = new PurchaseOrderDetail;
                $dt->product_id = intval($item['product_id']);
                $dt->quantity = floatval($item['quantity']);
                $dt->price = floatval($item['price']);
                $dt->subtotal = $dt->quantity * $dt->price;
                $dt->iva = floatval($item['iva']);
                $dt->iva_amount = $dt->subtotal * ($dt->iva / 100);
                $dt->total = $dt->iva_amount + $dt->subtotal;
                
                $subtotal += $dt->subtotal;
                $iva_amount += $dt->iva_amount;
                $total += $dt->total;

                $purchase->purchase_order_details()->save($dt);

                // update vendor price
                $this->updateVendorPrice($dt->product_id, $purchase->vendor_id, $dt->price);
            }

            $purchase->subtotal = $subtotal;
            $purchase->iva_amount = $iva_amount;
            $purchase->total = $total;
            $purchase->save();

            return $purchase;

        } catch (Exception $e) {
            return Response::json(array('msg' => 'Error al guardar'), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        return Response::json(array('msg' => 'No puede editar una Order de Compra'), 500);
    }

    /**
     * Cancel cotization
     *
     * @param  int  $id
     * @return Response
     */
    public function cancel($id)
    {
        $record = PurchaseOrder::find($id);

        if (! $record) {
            return Response::json(array('msg' => 'Registro no encontrado'), 500);
        }

        if ($record->status != 'N') {
            return Response::json(array('msg' => 'Estado inválido'), 500);
        }

        if ($record->total > $record->balance) {
            return Response::json(array('msg' => 'Abonos activos existentes'), 500);
        }

        $record->status = 'C';

        if ($record->save()) {
            return Response::json($record);
        } else {
            return Response::json(array('msg' => 'Error al cancelar'), 500);
        }
    }

    public function save_payment($id, Request $request)
    {
        $order = PurchaseOrder::find($id);

        $amount = floatval($request->amount);
        $type = intval($request->payment_type_id);

        // validations
        if ($amount <= 0) {
            return Response::json(array('msg' => 'Importe inválido'), 500);
        }

        if ($type <= 0) {
            return Response::json(array('msg' => 'Tipo de pago inválido'), 500);
        }

        if ($amount > $order->balance) {
            return Response::json(array('msg' => 'Importe mayor al saldo '. $order->balance), 500);
        }

        // create Payment
        $payment = Payment::create([
            'payment_date' => date('Y-m-d H:i:s'),
            'amount' => $amount,
            'payment_type_id' => $type,
            'comments' => $request->comments
        ]);

        // create PurchasePayment
        PurchasePayment::create([
            'purchase_order_id' => $order->id,
            'payment_id' => $payment->id,
            'amount' => $amount
        ]);

        // mark purchase order as paid
        if ($amount == $order->balance) {
            $order->status = 'P';
            $order->save();
        } 

        return $payment;
    }

    /**
     * Print purchase order pdf
     *
     * @param  int  $id
     * @return Response
     */
    public function print_pdf($id, Request $request)
    {
        $order = PurchaseOrder::find($id);
        $pdf = new PurchaseOrderPdf($order);
        $pdf->AliasNbPages();
        return Response::make($pdf->Output('I', 'orden_'.$id.'.pdf'), 200, array('content-type' => 'application/pdf'));
    }


    private function updateVendorPrice($product_id, $vendor_id, $price)
    {
        $vendor_price = VendorPrice::where('vendor_id', $vendor_id)->where('product_id', $product_id)->first();
        
        if ($vendor_price) {
            $vendor_price->price = $price;
            $vendor_price->save();
        } else {
            $vendor_price = new VendorPrice;
            $vendor_price->vendor_id = $vendor_id;
            $vendor_price->product_id = $product_id;
            $vendor_price->price = $price;
            $vendor_price->save();
        }
    }
}
