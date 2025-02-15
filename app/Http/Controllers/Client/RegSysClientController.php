<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TableType;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

class RegSysClientController extends Controller
{
    public static function getPackages(?string $regId): ?array
    {
        if (!self::isConfigured()) {
            return null;
        }

        if (empty($regId)) {
            return null;
        }

        $response = Http::get(config('services.regsys.url') . '/regsys/service/package-api', [
            'token' => config('services.regsys.token'),
            'id' => $regId,
        ]);

        if ($response->ok()) {
            return (array)$response->json('packages');
        } else {
            self::logError("Registration with id " . $regId . " could not be retrieved, reason: " . $response->reason());
            return null;
        }
    }

    public static function bookPackage(string $regId, TableType $tableType): bool
    {
        if (!self::isConfigured()) {
            return false;
        }
        if (empty($regId)) {
            return false;
        }
        if ($tableType === null) {
            self::logError("Unable book table package for registration ID {$regId} without valid table type.");
            return false;
        }

        $url = config('services.regsys.url')
            . '/regsys/service/package-api?token=' . config('services.regsys.token')
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

    public static function removePackage(string $regId, TableType $tableType): bool
    {
        if (!self::isConfigured()) {
            return false;
        }
        if (empty($regId)) {
            return false;
        }
        if ($tableType === null) {
            self::logError("Unable remove table package for registration ID {$regId} without valid table type.");
            return false;
        }

        $url = config('services.regsys.url')
            . '/regsys/service/package-api?token=' . config('services.regsys.token')
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

    /**
     * Retrieve basic data on all registrations indexed by email or id.
     *
     * @param string $key registration attribute to be used as key for the associative array (must be 'email' or 'id')
     * @return array associative array of all registrations using the value of the attribute
     *               defined by `$key` as the key for the respective registration.
     * @throws BindingResolutionException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public static function getAllRegs($key = 'email'): array
    {
        if (!self::isConfigured()) {
            return [];
        }
        if ($key !== 'email' && $key !== 'id') {
            throw new InvalidArgumentException('key must be either "email" or "id"');
        }

        $response = Http::get(config('services.regsys.url') . '/regsys/service/dealers-den-api', [
            'token' => config('services.regsys.token'),
        ]);

        if ($response->ok()) {
            $result = (array)$response->json('result');
            return array_reduce($result, function ($registrations, $registration) use ($key) {
                $registrations[$registration[$key]] = $registration;
                return $registrations;
            }, []);
        } else {
            self::logError("Registrations could not be retrieved, reason: " . $response->reason());
            return [];
        }
    }

    public static function getSingleReg(?string $regId): mixed
    {
        if (!self::isConfigured()) {
            return null;
        }
        if (empty($regId)) {
            return null;
        }

        $response = Http::get(config('services.regsys.url') . '/regsys/service/dealers-den-api', [
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
        if (!self::isConfigured()) {
            return null;
        }
        if (empty($regId)) {
            return null;
        }

        $httpRequest = Http::withQueryParameters([
            'token' => config('services.regsys.token'),
            'area' => 'dealerreg',
            'id' => $regId,
        ]);

        $response = null;
        if ($hasDealerReg) {
            $response = $httpRequest->post(config('services.regsys.url') . '/regsys/service/addinfo-api');
        } else {
            $response = $httpRequest->delete(config('services.regsys.url') . '/regsys/service/addinfo-api');
        }

        $result = $response?->json();

        if (!$response?->ok() || $result['ok'] !== true) {
            self::logError("Additional info 'dealerreg' for registration with id {$regId} could not be set to {$hasDealerReg}, reason: " . ($response?->reason() ?? 'request failed') . " " . ($result['message'] ?? 'for unknown reason'));
            return null;
        }

        return $result['enabled'] === $hasDealerReg;
    }

    /**
     * Check whether the `dealerreg` additional info flag is set for the given registration.
     *
     * @param null|string $regId Registration number to retrieve the flag for
     * @return null|bool true or false depending on whether the flag is set on the registration, null if an error was encountered.
     * @throws BindingResolutionException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public static function getAdditionalInfoDealerReg(?string $regId): ?bool
    {
        if (!self::isConfigured()) {
            return null;
        }
        if (empty($regId)) {
            return null;
        }

        $response = Http::get(config('services.regsys.url') . '/regsys/service/addinfo-api', [
            'token' => config('services.regsys.token'),
            'area' => 'dealerreg',
            'id' => $regId,
        ]);

        $result = $response?->json();

        if (!$response?->ok() || $result['ok'] !== true) {
            self::logError("Additional info 'dealerreg' for registration with id " . $regId . " could not be retrieved, reason: " . ($response?->reason() ?? 'request failed') . " " . ($result['message'] ?? 'for unknown reason'));
            return null;
        }

        return boolval($result['enabled']);
    }

    /**
     * Retrieve registration ID for user identified either by provided access token or the one
     * stored in the current user's session.
     *
     * @param null|string $accessToken optional access token to be used instead of session
     * @return null|string registration ID provided by attendee service
     * @throws BindingResolutionException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws Exception
     */
    public static function getRegistrationIdForCurrentUser(?string $accessToken = null): ?string
    {
        if (!self::isConfigured()) {
            return null;
        }
        if ($accessToken === null && !Session::has('access_token')) {
            return null;
        }

        $response = Http::withToken($accessToken ?? Session::get('access_token'))->get(config('services.regsys.url') . '/attsrv/api/rest/v1/attendees');

        if ($response->notFound()) {
            return null;
        }

        if ($response->ok()) {
            $result = $response->json('ids');
            if (empty($result)) {
                return null;
            }
            if (count($result) !== 1) {
                // Endpoint may return multiple IDs, which is currently not relevant to us.
                // https://github.com/eurofurence/reg-attendee-service/blob/2bfb94f71b649b50e0b188e1f7c2fb3dad0d21b8/api/openapi-spec/openapi.yaml#L35
                self::logError("Expected zero or one registration IDs, but got: " . print_r($result, true));
                return null;
            }

            return $result[0];
        }

        self::logError("Registration id for currently signed-in user could not be retrieved, reason: " . $response->reason());
        return null;
    }


    private static function logError(string $message)
    {
        Log::warning("Class: " . __CLASS__ . ", Function: " . __FUNCTION__ . ": " . $message);
    }

    /**
     * Checks if regsys client is configured to avoid making calls if it has been disabled by not
     * providing either URL or token. Must be checked before any calls are made.
     *
     * @return bool `true` if all required settings are configured
     */
    private static function isConfigured()
    {
        # self::logError("Checking regsys client configuration: " . (config('services.regsys.url') !== null && config('services.regsys.token') !== null ? "configured" : "disabled"));
        return config('services.regsys.url') !== null && config('services.regsys.token') !== null;
    }
}
