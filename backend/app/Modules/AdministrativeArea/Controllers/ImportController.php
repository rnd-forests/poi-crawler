<?php

namespace App\Modules\AdministrativeArea\Controllers;

//use Illuminate\Support\Facades\DB;
use App\Modules\Shared\Controllers\Controller;

class ImportController extends Controller
{
    public function importProvinces()
    {
////        $provinces = DB::connection('mysql')->table('provinces')->get();
//        $provinces = DB::connection('mysql')->table('devvn_tinhthanhpho')->get();
//
//        $provinces = $provinces->map(function ($e) {
//            return [
////                'original_id' => (string) $e->id,
//                'original_id' => (string) $e->matp,
//                'name' => $e->name,
//                'type' => $e->type,
//            ];
//        })->toArray();
//
//        DB::table('provinces')->insert($provinces);

//        $districts = DB::connection('mysql')->table('districts')->get();
//        $wards = DB::connection('mysql')->table('wards')->get();

        return $this->resSuccess(null, 'Import provinces successfully');
    }

    public function importDistricts()
    {
//        $provinces = DB::table('provinces')->get();
//
//        $districts = DB::connection('mysql')->table('devvn_quanhuyen')->get();
//
//        $districts = $districts->map(function ($e) use ($provinces) {
//            return [
////                'original_id' => (string) $e->id,
//                'original_id' => (string) $e->maqh,
//                'name' => $e->name,
//                'type' => $e->type,
//                'original_province_id' => (string) $e->matp,
//                'province_id' => $provinces->first(function ($i) use ($e) {
//                    return $i['original_id'] == $e->matp;
//                })['_id']->__toString(),
//            ];
//        })->toArray();
//
//        DB::table('districts')->insert($districts);

        return $this->resSuccess(null, 'Import districts successfully');
    }

    public function importWards()
    {
//        $districts = DB::table('districts')->get();
//
//        $wards = DB::connection('mysql')->table('devvn_xaphuongthitran')->get();
//        $wards = $wards->map(function ($e) use ($districts) {
//            return [
////                'original_id' => (string) $e->id,
//                'original_id' => (string) $e->xaid,
//                'name' => $e->name,
//                'type' => $e->type,
//                'original_district_id' => (string) $e->maqh,
//                'district_id' => $districts->first(function ($i) use ($e) {
//                    return $i['original_id'] == $e->maqh;
//                })['_id']->__toString(),
//            ];
//        })->toArray();
//
//        DB::table('wards')->insert($wards);

        return $this->resSuccess(null, 'Import wards successfully');
    }
}
