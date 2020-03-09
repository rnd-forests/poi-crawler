<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 08/10/2018 15:21
 */

namespace App\Modules\Location\Services;

use App\Exceptions\ApiException;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Modules\AdministrativeArea\Models\District;
use App\Modules\AdministrativeArea\Models\Province;
use App\Modules\AdministrativeArea\Models\Ward;
use App\Modules\Location\Helper\LocationHelper;
use App\Modules\Location\Models\AdditionLocationInfo;
use App\Modules\Location\Models\Location;
use App\Modules\Location\Resources\LocationCollection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Arr;

class LocationService
{
    protected $query;

    protected $table = 'locations';

    protected $creationFields = [
        'name',
        'slug',
        'description',
        'avatar',
        'longitude',
        'latitude',
        'weight',
        'source',
        'source_url',
    ];

    protected $translationFields = [
        'name',
        'language',
        'description',
        'keywords',
        'price_range',
        'formatted_address'
    ];

    public function __construct()
    {
        $this->query = (new Location)->newQuery();
    }

    public function getLocations(Request $request)
    {
        $data = $request->all();

        $fields = $request->fields;

        if ($fields) {
            $fields = explode(',', $fields);
        } else {
            $fields = [
                'name',
//                'slug',
                'description',
                'avatar',
//                'categories',
//                'keywords',
                'weight',
                'source',
                'source_url',
//                'review',
                'formatted_address',
                'locales',
                'area',
                'geometry',
                'type',
//                'price_range',
                'updated_at',
                'edited_by',
            ];
        }

        $this->query->select($fields);

        // search by location name
        if (isset($data['q'])) {
            $this->query->where('name', 'like', "%{$data['q']}%");
        }

        // search by editor
        if (isset($data['user_id'])) {
            $this->query->where('edited_by', 'elemMatch', ['_id' => $data['user_id']]);

            $createdAt = $request->created_at;

            if ($createdAt) {
                $date = new Carbon($createdAt);

                $this->query->where('created_at', '>', $date->copy()->subDay()->endOfDay());
                $this->query->where('created_at', '<', $date->addDay());
            }
        }

        // search by province id
        if (isset($data['province_id'])) {
            $this->query->whereRaw(['area.province._id' => ['$eq' => $data['province_id']]]);

            // search by district id
            if (isset($data['district_id'])) {
                $this->query->whereRaw(['area.district._id' => ['$eq' => $data['district_id']]]);
            }
        }

        // search by weight
        if (isset($data['weight'])) {
            $this->query->where('weight', '=', (int)$data['weight']);
//            $this->query->whereRaw(['weight' => ['$eq' => (int) $data['weight']]]);
        }

        // search by types
        if (isset($data['type_ids'])) {
            $types = explode(',', $data['type_ids']);

            foreach ($types as $e) {
                $this->query->where('type', 'elemMatch', ['_id' => $e]);
            }
        }

        $this->query->orderBy($request->input('sort_field', '_id'), $request->input('sort_type', 'desc'));

        return new LocationCollection($this->query->paginate((int) $request->input('per_page', 15)));
    }

    public function search(Request $request)
    {
        $limit = (int)$request->input('per_page', 10);
        $offset = (int)$request->input('page', 1) - 1;
        $offset *= $limit;

        list($longitude, $latitude) = $this->getCoordinateFromRequest($request);

        $maxDistance = (int)$request->input('max_distance', 1000);
        $minDistance = (int)$request->input('min_distance', 0);

        $rawQuery = [
            [
                '$geoNear' =>
                    [
                        'near' => ['coordinates' => [$longitude, $latitude], 'type' => 'Point'],
                        'distanceField' => 'distance',
                        '$maxDistance' => $maxDistance,
                        '$minDistance' => $minDistance,
                        'spherical' => true,
                    ],
            ],
            ['$limit' => $limit],
            ['$skip' => $offset],
            ['$project' => [
                'name' => 1,
                'description' => 1,
                'distance' => 1,
                'formatted_address' => 1,
                'type' => 1,
                'weight' => 1,
            ]]
        ];

        $type_ids = $request->input('type_ids');

        if ($type_ids) {
            $type_ids = explode(',', $type_ids);

            $rawQuery[] = ['$match' => ['type._id' => ['$in' => $type_ids]]];
        }

        $data = $this->query->raw(function($collection) use ($rawQuery) {
            return $collection->aggregate($rawQuery);
        });

        return $data->toArray();
    }

    protected function replaceTranslationData($data, $language)
    {
        return collect($data)->map(function ($e) use ($language) {
            if (isset($e['locales']) && ! empty($e['locales'])) {
                $translation = collect($e['locales'])->first(function ($v) use ($language) {
                    return $v['language'] === $language;
                });

                if ($translation) {
                    $e->name = $translation->name;

                    unset($e['locales']);
                }
            } else {
                $e->name = internationalize_string($e->name);
            }

            return $e;
        });
    }

