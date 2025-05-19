<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\ListOption;


class ListOptionController extends Controller
{
    public function list_options()
    {
        // $data['options'] = ListOption::where('list_type','Type')->get();
        $data['options'] = ListOption::get();
        return $this->sendResponse($data, 'available options');
    }
}
