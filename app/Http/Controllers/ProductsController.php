<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;
use App\Services\ProductHistoryPdf;

use App\Models\Product;
use App\Models\Vendor;

class ProductsController extends AppController
{
    protected $mainModel = 'App\Models\Product';

    // params needen for index
    protected $searchFields = ['description', 'code', 'sat_code'];
    protected $indexPaginate = 10;
    protected $indexJoins = [];
    protected $orderBy = ['field' => 'description', 'type' => 'ASC'];
    
    // params needer for show
    protected $showJoins = [];

    // params needed for store/update
    protected $defaultNulls = ['code'];
    protected $formRules = [
        'description'  => 'required',
        //'code' => 'nullable|unique:products,code,{{id}}',
        //'sat_code' => 'nullable|unique:products,sat_code,{{id}}',
    ];

    protected $allowDelete = true;


    /**
     * Display the specified resource.
     *
     * @param  string  $code
     * @return Response
     */
    public function search_code(Request $request)
    {
    	$code = $request->code;
    	$product = Product::where('code', $code)->where('active', 1)->first();
    	
    	if ($product) {
    		$response = array('success' => true, 'product' => $product);
    	} else {
    		$response = array('success' => false, 'msg' => 'No se encontro el producto');
    	}

    	return Response::json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $description
     * @return Response
     */
    public function search_description(Request $request)
    {
        $description = $request->description;
        $product = Product::where('description', 'like', $description.'%')->where('active', 1)->get();
        
        if ($product && count($product) > 0) {

            if (count($product) == 1) {
                $response = array('success' => true, 'product' => $product[0]);
            } else {
                $response = array('success' => true, 'product' => false);
            }

        } else {
            $response = array('success' => false, 'msg' => 'No se encontro el producto');
        }

        return Response::json($response);
    }

    /**
     * Get the price for a product
     *
     * @param  int  $id
     * @param  int  $vendor
     * @return Response
     */
    public function get_price(Request $request)
    {
        $product_id = $request->id;
        $vendor_id = $request->vendor;
        
        // get the vendor's price
        $price = \DB::table('vendor_prices AS vp')
            ->where('vendor_id', $vendor_id)
            ->where('product_id', $product_id)
            ->select('price')
            ->first();


        if (! $price) {
            $price = ['price' => 0];
        }

        return Response::json($price);
    }

    /**
     * Get the compare rpt for products
     *
     * @return Response
     */
    public function rpt_compare(Request $request)
    {
        $search = $request->search;
        $order = $request->order;

        $products = Product::where('active', 1);

        if ($search) {
            $products = $products->where(function ($q) use ($search) {
                $q->where('code', 'like', '%'.$search.'%')->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $order = ($order == 'C') ? 'code' : 'description';
        $products = $products->orderBy($order, 'ASC')->paginate(10);


        foreach ($products as $key => $product) {
            $this->columns = array();

            $details = \DB::table('vendor_prices AS vp')
                ->join('vendors AS ven', 'ven.id', '=', 'vp.vendor_id')
                ->where('vp.product_id', $product->id)
                ->select('vp.id', 'vp.price', 'vp.vendor_id', 'ven.name AS vendor_name', 
                         'contact', 'phone', 'mobile', 'credit_conditions')
                ->get();

            foreach ($details as $item) {
                $this->columns[] = $item;
            }
            $this->orderByPrice();

            $products[$key]['columns'] = $this->columns;
        }

        return Response::json($products);
    }

    /**
     * Print product history pdf
     *
     * @param  int  $product
     * @param  int  $vendor
     * @return Response
     */
    public function rpt_history($product_id, $vendor_id, Request $request)
    {
        $product = Product::find($product_id);
        $vendor = Vendor::find($vendor_id);

        $pdf = new ProductHistoryPdf($product, $vendor);
        return Response::make($pdf->Output('I', 'product_history.pdf'), 200, array('content-type' => 'application/pdf'));
    }

    private function orderByPrice()
    {
        $price = array();
        $result = array();

        foreach ($this->columns as $key => $item) {
            $price[$item->id] = $item->price;
        }

        asort($price);

        foreach ($price as $key => $item) {
            foreach ($this->columns as $column) {
                if ($column->id == $key) {
                    $result[] = $column;
                    break;
                }
            }
        }

        $this->columns = $result;
    }
}
