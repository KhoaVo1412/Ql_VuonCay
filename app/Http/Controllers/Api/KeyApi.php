<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\AppMap;
use App\Models\Runtimes;
use App\Models\Webmap;
use Illuminate\Http\Request;

class KeyApi extends Controller
{
    public function index()
    {

        $keys = ApiKey::select('token_key')->get();
        $tokenKeys = $keys->map(function ($key) {
            return $key->token_key;
        });
        return response()->json([
            "data" => ["keys" => $tokenKeys],
            "status" => 200,
            "message" => 'api key.'
        ]);
    }
    public function webmap()
    {
        $webmaps = Webmap::select('webmap', 'webapp')->get();

        $data = $webmaps->map(function ($w) {
            return [
                'webmap' => $w->webmap,
                'webapp' => $w->webapp,
            ];
        });

        return response()->json([
            "data" => $data,
            "status" => 200,
            "message" => 'Web links and apps'
        ]);
    }
    public function appmap()
    {
        $keys = ApiKey::select('token_key', 'updated_at')->get();
        $tokenKeys = $keys->map(function ($key) {
            return [
                'key' => $key->token_key,
                'time_update_token' => $key->updated_at ? $key->updated_at->format('d/m/Y H:i:s') : null,

            ];
        });

        $appmaps = AppMap::select('appMap', 'updated_at')->get();
        $data = $appmaps->map(function ($w) {
            return [
                'appMap' => $w->appMap,
                'time_update_map' => $w->updated_at ? $w->updated_at->format('d/m/Y H:i:s') : null,
            ];
        });

        $runtimes = Runtimes::select('runtime', 'updated_at')->get();
        $data1 = $runtimes->map(function ($r) {
            return [
                'runtime' => $r->runtime,
                'runtime_update' => $r->updated_at ? $r->updated_at->format('d/m/Y H:i:s') : null,

            ];
        });
        return response()->json([
            "token" => $tokenKeys,
            "appmap" => $data,
            "runtime" => $data1,
            "status" => 200,
            "message" => 'data token, app map, runtime'
        ]);
    }
    public function appmap2222()
    {
        $keys = ApiKey::select('token_key', 'updated_at', 'created_at')->get();

        $tokenKeys = $keys->map(function ($key) {
            return [
                'token_key' => $key->token_key,
                'created_at' => $key->created_at,
                'updated_at' => $key->updated_at
            ];
        });
        $appmaps = AppMap::select('appMap', 'runtime', 'created_at', 'updated_at')->get();

        $data = $appmaps->map(function ($w) {
            return [
                'appMap' => $w->appMap,
                'runtime_lite_lincese' => $w->runtime,
                'created_at' => $w->created_at,
                'updated_at' => $w->updated_at
            ];
        });

        return response()->json([
            "token" => [
                "keys" => $tokenKeys,
            ],
            "data" => $data,
            "status" => 200,
            "message" => 'data token, app map, runtime'
        ]);
    }
}
