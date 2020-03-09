<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 08/10/2018 15:21
 */

namespace App\Modules\Location\Services;


use App\Exceptions\NotFoundException;
use App\Modules\Location\Models\LocationType;
use Illuminate\Http\Request;

class LocationTypeService
{
    protected $query;

    public function __construct()
    {
        $this->query = (new LocationType)->newQuery();
    }

    /**
     * @param $id
     * @param array $fields
     * @return mixed
     * @throws NotFoundException
     */
    public function findFirstOrFail($id, $fields = ['*'])
    {
//        $data =  LocationType::where('_id', '=', $id)->first($fields);
        $data =  $this->query->where('_id', '=', $id)->first($fields);

        if ($data) return $data;

        throw new NotFoundException();
    }

    public function getTranslation($id, $language = 'en')
    {
//        $data = $this->findFirstOrFail($id, ["locales"]);
//        $data = $this->scopeId($id)
//            ->locale($language)
////            ->where('locales.language', $language)
////            ->project(['locales' => ['$slice' => 1]])
//            ->get(['locales']);
        $data = $this->getTranslations($id);

        if ($data->isEmpty()) return null;

        $data = $data->first()->locales;

        if (!$data) return null;

        foreach ($data as $e) {
            if ($e['language'] === $language) {
                return $e;
            }
        }

        return null;
//        return $data->first()->locales[0];
    }

    public function getTranslations($id)
    {
        return $this->scopeId($id)
//            ->where('locales.language', $language)
//            ->project(['locales' => ['$slice' => 1]])
            ->get(['locales']);
    }

    public function createOrUpdateTranslation($id, Request $request)
    {
        if ($this->getTranslation($id, $request->language)) {
            return $this->updateTranslation($id, $request);
        }

        return $this->createTranslation($id, $request);
    }

    public function createTranslation($id, Request $request)
    {
        // why this won't work?
//        return $this->scopeId($id)
//            ->push('locales', $request->all());
        return \DB::table('location_types')->where('_id', '=', $id)
            ->update(['$push' => ['locales' => $request->all()]]);
    }

    public function updateTranslation($id, Request $request)
    {
        return $this->scopeId($id)
            ->locale($request->language)
            ->update(['locales.$' => $request->all()]);
    }

    protected function scopeId($id)
    {
        return $this->query->where('_id', '=', $id);
    }
}
