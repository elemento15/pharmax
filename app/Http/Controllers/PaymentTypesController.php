<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentTypesController extends AppController
{
    protected $mainModel = 'App\Models\PaymentType';

    // params needen for index
    protected $searchFields = ['id'];
    protected $indexPaginate = 10;
    protected $indexJoins = [];
    protected $orderBy = ['field' => 'id', 'type' => 'ASC'];

    // params needer for show
    protected $showJoins = [];
    
    // params needed for store/update
    protected $defaultNulls = [];
    protected $formRules = [];

    protected $allowDelete = false;
    protected $allowUpdate = false;
    protected $allowStore  = false;
    protected $except = [];

    protected $useTransactions = false;
}
