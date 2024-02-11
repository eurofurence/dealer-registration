<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TableType;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

class RegSysClientController extends Controller
{
    public static function getPackages(string|null $regId): mixed
    {
        if (!self::validateParam($regId, 'reg id')) {
            return [];
        }

        $response = Http::get(config('services.regsys.url') . '/package-api', [
            'token' => config('services.regsys.token'),
            'id' => $regId,
        ]);

        if ($response->ok()) {
            return $response->json('packages');
        } else {
            self::logError("Registration with id " . $regId . " could not be retrieved, reason: " . $response->reason());
            return [];
        }
    }

    public static function bookPackage(string|null $regId, TableType $tableType): bool
    {
        if (!self::validateParam($regId, 'reg id')) {
            return false;
        }
        if (!self::validateParam($tableType, 'table type')) {
            return false;
        }

        $url = config('services.regsys.url')
            . '/package-api?token=' . config('services.regsys.token')
            . "&id=" . $regId
            . "&package=" . $tableType->package;

        $response = Http::post($url);

        if ($response->ok()) {
            return true;
        } else {
            self::logError("Package for id " . $regId . " could not be booked, reason: " . $response->reason());
            return false;
        }
    }

    public static function removePackage(string|null $regId, TableType $tableType): bool
    {
        if (!self::validateParam($regId, 'reg id')) {
            return false;
        }
        if (!self::validateParam($tableType, 'table type')) {
            return false;
        }

        $url = config('services.regsys.url')
            . '/package-api?token=' . config('services.regsys.token')
            . "&id=" . $regId
            . "&package=" . $tableType->package;

        $response = Http::delete($url);

        if ($response->ok()) {
            return true;
        } else {
            self::logError("Package for id " . $regId . " could not be deleted, reason: " . $response->reason());
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

    public static function getSingleReg(string|null $regId): mixed
    {
        if (!self::validateParam($regId, 'reg id')) {
            return null;
        }

        $response = Http::get(config('services.regsys.url') . '/dealers-den-api', [
            'token' => config('services.regsys.token'),
            'id' => $regId,
        ]);

        if ($response->ok()) {
            if (!empty($response->json('result'))) {
                return $response->json('result')[0];
            } else {
                // If reg ID is invalid, regsys returns HTTP 200 but an empty array.
                self::logError("Registration with id " . $regId . " could not be retrieved, reg id invalid.");
                return null;
            }
        } else {
            self::logError("Registration with id " . $regId . " could not be retrieved, reason: " . $response->reason());
            return null;
        }
    }

    /**
     * Set the `dealerreg` additional info flag for the given registration.
     *
     * @param string $regId Registration number to set the flag for
     * @param bool $hasDealerReg true if the flag should be set
     * @return bool|null true or false depending on whether the flag was successfully set to the requested on the registration, null if an error was encountered.
     * @throws BindingResolutionException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws Exception
     */
    public static function setAdditionalInfoDealerReg(string $regId, bool $hasDealerReg): bool|null
    {
        if (!self::validateParam($regId, 'reg id')) {
            return null;
        }

        $httpRequest = Http::withQueryParameters([
            'token' => config('services.regsys.token'),
            'area' => 'dealerreg',
            'id' => $regId,
        ]);

        $response = null;
        if ($hasDealerReg) {
            $response = $httpRequest->post(config('services.regsys.url') . '/addinfo-api');
        } else {
            $response = $httpRequest->delete(config('services.regsys.url') . '/addinfo-api');
        }

        $result = $response?->json();

        if (!$response?->ok() || $result['ok'] !== true) {
            self::logError("Additional info 'dealerreg' for registration with id {$regId} could not be set to {$hasDealerReg}, reason: " . ($response?->reason() ?? 'request failed') . " " . $result['message'] ?? 'for unknown reason');
            return null;
        }

        return $result['enabled'] === $hasDealerReg;
    }

    /**
     * Check whether the `dealerreg` additional info flag is set for the given registration.
     *
     * @param string $regId Registration number to retrieve the flag for
     * @return bool|null true or false depending on whether the flag is set on the registration, null if an error was encountered.
     * @throws BindingResolutionException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public static function getAdditionalInfoDealerReg(string $regId): bool|null
    {
        if (!self::validateParam($regId, 'reg id')) {
            return null;
        }

        $response = Http::get(config('services.regsys.url') . '/addinfo-api', [
            'token' => config('services.regsys.token'),
            'area' => 'dealerreg',
            'id' => $regId,
        ]);

        $result = $response?->json();

        if (!$response?->ok() || $result['ok'] !== true) {
            self::logError("Additional info 'dealerreg' for registration with id " . $regId . " could not be retrieved, reason: " . ($response?->reason() ?? 'request failed') . " " . $result['message'] ?? 'for unknown reason');
            return null;
        }

        return $result['enabled'];
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
