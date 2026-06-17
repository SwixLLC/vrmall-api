<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Setting;
class SettingController extends Controller
{
    public function index()
    {
        $s = Setting::all()->pluck("value", "key");
        return response()->json(["success" => true, "data" => [
            "app_name" => $s->get("app_name", "VRMALL"),
            "currencies" => ["USD", "EUR", "MAD"],
            "currency" => $s->get("currency", "USD"),
            "googleMapKey" => $s->get("googleMapKey", ""),
            "google_map_key" => $s->get("google_map_key", ""),
            "default_latitude" => $s->get("default_latitude", "33.9716"),
            "default_longitude" => $s->get("default_longitude", "-6.8498"),
            "default_city" => $s->get("default_city", "Rabat"),
            "cod_enabled" => $s->get("cod_enabled", "true"),
            "wallet_enabled" => $s->get("wallet_enabled", "true"),
        ]]);
    }
}