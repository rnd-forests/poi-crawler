<?php

namespace App\Modules\Home\Controllers;

use App\Modules\AdministrativeArea\Models\Province;
use App\Modules\AdministrativeArea\Models\Ward;
use App\Modules\Crawler\Models\FailedJob;
use App\Modules\Shared\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    public function index()
    {
        if (config('app.env') === 'production') {
            return $this->resNoContent('Develop by Hà Viết Tráng');
        }

        return now()->toDateTimeString();
    }
}
