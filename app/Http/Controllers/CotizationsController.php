<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;
use App\Services\CotizationPdf;

use App\Models\Cotization;
use App\Models\CotizationDetail;
use App\Models\Payment;
use App\Models\CotizationPayment;

class CotizationsController extends AppController
{
    protected $mainModel = 'App\Models\Cotization';

    // params needen for index
    protected $searchFields = ['id'];
    protected $indexPaginate = 10;
    protected $indexJoins = ['customer', 'cotization_payments.payment.type'];
    protected $orderBy = ['field' => 'id', 'type' => 'DESC'];

    // params needer for show
    protected $showJoins = ['customer', 'cotization_details', 'cotization_details.product'];
    
    // params needed for store/update
    protected $defaultNulls = [];
    protected $formRules = [
        'customer_id'  => 'required'
    ];

    protected $allowDelete = false;


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
        
        try {
            $cotization = new Cotization;
            $cotization->customer_id = $request->customer_id;
            $cotization->comments = $request->comments;
            $cotization->cotization_date = date('Y-m-d H:i:s');
            $cotization->save();

            foreach ($request->cotization_details as $item) {
                if (isset($item['_deleted'])) continue;

                $dt = new CotizationDetail;
                $dt->product_id = intval($item['product_id']);
                $dt->quantity = floatval($item['quantity']);
                $dt->price = floatval($item['price']);
                $dt->subtotal = $dt->quantity * $dt->price;
                $dt->iva = floatval($item['iva']);
                $dt->iva_amount = $dt->subtotal * ($dt->iva / 100);
                $dt->total = $dt->iva_amount + $dt->subtotal;
                $dt->lot = $item['lot'];
                $dt->expiration = $item['expiration'];
                
                $subtotal += $dt->subtotal;
                $iva_amount += $dt->iva_amount;
                $total += $dt->total;

                $cotization->cotization_details()->save($dt);
            }

            $cotization->subtotal = $subtotal;
            $cotization->iva_amount = $iva_amount;
            $cotization->total = $total;
            $cotization->save();

            return $cotization;

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
        return Response::json(array('msg' => 'No puede editar una Cotización'), 500);
    }

    /**
     * Cancel cotization
     *
     * @param  int  $id
     * @return Response
     */
    public function cancel($id)
    {
        $record = Cotization::find($id);

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
        $cotization = Cotization::find($id);

        $amount = floatval($request->amount);
        $type = intval($request->payment_type_id);

        // validations
        if ($amount <= 0) {
            return Response::json(array('msg' => 'Importe inválido'), 500);
        }

        if ($type <= 0) {
            return Response::json(array('msg' => 'Tipo de pago inválido'), 500);
        }

        if ($amount > $cotization->balance) {
            return Response::json(array('msg' => 'Importe mayor al saldo'), 500);
        }

        // create Payment
        $payment = Payment::create([
            'payment_date' => date('Y-m-d H:i:s'),
            'amount' => $amount,
            'payment_type_id' => $type,
            'comments' => $request->comments
        ]);

        // create CotizationPayment
        CotizationPayment::create([
            'cotization_id' => $cotization->id,
            'payment_id' => $payment->id,
            'amount' => $amount
        ]);

        // mark cotization as paid
        if ($amount == $cotization->balance) {
            $cotization->status = 'P';
            $cotization->save();
        } 

        return $payment;
    }

    /**
     * Print cotization pdf
     *
     * @param  int  $id
     * @return Response
     */
    public function print_pdf($id, Request $request)
    {
        $order = Cotization::find($id);
        $pdf = new CotizationPdf($order);
        return Response::make($pdf->Output('I', 'cotizacion_'.$id.'.pdf'), 200, array('content-type' => 'application/pdf'));
    }
}
