<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Payment;

class PaymentsController extends AppController
{
    protected $mainModel = 'App\Models\Payment';

    // params needen for index
    protected $searchFields = ['id'];
    protected $indexPaginate = 10;
    protected $indexJoins = [];
    protected $orderBy = ['field' => 'payment_date', 'type' => 'DESC'];

    // params needer for show
    protected $showJoins = ['type'];
    
    // params needed for store/update
    protected $defaultNulls = [];
    protected $formRules = [];

    protected $allowDelete = false;


    /**
     * Cancel payment
     *
     * @param  int  $id
     * @return Response
     */
    public function cancel($id)
    {
        $record = Payment::find($id);

        if (! $record) {
            return Response::json(array('msg' => 'Registro no encontrado'), 500);
        }

        if (! $record->active) {
            return Response::json(array('msg' => 'Abono Cancelado'), 500);
        }

        $record->active = 0;
        $record->cancel_date = date('Y-m-d H:i:s');

        if ($record->save()) {
            // set status "N" to purchase orders or cotization

            foreach ($record->purchase_payments as $item) {
                $item->purchase_order->status = 'N';
                $item->purchase_order->save();
            }

            foreach ($record->cotization_payments as $item) {
                $item->cotization->status = 'N';
                $item->cotization->save();
            }

            return Response::json($record);
        } else {
            return Response::json(array('msg' => 'Error al cancelar'), 500);
        }
    }
}
