<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TableType;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RegSysClientController extends Controller
{
    public static function bookPackage(string $id, TableType $tableType): bool
    {
        $response = Http::post(config('services.regsys.url') . '/package-api', [
            'token' => config('services.regsys.token'),
            'id' => $id,
            'package' => $tableType->package,
        ]);

        if ($response->ok()) {
            return true;
        } else {
            Log::warning("Package for id " . $id . " could not be booked, reason: " . $response->reason());
            return false;
        }
    }

    public static function removePackage(string $id, TableType $tableType): bool
    {
        $response = Http::delete(config('services.regsys.url') . '/package-api', [
            'token' => config('services.regsys.token'),
            'id' => $id,
            'package' => $tableType->package,
        ]);

        if ($response->ok()) {
            return true;
        } else {
            Log::warning("Package for id " . $id . " could not be deleted, reason: " . $response->reason());
            return false;
        }
    }

    public static function getAllRegs(): mixed
    {
        $response = Http::get(config('services.regsys.url') . '/dealers-den-api', [
            'token' => config('services.regsys.token'),
        ]);

        if ($response->ok()) {
            return $response->json();
        } else {
            Log::warning("Registrations could not be retrieved, reason: " . $response->reason());
            return null;
        }
    }

    public static function getSingleReg(string $id): mixed
    {
        $response = Http::get(config('services.regsys.url') . '/dealers-den-api', [
            'token' => config('services.regsys.token'),
            'id' => $id,
        ]);

        if ($response->ok()) {
            return $response->json();
        } else {
            Log::warning("Registration with id " . $id . " could not be retrieved, reason: " . $response->reason());
            return null;
        }
    }
}