    /**
     * @param Collection | array $result
     * @param Collection $data
     * @param $group
     * @param int $max
     * @return Collection | array
     */
    protected function fillDataForGroup($result, $data, $group, $max = 3)
    {
        if (isset($result[$group['text']])) {
            $result[$group['text']] = array_merge($result[$group['text']], $data->slice(0, $max - count($result[$group['text']]))->values()->toArray());
        } else {
            $result[$group['text']] = $data->slice(0, $max)->toArray();
        }

        return $result;
    }

    /**
     * @param Collection $data
     * @param $group
     * @return Collection | array
     */
    protected function filterDataForGroup($data, $group)
    {
        return $data->filter(function ($d) use ($group) {
            if ($d->type) {
                return count(array_intersect(array_map(function ($i) {
                    return $i->_id;
                }, $d->type->jsonSerialize()), $group['ids']));
            }

            return false;
        });
    }

    /**
     * @param Collection $data
     * @param array $group
     * @return Collection | array
     */
    protected function getTopDataForGroup($data, $group)
    {
        return $data->slice(0, array_get($group, 'quantity', 3));
    }

    protected function isGroupFilled($result, $group)
    {
        return isset($result[$group['text']]) && count($result[$group['text']]) >= array_get($group, 'quantity', 3);
    }

    protected function insertDataToGroup($result, $groups, $data, $maxItems)
    {
        if (isset($result[$groups['text']])) {
//                            $r->distance = StringHelper::prettyDistance($r->distance);

            // if group length less than $maxItemPerGroup
            if (count($result[$groups['text']]) < $maxItems) {
                array_push($result[$groups['text']], $data->toArray());
            }
        } else {
//                            $r->distance = StringHelper::prettyDistance($r->distance);

            $result[$groups['text']] = [$data->toArray()];
        }

        return $result;
    }

    protected function isSetAndNotEmpty($arr, $key)
    {
        return isset($arr[$key]) && !empty($arr[$key]);
    }

    public function createLocation(Request $request)
    {
        if ($request->map_info) {
            AdditionLocationInfo::create(['google_map' => $request->map_info]);
        }

        $data = $request->except('map_info');

//        $data['edited_by'] = [Common::getEditedUser(auth()->user())];

        return Location::create($data);
    }

    /**
     * @param $id
     * @param $request
     * @return
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function updateLocation($id, Request $request)
    {
//        $location = Location::where('_id', '=', $id)
//            ->orWhere('slug', '=', $request->slug)
//            ->get();
        $location = Location::find($id);

//        $quantity = $location->count();

//        if (!$quantity) {
        if (!$location) {
            throw new NotFoundException();
        }

        // unique slug
//        if ($quantity === 2) {
//            throw new ValidationException(null, __('validation.unique', ['attribute' => 'slug']));
//        }

//        $location = $location[0];

        $updateFields = [
            'name',
            'slug',
            'avatar',
            'description',
            'keywords',
            'geometry',
            'weight',
            'area',
            'formatted_address',
            'price_range',
            'type',
        ];

        $location->update($request->all($updateFields));

        return $location;
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundException
     */
    public function firstOrFail($id, $fields = ['*'])
    {
        $data = Location::where('_id', $id)->first($fields);

        if ($data) return $data;

        throw new NotFoundException();
    }

    /**
     * @param $id
     * @throws NotFoundException
     */
    public function delete($id)
    {
        $location = Location::where('_id', '=', $id)
            ->first(['_id']);

        if (!$location) {
            throw new NotFoundException();
        }

        // editor will be auto detect
        $location->deleted_at = new UTCDateTime();

        $location->save();
    }

    public function deleteMany($ids)
    {
        $now = new UTCDateTime();

        return DB::table('locations')->whereIn('_id', $ids)
            ->update(['deleted_at' => $now]);
    }

    /**
     * @param $id
     * @param string $language
     * @return null
     * @throws NotFoundException
     */
    public function getTranslation($id, $language = 'en')
    {
        $data = $this->firstOrFail($id);

        $data = $data->locales;

        if (!$data) return null;

        foreach ($data as $e) {
            if (isset($e['language']) && $e['language'] === $language) {
                return $e;
            }
        }

        return null;
    }

    /**
     * @param $id
     * @param Request|mixed $request
     * @return int
     * @throws NotFoundException|BadRequestException
     */
    public function createOrUpdateTranslation($id, $request)
    {
        $lang = $request->language;

        $this->isAllowLanguage($lang);

        $data = $request->only($this->translationFields);

        if ($this->getTranslation($id, $lang)) {
            return $this->updateTranslation($id, $lang, $data);
        }

        return $this->createTranslation($id, $data);
    }

    public function createTranslation($id, $data)
    {
        return \DB::table(Location::getTableName())->where('_id', $id)
            ->update(['$push' => ['locales' => $data]]);
    }

    public function updateTranslation($id, $language, $data)
    {
        return $this->query
            ->where('_id', $id)
            ->where('locales.language', $language)
            ->update(['locales.$' => $data]);
    }

