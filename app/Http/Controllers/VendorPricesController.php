<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;

use App\Models\VendorPrice;

class VendorPricesController extends AppController
{
    protected $mainModel = 'App\Models\VendorPrice';

    // params needen for index
    protected $searchFields = [];
    protected $indexPaginate = 10;
    protected $indexJoins = ['product'];
    protected $orderBy = ['field' => 'id', 'type' => 'DESC'];
    
    // params needer for show
    protected $showJoins = [];

    // params needed for store/update
    protected $defaultNulls = [];
    protected $formRules = [
        'price'  => 'required'
    ];

    protected $allowDelete = true;
    protected $allowStore = true;
    protected $allowUpdate = true;
    protected $except = [];

    protected $useTransactions = false;


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $filters = $request->filters;
        $order = $request->order;

        foreach ($filters as $key => $filter) {
            switch ($filter['field']) {
                case 'vendor_id' : $vendor_id = $filter['value']; break;
            }
        }

        $order_by = ($order == 'C') ? 'code' : 'description';

        $data = \DB::table('vendor_prices AS vp')
            ->join('products AS pro', 'pro.id', '=', 'vp.product_id')
            ->where('vp.vendor_id', $vendor_id)
            ->where(function ($query) use ($search) {
                $query->where('pro.code', 'like', $search.'%')->orWhere('pro.description', 'like', $search.'%');
            })
            ->orderBy($order_by, 'ASC')
            ->select('vp.id','pro.id AS product_id','pro.code','pro.description','vp.price')
            ->paginate($this->indexPaginate);

        return Response::json($data);
    }
}
