<?php

namespace App\Console\Commands;

use App\Modules\Location\Models\Location;
use App\Modules\Location\Models\LocationType;
use Illuminate\Console\Command;
use App\Modules\Location\Services\LocationService;

class MergeLocationType extends Command
{
    protected $service;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location-type:merge {from} {to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge locations from a type to another';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(LocationService $service)
    {
        $this->service = $service;

        parent::__construct();
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

        $from = $this->argument('from');

        $from = LocationType::find($from, ['_id', 'name', 'slug']);

        $to = $this->argument('to');
        $to = LocationType::find($to, ['_id', 'name', 'slug']);

        $query = (new Location())->newQuery();
        $query->where('type', 'elemMatch', ['_id' => $from->_id]);

        $locations = $query->get();
        \Auth::loginUsingId('5bc18c7c5203177bab791c42'); // tranghv

        foreach ($locations as $location) {
            $location->update([
                'type' => [
                    $to->toArray()
                ]
            ]);
        }

        $this->info('Change type of ' . $locations->count() . ' locations');
    }
}
