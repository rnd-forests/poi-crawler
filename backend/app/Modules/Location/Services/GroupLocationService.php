<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 08/10/2018 15:21
 */

namespace App\Modules\Location\Services;

use Illuminate\Http\Request;

class GroupLocationService
{
    protected $query;

    protected $table = 'locations';

//    public function __construct()
//    {
//        $this->query = (new Location)->newQuery();
//    }

    public function searchByGroup(Request $request)
    {
        $longitude = (float)$request->input('longitude');
        $latitude = (float)$request->input('latitude');
        $language = $request->input('language', 'vi');

        $locations = [];

        if ($language !== 'vi') {
            \App::setLocale($language);
        }

        $schemas = $this->typesToGet();

        foreach ($schemas as $schema) {
            $query = $this->queryFactory($longitude, $latitude, $schema, $language);

//            $subLocations = $this->query->raw(function($collection) use ($query) {
//                return $collection->aggregate($query);
//            });
            $subLocations = \DB::table($this->table)->raw(function($collection) use ($query) {
                return $collection->aggregate($query);
            });

            $subLocations = $subLocations->toArray();

            if (!empty($subLocations)) {
//            if (!$subLocations->isEmpty()) {

                $subLocations = $this->transformSubLocations($subLocations, $language);

                array_push($locations, [
                    'category' => $schema['text'],
                    'slug' => $schema['slug'],
                    'places' => $subLocations,
                ]);
            }
        }

        return $locations;
    }

    /**
     * @param $subLocations array
     * @param $language string
     * @return array
     */
    protected function transformSubLocations($subLocations, $language)
    {
        return array_map(function ($location) use ($language) {
//            $data = [];
            $data = ['_id' => $location['_id']->__toString()];

            if (isset($location['avatar'])) {
                $data['avatar'] = $location['avatar'];
            }

            if (isset($location['locales'])) {
                // search translation
                foreach ($location['locales']->getArrayCopy() as $e) {
                    $e = $e->getArrayCopy();

                    if ($e['language'] === $language) {
                        $data['name'] = array_get($e, 'name', $location['name']);

                        break;
                    }

                    $data['name'] = $location['name'];
                }
            } else {
                $data['name'] = $location['name'];
            }

            $data['name'] = ucfirst($data['name']);

            return $data;
        }, $subLocations);
    }



    /**
     * @param $longitude
     * @param $latitude
     * @param $schema
     * @param $language
     * @return array
     */
    protected function queryFactory($longitude, $latitude, $schema, $language)
    {
        $query = [
            [
                '$geoNear' => [
                    'near' => ['coordinates' => [$longitude, $latitude], 'type' => 'Point'],
                    'distanceField' => 'distance',
                    'maxDistance' => $schema['max'], // meters
                    'minDistance' => $schema['min'],
                    'spherical' => true,
                ],
            ],
            ['$match' => ['type._id' => ['$in' => $schema['ids']]]],
//            ['$match' => ['name' => ['$regex' => new \MongoRegex("/{$schema['priority']['name']}/")]]],
            ['$sort' => ['weight' => -1]],
            ['$limit' => $schema['quantity']],
            ['$project' => [
                'name' => 1,
//                'distance' => 1,
//                'formatted_address' => 1,
//                'type' => 1,
                'avatar' => 1,
            ]]
        ];

        if ($language !== 'vi') {
            $query[4]['$project']['locales'] = 1;
        }

        return $query;
    }

