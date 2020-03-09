<?php

namespace App\Modules\AdministrativeArea\Controllers;

use App\Modules\AdministrativeArea\Models\District;
use App\Modules\AdministrativeArea\Models\Province;
use App\Modules\Shared\Controllers\Controller;

class ProvinceController extends Controller
{
    public function index()
    {
        return $this->resSuccess(Province::all(['_id', 'name'])->toArray());
    }

    public function districts($id)
    {
        return $this->resSuccess(
            District::where('province_id', '=', $id)->get(['_id', 'name'])
        );
    }
}
