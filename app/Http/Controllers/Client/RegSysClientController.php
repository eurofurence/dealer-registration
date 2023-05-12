<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TableType;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RegSysClientController extends Controller
{
    public static function getPackages(string|null $reg_id): mixed
    {
        if (!self::validateParam($reg_id, 'reg id')) {
            return [];
        }

        $response = Http::get(config('services.regsys.url') . '/package-api', [
            'token' => config('services.regsys.token'),
            'id' => $reg_id,
        ]);

        if ($response->ok()) {
            return $response->json('packages');
        } else {
            self::logError("Registration with id " . $reg_id . " could not be retrieved, reason: " . $response->reason());
            return [];
        }
    }

    public static function bookPackage(string|null $reg_id, TableType $tableType): bool
    {
        if (!self::validateParam($reg_id, 'reg id')) {
            return false;
        }
        if (!self::validateParam($tableType, 'table type')) {
            return false;
        }

        $url = config('services.regsys.url')
            . '/package-api?token=' . config('services.regsys.token')
            . "&id=" . $reg_id
            . "&package=" . $tableType->package;

        $response = Http::post($url);

        if ($response->ok()) {
            return true;
        } else {
            self::logError("Package for id " . $reg_id . " could not be booked, reason: " . $response->reason());
            return false;
        }
    }

    public static function removePackage(string|null $reg_id, TableType $tableType): bool
    {
        if (!self::validateParam($reg_id, 'reg id')) {
            return false;
        }
        if (!self::validateParam($tableType, 'table type')) {
            return false;
        }

        $url = config('services.regsys.url')
            . '/package-api?token=' . config('services.regsys.token')
            . "&id=" . $reg_id
            . "&package=" . $tableType->package;

        $response = Http::delete( $url);

        if ($response->ok()) {
            return true;
        } else {
            self::logError("Package for id " . $reg_id . " could not be deleted, reason: " . $response->reason());
            return false;
        }
    }

    public static function getAllRegs(): mixed
    {
        $response = Http::get(config('services.regsys.url') . '/dealers-den-api', [
            'token' => config('services.regsys.token'),
        ]);

        if ($response->ok()) {
            return $response->json('result');
        } else {
            self::logError("Registrations could not be retrieved, reason: " . $response->reason());
            return [];
        }
    }

    public static function getSingleReg(string|null $reg_id): mixed
    {
        if (!self::validateParam($reg_id, 'reg id')) {
            return null;
        }

        $response = Http::get(config('services.regsys.url') . '/dealers-den-api', [
            'token' => config('services.regsys.token'),
            'id' => $reg_id,
        ]);

        if ($response->ok()) {
            return $response->json('result')[0];
        } else {
            self::logError("Registration with id " . $reg_id . " could not be retrieved, reason: " . $response->reason());
            return null;
        }
    }

    private static function validateParam(object|string|null $param, string $type): bool
    {
        if (empty($param)) {
            self::logError("Parameter " . $type . " missing");
            return false;
        } else {
            return true;
        }
    }

    private static function logError(string $message)
    {
        Log::warning("Class: " . __CLASS__ . ", Function: " . __FUNCTION__ . ": " . $message);
    }
}
