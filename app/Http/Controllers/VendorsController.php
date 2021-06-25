<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Vendor;

class VendorsController extends AppController
{
    protected $mainModel = 'App\Models\Vendor';

    // params needen for index
    protected $searchFields = ['name', 'rfc', 'contact', 'email'];
    protected $indexPaginate = 10;
    protected $indexJoins = [];
    protected $orderBy = ['field' => 'name', 'type' => 'ASC'];
    
    // params needer for show
    protected $showJoins = [];

    // params needed for store/update
    protected $saveFields = ['name','rfc','contact','phone','mobile','email','credit_conditions','address','comments'];
    //protected $storeFields = [];
    //protected $updateFields = [];

    protected $defaultNulls = ['rfc'];
    protected $formRules = [
        'name'  => 'required|min:5',
        'email' => 'email|nullable',
        'rfc' => 'nullable|unique:vendors,rfc,{{id}}|min:12|max:13',
    ];

    protected $allowDelete = true;
    protected $allowUpdate = true;
    protected $allowStore  = true;
    protected $except = [];

    protected $useTransactions = false;
}
