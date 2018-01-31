<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\WilayahModel;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Wilayah extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:wilayah {startfrom}';
    protected $startfrom;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ambil data wilayah dari cahyadsn/wilayah';

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
        $startfrom = (int) $this->argument('startfrom');
        $this->info($this->description . '...');
        $this->getWilayahs($startfrom);

////        $this->info(dump($this->getData('11.01.01.2004')));
    }

    function getWilayahs($startfrom = 21518)
    {
        //$wilayahs = WilayahModel::all();

        $skip = $startfrom;
        $take = 100000;
        $sleep_after = 110;
        $wilayahs = WilayahModel::skip($skip-1)->take($take)->get();
        $separator = '-------------------------';
        $lop = 0;
        foreach ($wilayahs as $wilayah) {
            $lop ++;
            if ($lop == $sleep_after) {
                $lop = 0;
                $this->info("SLEEPING ...");
                sleep(30);
            }
            $skip++;
            $this->info("Process: $skip");
            $kode = $wilayah->kode;
            $data = $this->getData($kode);
            if (isset($data->error)) {
                $this->error($kode . ': [Error]. ' . $data->error);
                $this->info("Kodepos SET: " . $data->kodepos);
                dump($data);
                $this->info($separator);

                // Saat error, kodepos masih ada maka simpan
                $wilayah->kodepos = $data->kodepos;
                $wilayah->save();
            }
            else {
                $data = $data->data;

                $nama = $wilayah->nama;
                $lat = $data->lat;
                $lng = $data->lng;
                $kodepos = $data->kodepos;
                $path = json_encode($data->path);

                $this->info("Kode: $kode");
                $this->info("Wilayah: $nama");
                $this->info("LatLng: $lat, $lng");
                $this->info("Kodepos: $kodepos");
                //$this->info("Path: $path");
                $this->info($separator);

                // Simpan variable yang ada
                $wilayah->lat = $data->lat;
                $wilayah->lng = $data->lng;
                $wilayah->kodepos = $data->kodepos;
                $wilayah->path = json_encode($data->path);
                $wilayah->save();
            }
        }
    }

    function getData($kode)
    {
        $client = new Client(); //GuzzleHttp\Client
        $result = $client->get('http://cahyadsn.dev.php.or.id/wilayah/inc/geo_ajax.php?id='.$kode, [
            'headers'        => ['Accept-Encoding' => 'json'],
        ]);
        $contents = (string) $result->getBody();
        $contents = utf8_encode($contents);
        // replace unknown symbol from response
        $contents = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $contents);

        $contents = json_decode($contents);
        return $contents;
    }

    function getJsonLastError()
    {
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                echo ' - No errors';
                break;
            case JSON_ERROR_DEPTH:
                echo ' - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                echo ' - Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                echo ' - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                echo ' - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                echo ' - Unknown error';
                break;
        }
    }
}
