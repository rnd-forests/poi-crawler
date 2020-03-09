<?php

namespace App\Console\Commands;

use App\Modules\Location\Models\Location;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ChangeLocationTypeToArray extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:change-type-to-array';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change type from object to value';

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

//        $types = DB::table('locations')->whereRaw(['type' => ['$type' => ['object']]])->get(['type']);
//        $types = DB::table('locations')->get(['type']);
//        $types = Location::withTrashed()->where('type', 'type', 'object')->get(['type']);
        $types = Location::withTrashed()->get(['_id', 'type']);

        if (! $this->confirm($types->count() . ' records will be effected, are you sure?')) {
            $this->info('Aborted');
            return;
        }

        $types->each(function ($e) {
//            DB::table('locations')->where('_id', '=', $e['_id']->__toString())->update(['type' => [$e['type']]]);
            if (isset($e->type['_id'])) {
                DB::table('locations')->where('_id', '=', $e->_id)->update(['type' => [$e->type]]);
            }
        });

        $this->info('Success');
    }
}