    public function typesToGet()
    {
        return [
            [
                'text' => trans('messages.famous_places'),
                'slug' => 'famous_places',
                'ids' => [
                    '5c3bf9799dc6d6c50c5f2812', // Địa điểm nổi tiếng
//                    '5bc5b7255203175bdb644f73', // Thắng cảnh
//                    '5bda6def9dc6d6240b3f2da7', // Thăm quan & chụp ảnh
//                    '5bef64b69dc6d66ac76b183b', // Chụp hình cưới
//                    '5bebd68c9dc6d61d080762e7', // Khu nghỉ dưỡng
//                    '5bc85d1552031727e275aafa', // Du lịch sinh thái
//                    '5bef76519dc6d66ac76b186e', // Bảo tàng & Di tích
                ],
                'min' => 200,
                'max' => 10000,
                'priority' => [
//                    'type' => [
//                        '5bc5b7255203175bdb644f73', // Thắng cảnh
//                        '5bef76519dc6d66ac76b186e', // Bảo tàng & Di tích
//                    ],
                    'weight' => true,
                ],
                'quantity' => 10,
            ],
            [
                'text' => trans('messages.cuisine'),
                'slug' => 'cuisine',
                'ids' => [
                    '5c3973ea9dc6d6cac601fe82', // Ẩm Thực
//                    '5bc5b8d6520317657735fef5', // Địa điểm ăn uống
//                    '5bc5cb9e5203176db702ac1d', // Ăn vặt/vỉa hè
//                    '5bc5cc61520317657735ff0f', // Quán ăn
//                    '5bc5d16d5203177feb556aad', // Sang trọng
//                    '5bc6a6535203172c7f166da2', // Nhà hàng
////                    '5bc5da425203170c36502b59', // Khách sạn
//                    '5bc5dbc85203170c36502b5f', // Café/Dessert
//                    '5bc5ee6e5203170c36502b8a', // Buffet
//                    '5bc7347f5203174e65298d41', // Ăn chay
//                    '5bc73b7952031752dc2fe40d', // Tiệm bánh
//                    '5bc858fb52031727e275aae3', // Khu Ẩm Thực
//                    '5bc9a55852031741d01ff5ed', // Beer club
//                    '5bcf11059dc6d6740a3f4124', // Giao cơm văn phòng
//                    '5bd5322d9dc6d6740a3f439b', // Quán nhậu
                ],
                'min' => 200,
                'max' => 3000,
                'priority' => [
                    'weight' => true,
                ],
                'quantity' => 3,
            ],
            [
                'text' => trans('messages.shopping'),
                'slug' => 'shopping',
                'ids' => [
                    '5c3bf63e9dc6d6fc91731af2', // Mua sắm
//                    '5bc5b70f5203174940334ce4', // khu thương mại
//                    '5bc5b8ce5203175bdb644f78', // Cửa hàng tiện lợi
//                    '5bc9b37f5203175be46878e5', // Chợ
//                    '5bcf126b9dc6d6740a3f412a', // Shop/Cửa hàng
//                    '5bebccf79dc6d61d0742359e', // Shop hóa mỹ phẩm
//                    '5beef1629dc6d66ac66a088f', // Siêu thị
                ],
                'min' => 200,
                'max' => 5000,
                'priority' => [
//                    'type' => [
//                        '5bc5b8ce5203175bdb644f78', // Cửa hàng tiện lợi
//                        '5bc5b70f5203174940334ce4', // khu thương mại
//                        '5bc9b37f5203175be46878e5', // Chợ
//                    ],
                    'weight' => true,
                ],
                'quantity' => 3,
            ],
            [
                'text' => trans('messages.transportation'),
                'slug' => 'transportation',
                'ids' => [
                    '5bc5b8b9520317657735fef3', // Giao thông
                ],
                'min' => 200,
                'max' => 40000,
                'priority' => [
                    'name' => 'Sân bay',
                    'weight' => true,
                ],
                'quantity' => 3,
            ],
            [
                'text' => trans('messages.entertainment'),
                'slug' => 'entertainment',
                'ids' => [
                    '5bc5b8c65203174ad86eabc8', // Giải trí
//                    '5bc5df435203170c36502b6e', // Bar/Pub
//                    '5bcb236e5203172e070b7461', // Karaoke
//                    '5bda6def9dc6d6240b3f2da9', // Khu chơi Game
//                    '5bda6def9dc6d6240b3f2da8', // Sân khấu
//                    '5bee9b259dc6d66ac66a0771', // Công viên vui chơi
//                    '5bd9a0fb9dc6d6240b3f2d55', // Billiards
//                    '5bd7c2d09dc6d6240c7a5ac9', // Thể dục thẩm mỹ
                ],
                'min' => 200,
                'max' => 5000,
                'priority' => [
//                    'type' => [
//                        '5bc5b8c65203174ad86eabc8', // Giải trí
//                    ],
                    'weight' => true,
                ],
                'quantity' => 3,
            ],
            [
                'text' => trans('messages.medical'),
                'slug' => 'medical',
                'ids' => [
                    '5c3bf9839dc6d6c5056883f2', // Y tế
//                    '5bc5b7055203175bdb644f72', // Bệnh viện
//                    '5befc3819dc6d66ac76b19a2', // Phòng khám
//                    '5befc3819dc6d66ac76b19a3', // Nhà thuốc
                ],
                'min' => 200,
                'max' => 6000,
                'priority' => [
                    'weight' => true,
                ],
                'quantity' => 3,
            ],
            [
                'text' => trans('messages.sports'),
                'slug' => 'sports',
                'ids' => [
                    '5bf84e889dc6d6050737a6ab', // Thể dục thể thao
                ],
                'min' => 200,
                'max' => 3000,
                'priority' => [
                    'weight' => true,
                ],
                'quantity' => 3,
            ],
            [
                'text' => trans('messages.urban_area'),
                'slug' => 'urban_area',
                'ids' => [
                    '5bc5b7165203174ad86eabc7', // Khu đô thị
                ],
                'min' => 200,
                'max' => 4000,
                'priority' => [
                    'weight' => true,
                ],
                'quantity' => 3,
            ],
            [
                'text' => trans('messages.head_office'),
                'slug' => 'head_office',
                'ids' => [
//                    '5bc5b6f85203174ad86eabc6', // Cơ quan hành chính
                    '5c627ee3520317068c2bb7e2', // Cơ quan ban ngành
                ],
                'min' => 200,
                'max' => 4000,
                'priority' => [
                    'weight' => true,
                ],
                'quantity' => 3,
            ],
//            [
//                'text' => trans('labels.room_detail.places_around.others'),
//                'ids' => [
//                    '5bc9b3855203175be468823c', // Bưu Điện
//                    '5bf1428b9dc6d66ac76b1d4b', // Dịch Vụ Vận Chuyển Đồ Đạc
//                    '5bd4313a9dc6d674083eaf03', // Nhà sách & Thư viện
//                    '5bf316bd9dc6d66ac66a13ef', // Giặt ủi
//                ],
//                'priority' => [
//                    'type' => [
//                        '5bf316bd9dc6d66ac66a13ef', // Giặt ủi
//                    ],
//                    'distance' => true,
//                ],
//                'quantity' => 3,
//            ],

//            '5bc72a7d5203174e65298d04', // Tiệc cưới/Hội nghị
//            '5bcd6e695203172e070b76a3', // Shop Online
//            '5bd7c2d09dc6d6240c7a5ac8', // Spa/Massage
//            '5bd99c489dc6d6240c7a5b3a', // Tiệc tận nơi
//            '5bd99c489dc6d6240c7a5b3b', // Tiệc tại gia
//            '5be6358e9dc6d61d0644cfb1', // Mua sắm Online
//            '5beecd289dc6d66ac76b1745', // Tàu du lịch
//            '5beece1f9dc6d66ac66a082c', // Homestay
//            '5befe1e99dc6d66ac76b1a3f', // Nhà nghỉ
//            '5bf1428b9dc6d66ac76b1d4a', // Vận tải
        ];
    }
}