    /**
     * @param array $options
     * @return mixed
     * @throws ApiException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function detect(Request $request)
    {
        $data = $request->all();

        $options = Arr::only($data, ['address', 'latlng']);


        $results = $this->getDataFromGoogleMap($options);

        $this->checkGoogleMapStatus($results);

//        $results = $results['results'];
        $results = $results['results'][0]; // take first result

        $results['area'] = $this->findArea($results, $data);

        $coordinates = $results['geometry']['location'];

        $results['near_locations'] = $this->findNearLocation($coordinates, $data);

        return $results;
    }

    protected function findArea($results, $data)
    {
        $address_components = $results['address_components'];
        $formattedAddress = $results['formatted_address'];

        $province = $this->findAdministrativeArea('administrative_area_level_1', $address_components);
        $district = $this->findAdministrativeArea('administrative_area_level_2', $address_components);
        $ward = $this->findAdministrativeArea('administrative_area_level_3', $address_components);

        // TODO: find by $text index instead of like
        $province = $this->findProvince($province);
        $district = $this->findDistrict($district ? $district : $formattedAddress, $province['_id']);

        if ($district) {
            if (! $ward) {
                $address = array_get($data, 'address');

                if ($address) {
                    // weight 3 for address, 1 for formatted_address
                    $searchTerm = implode(' ', array_fill(0, 3, $address)) . " {$formattedAddress}";
                } else {
                    $searchTerm = $formattedAddress;
                }
            }

            $ward = $this->findWard($searchTerm, $district['_id']);
        }

        return [
            'province' => $province,
            'district' => $district,
            'ward' => $ward,
        ];
    }

    protected function findNearLocation($coordinates, $data)
    {
        $limit = array_get($data, 'limit', 10);
        $maxDistance = array_get($data, 'max_distance', 1000);
        $minDistance = array_get($data, 'min_distance', 0);

        $nearLocations = LocationHelper::queryNear(
            $this->query,
            [$coordinates['lng'], $coordinates['lat']],
            $maxDistance,
            $minDistance
        )
            ->limit($limit)
            ->get(['_id', 'name', 'formatted_address']);

        return $nearLocations ? $nearLocations->toArray() : [];
    }

    /**
     * @param $query
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getDataFromGoogleMap($options)
    {
        $client = new Client();

        $defaultOptions = [
//            'key' => config('crawler.GOOGLE_MAP_KEY'),
            'key' => config('services.google.maps.GOOGLE_MAP_KEY'),
            'language' => config('crawler.default_language'),
        ];

        $results = $client->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
            'query' => array_merge($options, $defaultOptions),
        ]);

        return json_decode($results->getBody()->getContents(), true);
    }

    /**
     * @param $results
     * @throws ApiException
     * @throws BadRequestException
     */
    protected function checkGoogleMapStatus($results)
    {
        $status = $results['status'];

        if ($status !== 'OK') {
            if ($status === 'ZERO_RESULTS') {
                throw new BadRequestException(null, 'Không tìm thấy địa chỉ');
            }

            throw new ApiException(null, $results['error_message']);
        }
    }

    protected function findAdministrativeArea($needle, $components)
    {
        foreach ($components as $h) {
            if (in_array($needle, $h['types'])) {
                return $h['short_name'];
            }
        }

        return false;
    }

    protected function findProvince($name)
    {
        if ($name) {
            $foundedProvince = Province::findNameLike($name);
//            $foundedProvince = $this->query->nameLike($name)->first(['_id', 'name', 'slug']);

            if ($foundedProvince) {
                return $foundedProvince->toArray();
            }
        }

        return null;
    }

    protected function findDistrict($name, $provinceId = null)
    {
//        $query = (new District)->newQuery();
        $query = District::fullText($name);

        if ($provinceId) {
            $query->where('province_id', '=', $provinceId);
        }

        $found = $query->first(['_id', 'name', 'slug']);

        return $found ? $found->toArray() : null;
    }

    protected function findWard($name, $districtId = null)
    {
//        $query = (new Ward)->newQuery();
        $query = Ward::fullText($name);

        if ($districtId) {
            $query->where('district_id', '=', $districtId);
        }

        $found = $query->first(['_id', 'name', 'slug']);
//        $found = $query->limit(5)->get(['_id', 'name', 'slug']);

        return $found ? $found->toArray() : null;
    }

    protected function scopeId($id)
    {
        return $this->query->where('_id', '=', $id);
    }

    protected function getCoordinateFromRequest(Request $request)
    {
        $coordinates = $request->input('coordinates');

        return array_map(function ($e) {
            return (float)$e;
        }, explode(',', $coordinates));
    }

    /**
     * @param $language
     * @throws BadRequestException
     */
    protected function isAllowLanguage($language)
    {
        if ($language === 'vi') {
            throw new BadRequestException('Bạn không được thêm ngôn ngữ Tiếng Việt');
        }
    }
}
