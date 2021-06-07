<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomersController extends AppController
{
    protected $mainModel = 'App\Models\Customer';

    // params needen for index
    protected $searchFields = ['name', 'rfc', 'contact', 'email'];
    protected $indexPaginate = 10;
    protected $indexJoins = [];
    protected $orderBy = ['field' => 'name', 'type' => 'ASC'];
    
    // params needer for show
    protected $showJoins = [];

    // params needed for store/update
    protected $defaultNulls = ['rfc'];
    protected $formRules = [
        'name'  => 'required',
        'email' => 'email|nullable',
        'rfc' => 'required|unique:customers,rfc,{{id}}|min:12|max:13',

    ];

    protected $allowDelete = true;
}
