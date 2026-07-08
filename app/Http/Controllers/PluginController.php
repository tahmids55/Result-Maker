<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PluginController extends Controller
{
    public function config()
    {
        return response()->json([
            "name" => "Insert Placeholder",
            "nameLocale" => [
                "fr" => "Insérer un espace réservé",
                "es" => "Insertar marcador de posición",
                "de" => "Platzhalter einfügen"
            ],
            "guid" => "asc.{E9C9F9A5-C4BD-4DF8-97F1-79A595B40974}",
            "baseUrl" => request()->getSchemeAndHttpHost() . '/onlyoffice-plugin/panel/',
            "version" => "1.0.0",
            "variations" => [
                [
                    "description" => "Insert system placeholders easily.",
                    "url" => "index.html",
                    "icons" => ["icon.png", "icon@2x.png"],
                    "isViewer" => false,
                    "EditorsSupport" => ["word"],
                    "isVisual" => true,
                    "isModal" => false,
                    "isInsideMode" => true,
                    "initDataType" => "none",
                    "initData" => "",
                    "type" => "panel",
                    "buttons" => [],
                    "events" => []
                ]
            ]
        ])->header('Access-Control-Allow-Origin', '*');
    }

    public function index()
    {
        return response()->view('onlyoffice-plugin')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
