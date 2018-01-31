<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Village;
use File;

class JsonConverter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:jsonconverter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert GeoJSON to SQL';

    /**
     * Create a new command instance.
     *
     * @return void
     */
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
        $this->info($this->description);                

        $files = File::allFiles(public_path('mapdata'));
        foreach ($files as $key => $value) {
            // $this->info($value);
            $this->parse($value);
        }
    }

    public function parse($files)
    {
        $jsonpath = file_get_contents($files);
        $decode = json_decode($jsonpath);

        // dd($decode);
        $selection = $decode->features;
        // $re_encode = json_encode($selection);        

        // Get only property in array
        $property = $this->getProperty($selection);

        // $this->info(response()->json($property));

        // dd($property[300]);
        // saving to db
        // TODO taruh di seeder untuk seeding database
        // Truncate table agar tidak duplikat saat di refresh
        // Village::truncate();
        foreach ($property as $key => $value) {
            $p_item = $value;
            $desa = new Village();
            // terdeteksi ada duplicate
            // $desa->id = $p_item->ID_DESA;

            $desa->provinsi = $p_item->PROVINSI;
            $desa->kabkot = $p_item->KABKOT;
            $desa->kecamatan = $p_item->KECAMATAN;
            // $desa->pulau = $p_item->Pulau;
            $desa->desa = $p_item->DESA_1;
            $desa->id_desa = $p_item->ID_DESA;
            $desa->json_index = $key;
            // $desa->save();

            $this->info(response()->json($desa));
        }
        // dd($property);
    }

    public function getProperty($input)
    {
        $property = collect($input);
        $new_property = $property->map(function ($item, $key) {
            // $p_item = $item->properties;
            $p_item = $item->attributes;            
            return $p_item;
        });
        return $new_property;
    }
  
}
