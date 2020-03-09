<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExportToExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export locations to excel';

    protected $header = [
        '_id',
        'name',
        'type',
        'weight',
        'formatted_address',
        'description',
        'delete',
    ];

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

        $provinces = DB::table('provinces')
            ->get(['_id', 'name'])
            ->map(function ($e) {
                $e['_id'] = $e['_id']->__toString();

                return $e;
            });

        foreach ($provinces as $province) {
            $data = DB::table('locations')
                ->whereRaw(['area.province._id' => ['$eq' => $province['_id']]])
                ->whereNull('deleted_at')
                ->get([
                    '_id',
                    'name',
                    'type',
                    'formatted_address',
                    'weight',
                    'description',
                ])
                ->map(function ($e) {
//            $e['_id'] = $e['_id']->__toString();
//            $e['type'] = $e['type'][0]['name'];

//            return $e;
                    return [
                        $e['_id']->__toString(),
                        $e['name'],
                        $e['type'][0]['name'],
                        array_get($e, 'weight', ''),
                        $e['formatted_address'],
                        array_get($e, 'description', ''),
                        '',
                    ];
                })->toArray();

            $spreadsheet = new Spreadsheet();

            try {
                $spreadsheet->setActiveSheetIndex(0);
                $sheet = $spreadsheet->getActiveSheet();

                $sheet->fromArray(array_merge([$this->header], $data), NULL, 'A1');

                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save("{$province['name']}.xlsx");
            } catch (\Exception $e) {
                $this->error('Write to file fail', $e->getMessage());
            }
        }

        $this->info('Done');
    }
}
