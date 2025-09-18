<?php

namespace App\Http\Controllers;

use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;

class ProdyController extends Controller
{
    public function index($id){

        try{
            $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiNjhmYzgwZDc3MWRjMmFmMDg4Y2UwMmE1MjFiYmEyZjY3NmNjMDg3NTFlYmM4ODI5N2U4MmIxZWIwNTQ0YzdjMDZlNzU3ZGRiYWVlNTNjYzQiLCJpYXQiOjE3NTgxNjAyNTguMjI5Mzg0LCJuYmYiOjE3NTgxNjAyNTguMjI5Mzg2LCJleHAiOjE3NTgyNDY2NTguMjE5MzQ5LCJzdWIiOiJiYWdhc3NhbXVkcmEiLCJzY29wZXMiOlsib2xkLXN1cGVyYWRtaW4iLCJjZWxvZS1kYXNoYm9hcmQiLCJvbGQtZG9zZW4iLCJvbGQtYmFhIiwiYXR0ZW5kYW5jZS1lbXBsb3llZSIsImRhc2hib2FyZC11c2VyIiwibmV3cy1hcHByb3ZlciIsIm5ld3MtY3JlYXRvciIsIm5ldy1zc28iLCJvbGQtcGVnYXdhaSIsInNzby1vcGVubGliIiwib2xkLWFkbWluLWRhdGEtbWFoYXNpc3dhLWZha3VsdGFzIiwib2xkLXNpc2ZvIiwib2xkLWFkbWluLWxhYyIsIm9sZC1hZG1pbi1sYWFrIiwib2xkLWJrLXRlbHUiLCJvbGQta2Vsb21wb2sta2VhaGxpYW4iLCJ0cmFpbmluZy1mc2RwLXN1cHBvcnQiLCJ0cmFpbmluZy11c2VyLWZha3VsdGFzIiwib2xkLWFkbWluLWJrIiwib2xkLWRldnRlYW0iLCJlbXBsb3llZS1zdHJ1Y3R1cmFsIiwib25zaXRlcmVnaXN0cmF0aW9uLWFkbWluIiwiaXRkb2MtbWFxdXRpIiwiaXRkb2NfZXh0YXVkaXRvciJdfQ.sncWr8QbGp8TCHeb6YFMCJU5dZnP5R_tmqd-iZ85FPVCA1E9i9QttBJUc1j9SrTxVrsy54sORNHewnsqiLSlVmZOIisVA78nNVe2pUKx2NPZ5xK1HsK9jfkRHqtRJyg8Y0T5o_4GPDECRzzCO6vOTVOuSnSNE6dRbDJjlvebQ-erN2Xubhkag2WecUrclMzm_fMMTGGHnKHfnU9N_MA_1x90rIgdHuNYt1W-7R98_ZTFUXqGzcjiyNdwu9p5V0hpId2Rs6l6fMDdSuc0eq0EBGHh98weRtcwjLumaE36QZ1PH4qeiJmrS-D155vm9vOfTlufxi3ynh57JuPvqmwnoHes1fDIeDDJ-bmDRxnWGJvNYusucoZIiL7VkxPCL4n1Ahyi0hO2juzYliHgQTxpBULmtsAnCJ7qYTYfKL8oy8BG-DDk0qvme9WOZhqq-s6IhiOO6-WJIPMOm-VuHcnY5LItlf2S_-LFCBpdQFH1KkBDOnJbqfUmU8KmooFRn51fLcHa-N29g9JjvQgQEhiMLcH-y16eWOV7V7DjNjW60jMP1Q7g7NLFlscNowj1EoZvwCZelZf2X2TiM67401dKIhgU8HEG8pujBN0OZ7l-NYp_WN05yMe64VeMHMPtsZyrBUNwPu3YmhB0GWKO11VAcsZr4GrTwhk-tePrSEy3R3U";

            $response = Http::withToken($token)
                ->get("https://gateway.telkomuniversity.ac.id/b2ac79622cd60bce8dc5a1a7171bfc9c/{$id}");

            if ($response->successful() === 403) {
                Log::info('Token tidak valid');
                return response()->json([
                    'Success' => 'Failed',
                    'Message' => 'Token tidak valid'
                ],403);
            }

            $prody = $response->json();

            return response()->json([
                'Success' => 'Success',
                'Data' => $prody
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'Success' => 'Failed',
                'Message' => $e->getMessage()
            ]);
        }
    }
}