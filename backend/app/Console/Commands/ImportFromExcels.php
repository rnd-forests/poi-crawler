<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Cocur\Slugify\Slugify;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use App\Modules\Location\Models\LocationType;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

class ImportFromExcels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:import {folder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import locations from excel';

    protected $spreadsheet;

    protected $reader;
    protected $slugify;

    /**
     * Create a new command instance.
     *
     * @param Xlsx $reader
     * @param Slugify $slugify
     */
    public function __construct(Xlsx $reader, Slugify $slugify)
    {
        parent::__construct();

        $this->reader = $reader;
        $reader->setReadDataOnly(true);

        $this->slugify = $slugify;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $this->confirm('Do you wish to continue?')) {
            $this->info('Aborted');

            return;
        }

        $folder = $this->argument('folder');

        $files = array_diff(scandir($folder), ['..', '.']);

        foreach ($files as $file) {
            try {
                $this->spreadsheet = $this->reader->load($folder . '/' . $file);
            } catch (Exception $e) {
                $this->error('Cannot read file ' . $file);
                $this->error($e->getMessage());

                continue;
            }

            try {
                $sheet = $this->spreadsheet->getSheet(0);

                $worksheet = $sheet->toArray();
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
                $this->error('Cannot read worksheet 0 of file ' . $file);
                $this->error($e->getMessage());

                continue;
            }

//            0 => '_id',
//            1 => 'name',
//            2 => 'type',
//            3 => 'weight',
//            4 => 'formatted_address',
//            5 => 'description',
//            6 => 'delete',

            $rows = count($worksheet);

            for ($i = 1; $i < $rows; $i++) {
                try {
                    $type = trim($worksheet[$i][2]);

                    $type = LocationType::firstOrCreate(
                        ['name' => $type],
                        ['slug' => $this->slugify->slugify($type)]
                    );

                    $type = Arr::only($type->toArray(), ['_id', 'name', 'slug']);

                    $location = [
                        'name' => $worksheet[$i][1],
                        'type' => [$type],
                        'weight' => $worksheet[$i][3] ?: 1,
                        'formatted_address' => $worksheet[$i][4],
                        'description' => $worksheet[$i][5],
                    ];

                    // deleted location
                    if ($worksheet[$i][6]) {
                        $location['deleted_at'] = Carbon::now();
                    }

                    DB::table('locations')
                        ->where('_id', $worksheet[$i][0])
                        ->update($location);
                } catch (\Exception $e) {
                    $this->error('Cannot save location ' . $worksheet[$i][1] . ' at line ' . $i);
                    $this->error($e->getMessage());

                    continue;
                }
            }
        }


        $this->info('Done');
    }
}
