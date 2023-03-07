<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {

    }

    public function create(Request $request)
    {
        $imgThumbnailName = NULL;
        $imgArtistName = NULL;
        $imgArtName = NULL;
        $application_id = $request->application_id;

        if($request->hasFile('image_thumbnail')){
            $imgThumbnailName = 'thumbnail_'.$application_id.'.'.$request->file('image_thumbnail')->getClientOriginalExtension();
            $request->file('image_thumbnail')->move(public_path('images/upload'), $imgThumbnailName);
        }
        if($request->hasFile('image_artist')){
            $imgArtistName = 'artist_'.$application_id.'.'.$request->file('image_artist')->getClientOriginalExtension();
            $request->file('image_artist')->move(public_path('images/upload'), $imgArtistName);
        }
        if($request->hasFile('image_art')){
            $imgArtName = 'art_'.$application_id.'.'.$request->file('image_art')->getClientOriginalExtension();
            $request->file('image_art')->move(public_path('images/upload'), $imgArtName);
        }

        return Profile::create([
            "application_id" => $application_id,
            "short_desc" => $this->get('short_desc'),
            "artist_desc" => $this->get('artist_desc'),
            "art_desc" => $this->get('art_desc'),
            "website" => $this->get('profile_website'),
            "twitter" => $this->get('twitter'),
            "telegram" => $this->get('telegram'),
            "discord" => $this->get('discord'),
            "tweet" => $this->get('tweet'),
            "art_preview_caption" => $this->get('art_preview_caption'),
            "is_print" => $this->get('is_print') === "on",
            "is_artwork" => $this->get('is_artwork') === "on",
            "is_fursuit" => $this->get('is_fursuit') === "on",
            "is_commissions" => $this->get('is_commissions') === "on",
            "is_misc" => $this->get('is_misc') === "on",
            "attends_thu" => $this->get('attends_thu') === "on",
            "attends_fri" => $this->get('attends_fri') === "on",
            "attends_sat" => $this->get('attends_sat') === "on",
            "image_thumbnail" => $imgThumbnailName,
            "image_art" => $imgArtName,
            "image_artist" => $imgArtistName,
        ]);
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
}
