<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestApiSetAcademic extends Command
{
    protected $signature = 'test:api-set-academic {nim} {--check}';
    protected $description = 'Test API set_academic (stt=8) and verify with stt=7';

    public function handle()
    {
        $nim = $this->argument('nim');
        $checkOnly = $this->option('check');
        
        if ($checkOnly) {
            // Hanya cek status saat ini
            $this->info("Checking current status for NIM: $nim");
            $this->checkStatus($nim);
        } else {
            // Set status dan cek
            $this->info("Setting status for NIM: $nim");
            $this->setStatus($nim);
            
            $this->info("Waiting 3 seconds...");
            sleep(3);
            
            $this->info("Checking status after set...");
            $this->checkStatus($nim);
        }
    }
    
    private function setStatus($nim)
    {
        $date = date('Y-m-d');
        $url = "https://webservice-feeder.telkomuniversity.ac.id/apidikti/getRegpd.php?stt=8&id=$nim&periode=$date&selected=Y";
        
        $this->info("URL: $url");
        
        $response = Http::get($url);
        
        $this->info("Status: " . $response->status());
        $this->info("Body: " . $response->body());
    }
    
    private function checkStatus($nim)
    {
        $url = "https://webservice-feeder.telkomuniversity.ac.id/apidikti/getRegpd.php?stt=7";
        
        $response = Http::get($url);
        
        if ($response->successful()) {
            $data = $response->json();
            
            $found = collect($data)->firstWhere('STUDENTID', $nim);
            
            if ($found) {
                $this->info("Found NIM: $nim");
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['NIM', $found['STUDENTID']],
                        ['Name', $found['FULLNAME']],
                        ['Periode', $found['PERIODE'] ?? 'NULL'],
                        ['Selected', $found['SELECTED'] ?? 'NULL'],
                        ['Status', $found['STATUS']],
                    ]
                );
            } else {
                $this->error("NIM not found: $nim");
            }
        } else {
            $this->error("API call failed");
        }
    }
}
