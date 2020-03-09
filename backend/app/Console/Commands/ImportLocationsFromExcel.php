<?php

namespace App\Console\Commands;

use App\Modules\AdministrativeArea\Models\District;
use App\Modules\AdministrativeArea\Models\Province;
use App\Modules\AdministrativeArea\Models\Ward;
use App\Modules\Location\Models\Location;
use App\Modules\Location\Models\LocationType;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\UTCDateTime;
//use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Cocur\Slugify\Slugify;

class ImportLocationsFromExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:import-from-excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import locations from excel';

    protected $typeFields = ['_id', 'name', 'slug'];
    protected $areaFields = ['_id', 'name'];

    protected $spreadsheet;

    protected $reader;
    protected $slugify;

    /**
     * Create a new command instance.
     *
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @return void
     */
//    public function __construct(Xlsx $reader, Slugify $slugify)
//    {
//        parent::__construct();
//
////        $this->spreadsheet = $spreadsheet;
////        $this->spreadsheet = $reader->load($this->path);
//        $this->reader = $reader;
//        $reader->setReadDataOnly(true);
//
//        $this->slugify = $slugify;
//    }

    /**
     * Execute the console command.
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function handle()
    {
        if (! $this->confirm('Do you wish to continue?')) {
            $this->info('Aborted');
            return;
        }

        $path = $this->ask('Enter file path');

        $this->spreadsheet = $this->reader->load($path);

//        $sheetData = $this->spreadsheet->getSheet(2)->toArray();
//        dd($sheetData[0], $sheetData[1], $sheetData[2]);

        $sheets = $this->spreadsheet->getAllSheets();
//        dd(count($sheets));

        foreach ($sheets as $data) {
//        for ($i = 0; $i < 5; $i++) {
//            $data = $this->spreadsheet->getSheet($i);
            $userName = $data->getTitle();
//            $data = $this->spreadsheet->getSheet($i);

            $data = $data->toArray();
//            unset($data[0]);

            $failedRows = [];
            $dataToInsert = [];

            $preProvince = '';
            $provinceDoc = null;
            $preProvinceDoc = null;

            $preDistrict = '';
            $preDistrictDoc = null;
            $districtDoc = null;

            $preWard = '';
            $preWardDoc = null;
            $wardDoc = null;

            $preType = '';
            $preTypeDoc = null;

            // read from row 2
            for ($i = 1; $i < count($data); $i++) {
                $e = $data[$i];
//            foreach ($data as $e) {
//            foreach ($data->getRowIterator(2) as $e) {
//                $cellIterator = $e->getCellIterator('A', 'H');
//
//                foreach ($cellIterator as $cell) {
//                    dump($cell->getValue());
//                }
//
//                dd($e);

                $name = trim($e[2]);
                $province = trim($e[3]);
                $district = trim($e[4]);
                $type = trim($e[5]);
                $weight = (int) $e[7];
                $coordinates = $e[6];
                $slug = $this->slugify->slugify($name);
//                dump($name);

                $doc = array_merge(compact('name', 'province', 'district', 'type', 'weight', $coordinates), ['user' => $userName]);

                if (!$name ||
                    !$province ||
                    !$district ||
                    !$type ||
                    !$coordinates ||
                    count(explode(',', $coordinates)) !== 2
                ) {
                    array_push($failedRows, array_merge($doc, ['reason' => 'Invalid data']));
                    continue;
                }

                $slugExisted = false;

                foreach ($dataToInsert as $e) {
                    if ($e['slug'] === $slug) $slugExisted = true;
                }

                if ($slugExisted || DB::table('locations')->where('slug', '=', $slug)->exists()) {
//                    array_push($failedRows, $doc);
//                    continue;
                    $slug = $this->slugify->slugify("${name} ${coordinates}");
                }

                if ($province === $preProvince) {
                    $provinceDoc = $preProvinceDoc;
                } else {
                    $preProvince = $province;

                    $provinceDoc = Province::fullText($province)->first();

                    if (!$provinceDoc) {
                        array_push($failedRows, array_merge($doc, ['reason' => 'Province not found']));
                        continue;
                    }

                    $preProvinceDoc = $provinceDoc;
                }
//                dd($provinceDoc->toArray());

                if ($district === $preDistrict) {
                    $districtDoc = $preDistrictDoc;
                } else {
                    $preDistrict = $district;

                    $districtDoc = District::where('province_id', '=', $provinceDoc->_id)
                        ->fullText($district)
                        ->first();

                    if (!$districtDoc) {
                        // CTV nhầm giữa xã và huyện
                        if ($district === $preWard) {
                            $wardDoc = $preWardDoc;
                        } else {
                            $preWard = $district;

                            $wardDoc = Ward::fullText($district)->first();

                            if (!$wardDoc) {
                                array_push($failedRows, array_merge($doc, ['reason' => 'District/Ward not found']));
                                continue;
                            }
                        }

                        $preWardDoc = $wardDoc;
                        $districtDoc = District::where('_id', '=', $wardDoc->district_id)->first();
//                        array_push($failedRows, $doc);
//                        continue;
                    }

                    $preDistrictDoc = $districtDoc;
                }
//                dd($districtDoc->toArray());

                if ($type === $preType) {
                    $typeDoc = $preTypeDoc;
                } else {
                    $preType = $type;

                    $typeDoc = LocationType::fullText($type)->first();

                    if (!$typeDoc) {
//                        array_push($failedRows, array_merge(compact('name', 'province', 'district', 'type', 'weight'), ['sheet' => $i]));
//                        continue;
                        $typeDoc = LocationType::create(['name' => $type, 'slug' => $this->slugify->slugify($type)]);
                    }

                    $typeDoc = Arr::only($typeDoc->toArray(), $this->typeFields);

                    $preTypeDoc = $typeDoc;
                }

                list($lat, $lng) = explode(",", $coordinates);

                $formatted_address = "{$name}, {$district}, {$province}";

                $now = new UTCDateTime();

                $area = [
                    'province' => Arr::only($provinceDoc->toArray(), $this->areaFields),
                    'district' => Arr::only($districtDoc->toArray(), $this->areaFields),
                ];

                if ($wardDoc) {
                    $area['ward'] = Arr::only($wardDoc->toArray(), $this->areaFields);
                }

                $docToInsert = array_merge(
                    compact('name', 'slug', 'formatted_address', 'weight', 'area'),
                    [
                        'type' => [$typeDoc],
                        'geometry' => Location::getGeometryFormat([$lng, $lat]),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );

                array_push($dataToInsert, $docToInsert);

                try {
                    DB::table('locations')->insert($docToInsert);
                } catch (\Throwable $e) {
                    $this->error($e->getMessage());
                }
            }

//            DB::table('locations')->insert($dataToInsert);
            if (!empty($failedRows)) {
                DB::table('locations_import_failed')->insert($failedRows);
            }

            $this->info("{$userName} has " . count($dataToInsert). " rows inserted");
            $this->info("{$userName} has " . count($failedRows). " rows failed");
        }

        $this->info('Finish');
    }
}
