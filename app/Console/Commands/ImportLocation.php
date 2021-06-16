<?php


namespace App\Console\Commands;


use App\Imports\CityImport;
use App\Imports\DistrictImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportLocation extends Command
{
    protected $signature = 'Import:Location';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update command like';


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Excel::import(new CityImport, public_path('/location_korean.xlsx'));
        Excel::import(new DistrictImport, public_path('/district.xlsx'));
        return;
    }
}
