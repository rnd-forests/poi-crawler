<?php

namespace App\Modules\AdministrativeArea\Controllers;

use App\Modules\AdministrativeArea\Models\Ward;
use App\Modules\Shared\Controllers\Controller;

class DistrictController extends Controller
{
    public function wards($id)
    {
        return $this->resSuccess(
            Ward::where('district_id', '=', $id)->get(['_id', 'name'])
        );
    }
}
