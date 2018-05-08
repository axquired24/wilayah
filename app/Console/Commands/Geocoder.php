<?php

namespace App\Console\Commands;

use App\WilayahModel;
use Illuminate\Console\Command;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class Geocoder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     * @param apikey = Google ApiKey (New API will active after 10 Minutes
     * `The provided API key is expired` will show if you try to access before 10 Minutes
     */
    protected $signature = 'bot:geocoder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Lat,lng from given location';
    protected $apikey = 'AIzaSyBJIYOyaSUgzPCyuslg2Gcp3GAN3XV6LPI';

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
        $this->info($this->description . '...');
        $this->getWilayah();
    }

    function getWilayah($startfrom=1){
        $selector = DB::raw("kode, nama, is_tracked,
                            LEFT(kode, 2) as prov_kode,
                            LEFT(kode, 5) as kabkot_kode,
                            LEFT(kode, 8) as kec_kode,
                            CONCAT(
                                nama, ', ', 
                                (SELECT nama FROM wilayah WHERE kode = kec_kode), ', ', 
                                (SELECT nama FROM wilayah WHERE kode = kabkot_kode), ', ', 
                                (SELECT nama FROM wilayah WHERE kode = prov_kode)
                                ) as detail,
                            lat, lng, kodepos");

        $filter = "CHAR_LENGTH(kode) = 13
                    AND lat IS NULL
                    AND lng IS NULL
                    AND is_tracked IS NULL";

        $countwilayah = WilayahModel::select($selector)->whereRaw($filter)->count();
        $limiteach = $countwilayah;
        $takeeach = $limiteach - ($startfrom - 1);

        $wilayahs = WilayahModel::select($selector)
                    ->whereRaw($filter)
                    ->skip($startfrom-1)->take($takeeach)
                    ->orderBy('kode', 'ASC')
                    ->get();

        foreach ($wilayahs as $wilayah) {
            $namawilayah = $wilayah->detail;
            $this->info("Wilayah [$wilayah->kode]: " . $namawilayah);

            $geocoder = $this->geoCoder($namawilayah);
            $parsed = $this->parseResponse($geocoder);

            if($parsed->status == "OVER_QUERY_LIMIT") {
                $this->error("OVER_QUERY_LIMIT");
                dump($parsed);
                exit();
            } elseif($parsed->status == "ZERO_RESULTS") {
                $this->error("Coordinate Not Found.");
                dump($parsed);
                $this->markAsTracked($wilayah);
            } elseif($parsed->status == "OK") {
                $this->info("Success. Geocode [Google]");
                $wilayah->lat = $parsed->lat;
                $wilayah->lng = $parsed->lng;
                $wilayah->geobounds = $parsed->geobounds;
                $wilayah->is_tracked = 1;
                $wilayah->save();
                dump($parsed);
            } else {
                $this->error("Unknown Error");
                dump($parsed);
            }

            $this->info("Done: $startfrom of $countwilayah");
            $startfrom++;
            $this->info("-------------------------------------");
        }
    }

    function geoCoder($namawilayah)
    {
        $key = $this->apikey;
        $uri = "https://maps.googleapis.com/maps/api/geocode/json?key=$key&address=$namawilayah";
        $client = new Client(); //GuzzleHttp\Client
        $result = $client->get($uri, [
            'headers'        => ['Accept-Encoding' => 'json'],
        ]);
        $contents = (string) $result->getBody();
        $contents = utf8_encode($contents);
        // replace unknown symbol from response
//        $contents = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $contents);

        $contents = json_decode($contents);
        return $contents;
    }

    function parseResponse($response) {
        if($response->status != "OK") {
            return $response;
        }

        $result = $response->results[0];
        // Insert into new column, geobounds
        // dump($result);
        $geobounds = json_encode($result->geometry->viewport);
        $gaddress = $result->formatted_address;

        $location = $result->geometry->location;
        $lat = $location->lat;
        $lng = $location->lng;

        $ret = [
            'geobounds' => $geobounds,
            'lat' => $lat,
            'lng' => $lng,
            'gaddress' => $gaddress,
            'status' => $response->status
        ];

        return (object) $ret;
    }

    function markAsTracked(WilayahModel $wilayah)
    {
        $wilayah->is_tracked = 1;
        $wilayah->save();
    }
}
