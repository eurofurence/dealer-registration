<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use ZipArchive;

class ProfileController extends Controller
{
    private static ImageManager $manager;

    public function index()
    {
    }

    public function create(Request $request)
    {
    }

    public static function createOrUpdate(Request $request, int $applicationId): Profile
    {
        $profileData = [
            "short_desc" => $request->input('short_desc'),
            "artist_desc" => $request->input('artist_desc'),
            "art_desc" => $request->input('art_desc'),
            "website" => $request->input('profile_website'),
            "twitter" => trim($request->input('twitter'), '@'),
            "mastodon" => trim($request->input('mastodon'), '@'),
            "bluesky" => trim($request->input('bluesky'), '@'),
            "telegram" => trim($request->input('telegram'), '@'),
            "discord" => $request->input('discord'),
            "tweet" => $request->input('tweet'),
            "art_preview_caption" => $request->input('art_preview_caption'),
            "attends_thu" => $request->input('attends_thu') === "on",
            "attends_fri" => $request->input('attends_fri') === "on",
            "attends_sat" => $request->input('attends_sat') === "on",
            "is_hidden" => $request->input('profile_hidden') === "on",
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

        $profile = Profile::updateOrCreate([
            "application_id" => $applicationId,
        ], $profileData);
        $profile->keywords()->sync($request->get('keywords'));
        return $profile;
    }

    private static function storeImage(Request $request, string $fileName, int|null $width, int|null $height)
    {
        if (!isset(self::$manager)) {
            self::$manager = new ImageManager(new Driver());
        }
        $imagePath = $request->file($fileName)->storePublicly('', ['disk' => 'public']);
        $image = self::$manager->read(Storage::disk('public')->get($imagePath))->cover($width, $height)->toWebp();
        Storage::disk('public')->put($imagePath, $image);
        return $imagePath;
    }

    public static function getImage(string $fileName)
    {
        Storage::disk('public')->get($fileName);
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
                'image',
                'mimes:jpeg,png',
                'max:1024',
                'dimensions:min_width=60,min_height=60,max_width=2000,max_height=2000'
            ],
            "image_artist" => [
                'image',
                'mimes:jpeg,png',
                'max:1024',
                'dimensions:min_width=60,min_height=60,max_width=2000,max_height=2000'
            ],
            "image_art" => [
                'image',
                'mimes:jpeg,png',
                'max:1024',
                'dimensions:min_width=60,min_height=60,max_width=2000,max_height=2000'
            ],
            "short_desc" => [
                'max:1024',
            ],
            "artist_desc" => [
                'max:2048',
            ],
            "art_desc" => [
                'max:2048',
            ],
            "profile_website" => [
                'max:255',
            ],
            "twitter" => [
                'nullable',
                // Twitter user name validation: https://help.twitter.com/en/managing-your-account/twitter-username-rules
                'regex:/^@?[0-9a-z_]{4,15}$/i',
            ],
            "mastodon" => [
                'nullable',
                // Mastodon user name validation loosely based on
                // https://docs.joinmastodon.org/spec/webfinger/#intro and
                // https://datatracker.ietf.org/doc/html/rfc7565#section-7
                /*
                 * acctURI     = "acct" ":" userpart "@" host
                 * userpart    = unreserved / sub-delims 0*( unreserved / pct-encoded / sub-delims )
                 * host        = IP-literal / IPv4address / reg-name
                 * IP-literal  = "[" ( IPv6address / IPvFuture  ) "]"
                 * IPvFuture   = "v" 1*HEXDIG "." 1*( unreserved / sub-delims / ":" )
                 * reg-name    = *( unreserved / pct-encoded / sub-delims )
                 * reserved    = gen-delims / sub-delims
                 * gen-delims  = ":" / "/" / "?" / "#" / "[" / "]" / "@"
                 * sub-delims  = "!" / "$" / "&" / "'" / "(" / ")"/ "*" / "+" / "," / ";" / "="
                 * pct-encoded = "%" HEXDIG HEXDIG
                 * unreserved  = ALPHA / DIGIT / "-" / "." / "_" / "~"
                 */
                'regex:/^@?([A-Z0-9\-._~!$&\'\(\)\*,;=][A-Z0-9\-._~%!$&\'\(\)\*,;=]*)@([A-Z0-9\-._~%!$&\'\(\)\*,;=]+\.[A-Z]{2,})$/i'
            ],
            "bluesky" => [
                'nullable',
                // Bluesky user name validation:
                // https://atproto.com/specs/handle
                'regex:/^@?([A-Z0-9]([A-Z0-9-]{0,61}[A-Z0-9])?\.)+[A-Z]([A-Z0-9-]{0,61}[A-Z0-9])?$/i',
            ],
            "telegram" => [
                'nullable',
                // Telegram user name validation: https://core.telegram.org/method/account.checkUsername
                'regex:/^@?[0-9a-z_]{5,32}$/i',
            ],
            "discord" => [
                'nullable',
                // Discord user name validation: https://discord.com/developers/docs/resources/user
                // (simplified because we don't need to check all edge cases)
                'regex:/^[^@#:]{2,32}(#[0-9]{4})?$/',
            ],
            "tweet" => [
                'max:280',
            ],
            "art_preview_caption" => [
                'max:255',
            ],
            "attends_thu" => [
                'exclude_if:applicationType,assistant',
                'required_without_all:attends_fri,attends_sat',
            ],
            "attends_fri" => [
                'exclude_if:applicationType,assistant',
                'required_without_all:attends_thu,attends_sat',
            ],
            "attends_sat" => [
                'exclude_if:applicationType,assistant',
                'required_without_all:attends_thu,attends_fri',
            ],
        ];
    }

    public static function addImagesToZip(ZipArchive $zip)
    {
        foreach (Profile::all() as $profile) {
            $imgThumbnail = $profile->image_thumbnail;
            if (!empty($imgThumbnail) && Storage::disk('public')->exists($imgThumbnail)) {
                $zip->addFromString('images/thumbnail_' . $profile->application()->first()->id . '.' . pathinfo($profile->image_thumbnail, PATHINFO_EXTENSION), Storage::disk('public')->get($imgThumbnail));
            }
            $imgArt = $profile->image_art;
            if (!empty($imgArt) && Storage::disk('public')->exists($imgArt)) {
                $zip->addFromString('images/art_' . $profile->application()->first()->id  . '.' . pathinfo($profile->image_art, PATHINFO_EXTENSION), Storage::disk('public')->exists($imgArt));
            }
            $imgArtist = $profile->image_artist;
            if (!empty($imgArtist) && Storage::disk('public')->exists($imgArtist)) {
                $zip->addFromString('images/artist_' . $profile->application()->first()->id . '.' . pathinfo($profile->image_artist, PATHINFO_EXTENSION), Storage::disk('public')->get($imgArtist));
            }
        }
    }
}
