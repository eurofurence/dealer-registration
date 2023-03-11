<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    public function index()
    {

    }

    public function create(Request $request)
    {

    }

    public static function createOrUpdate(Request $request, int $applicationId): Profile
    {
        $profileData = [
            "short_desc" => $request->get('short_desc'),
            "artist_desc" => $request->get('artist_desc'),
            "art_desc" => $request->get('art_desc'),
            "website" => $request->get('profile_website'),
            "twitter" => $request->get('twitter'),
            "telegram" => $request->get('telegram'),
            "discord" => $request->get('discord'),
            "tweet" => $request->get('tweet'),
            "art_preview_caption" => $request->get('art_preview_caption'),
            "is_print" => $request->get('is_print') === "on",
            "is_artwork" => $request->get('is_artwork') === "on",
            "is_fursuit" => $request->get('is_fursuit') === "on",
            "is_commissions" => $request->get('is_commissions') === "on",
            "is_misc" => $request->get('is_misc') === "on",
            "attends_thu" => $request->get('attends_thu') === "on",
            "attends_fri" => $request->get('attends_fri') === "on",
            "attends_sat" => $request->get('attends_sat') === "on",
        ];

        // Keep old images if no new data is sent with the request
        if ($request->hasFile('image_thumbnail')) {
            $profileData["image_thumbnail"] = ProfileController::storeImage($request, "image_thumbnail", 60, 60);
        }
        if ($request->hasFile('image_artist')) {
            $profileData["image_artist"] = ProfileController::storeImage($request, "image_artist", 400, 400);
        }
        if ($request->hasFile('image_art')) {
            $profileData["image_art"] = ProfileController::storeImage($request, "image_art", 400, 450);
        }

        return Profile::updateOrCreate([
            "application_id" => $applicationId,
        ], $profileData);
    }

    private static function storeImage(Request $request, String $fileName, int|null $width, int|null $height) {
        $imagePath = $request->file($fileName)->store('public');
        $image = Image::make(Storage::get($imagePath))->resize($width, $height)->encode();
        Storage::put($imagePath, $image);
        $imagePath = explode('/',$imagePath);
        return $imagePath[1];                    
    }

    public static function getImage(String $fileName) {
        Storage::get($fileName);
    }

    public static function getOrCreate(int|null $applicationId): Profile
    {
        return Profile::findByApplicationId($applicationId) ?? new Profile();
    }

    public static function getByApplicationId(int|null $applicationId): Profile|null
    {
        return Profile::findByApplicationId($applicationId);
    }

    public function store(Request $request)
    {

    }

    public function show(Profile $profile)
    {

    }

    public function edit(Profile $profile)
    {
    }

    public function update(Request $request, Profile $profile)
    {
    }

    public function destroy(Profile $profile)
    {
    }

    public static function getValidations()
    {
        return [
            "image_thumbnail" => [
                'mimes:jpeg,png',
                'max:1024',
                'dimensions:min_width=60,min_height=60,max_width=2000,max_height=2000',
                'exclude_if:applicationType,assistant',
            ],
            "image_artist" => [
                'mimes:jpeg,png',
                'max:1024',
                'dimensions:min_width=60,min_height=60,max_width=2000,max_height=2000',
                'exclude_if:applicationType,assistant',
            ],
            "image_art" => [
                'mimes:jpeg,png',
                'max:1024',
                'dimensions:min_width=60,min_height=60,max_width=2000,max_height=2000',
                'exclude_if:applicationType,assistant',
            ],
        ];
    }
}