<div class="card card-body mt-2">
    <div class="card-body">
        <div class="card-title">
            <h4>Profile</h4>
            <p>The following information will be used to present you and your goods in the official Eurofurence App, as
                well as in other media associated with Eurofurence. You will be able to edit this information even after
                the registration period has ended.</p>
        </div>
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label fw-bold">Attend&shy;ance</label>
        </div>
        <div class="row mb-1">
            <div class="col-sm-10 offset-sm-2">
                <div class="form-check">
                    <input class="form-check-input @error('attends_thu') is-invalid @enderror" name="attends_thu"
                        @checked(old('_token') ? old('attends_thu') : $profile?->attends_thu === true) type="checkbox" id="attends_thu">
                    <label class="form-check-label" for="attends_thu">
                        Monday
                    </label>
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-sm-10 offset-sm-2">
                <div class="form-check">
                    <input class="form-check-input @error('attends_fri') is-invalid @enderror" name="attends_fri"
                        @checked(old('_token') ? old('attends_fri') : $profile?->attends_fri === true) type="checkbox" id="attends_fri">
                    <label class="form-check-label" for="attends_fri">
                        Tuesday
                    </label>
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-sm-10 offset-sm-2">
                <div class="form-check">
                    <input class="form-check-input @error('attends_sat') is-invalid @enderror" name="attends_sat"
                        @checked(old('_token') ? old('attends_sat') : $profile?->attends_sat === true) type="checkbox" id="attends_sat">
                    <label class="form-check-label" for="attends_sat">
                        Wednesday
                    </label>
                    @error('attends_sat')
                        <div class="invalid-feedback" role="alert">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-sm-10 offset-sm-2">
                <div id="attendanceHelp" class="form-text">
                    Please select all days when our Attendees can meet you at
                    your Dealers' Den table (must be at least one).
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label fw-bold">Catego&shy;ries</label>
        </div>
        <div class="row mb-1">
            <div class="col-sm-10 offset-sm-2">
                <div class="form-check">
                    <input class="form-check-input" name="is_print" @checked(old('_token') ? old('is_print') : $profile?->is_print === true) type="checkbox"
                        id="is_print">
                    <label class="form-check-label" for="is_print">
                        Printed goods (books, magazines, comics, ...)
                    </label>
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-sm-10 offset-sm-2">
                <div class="form-check">
                    <input class="form-check-input" name="is_artwork" @checked(old('_token') ? old('is_artwork') : $profile?->is_artwork === true) type="checkbox"
                        id="is_artwork">
                    <label class="form-check-label" for="is_artwork">
                        Artwork (sketches, originals, prints, ...)
                    </label>
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-sm-10 offset-sm-2">
                <div class="form-check">
                    <input class="form-check-input" name="is_fursuit" @checked(old('_token') ? old('is_fursuit') : $profile?->is_fursuit === true) type="checkbox"
                        id="is_fursuit">
                    <label class="form-check-label" for="is_fursuit">
                        Fursuit (suits, parts, accessories, ...)
                    </label>
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-sm-10 offset-sm-2">
                <div class="form-check">
                    <input class="form-check-input" name="is_commissions" @checked(old('_token') ? old('is_commissions') : $profile?->is_commissions === true) type="checkbox"
                        id="is_commissions">
                    <label class="form-check-label" for="is_commissions">
                        Commissions
                    </label>
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-sm-10 offset-sm-2">
                <div class="form-check">
                    <input class="form-check-input" name="is_misc" @checked(old('_token') ? old('is_misc') : $profile?->is_misc === true) type="checkbox"
                        id="is_misc">
                    <label class="form-check-label" for="is_misc">
                        Miscellaneous merchandise
                    </label>
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-sm-10 offset-sm-2">
                <div id="attendanceHelp" class="form-text">Select the categories that apply to your goods to make
                    it easier to find you!
                </div>
            </div>
        </div>
        <div class="row mb-1 mt-5">
            <label for="image_thumbnail" class="col-sm-2 col-form-label fw-bold">Thumb&shy;nail</label>
            <div class="col-sm-10">
                <input id="image_thumbnail" type="file"
                    class="form-control @error('image_thumbnail') is-invalid @enderror" name="image_thumbnail"
                    accept="image/jpeg, image/png"
                    onchange="document.getElementById('image_thumbnail_preview').src = window.URL.createObjectURL(this.files[0]); document.getElementById('image_thumbnail_preview_large').src = window.URL.createObjectURL(this.files[0]);">
                <div id="image_thumbnailHelp" class="form-text">Upload an image to be shown next to your name in
                    the dealer list. This image should have a size of 60&times;60 pixels (max file size is 1 MB).
                </div>
                @error('image_thumbnail')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                <img id='image_thumbnail_preview' class="mx-auto mb-5" data-bs-toggle="modal"
                    data-bs-target="#imageThumbnailModal" src="{{ asset("storage/$profile?->image_thumbnail") }}"
                    alt="" style="height: 100px;">

                <!-- Modal -->
                <div class="modal fade" id="imageThumbnailModal" tabindex="-1"
                    aria-labelledby="imageThumbnailModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <img id='image_thumbnail_preview_large'class="mx-auto d-block w-100"
                                    src="{{ asset("storage/$profile?->image_thumbnail") }}" alt="">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <label for="short_desc" class="col-sm-2 col-form-label fw-bold">Short De&shy;scrip&shy;tion</label>
            <div class="col-sm-10">
                <textarea rows="5" type="text" name="short_desc"
                    class="form-control @error('short_desc') is-invalid @enderror" id="short_desc">{{ old('_token') ? old('short_desc') : $profile?->short_desc }}</textarea>
                @error('short_desc')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div id="short_descHelp" class="form-text">
                    A short description, 1 - 3 sentences, to appear in your profile header.
                </div>
            </div>
        </div>
        <div class="row mb-1 mt-5">
            <label for="image_artist" class="col-sm-2 col-form-label fw-bold">Profile Header</label>
            <div class="col-sm-10">

                <input id="image_artist" type="file"
                    class="form-control @error('image_artist') is-invalid @enderror" name="image_artist"
                    accept="image/jpeg, image/png"
                    onchange="document.getElementById('image_artist_preview').src = window.URL.createObjectURL(this.files[0]); document.getElementById('image_artist_preview_large').src = window.URL.createObjectURL(this.files[0]);">
                <div id="image_artistHelp" class="form-text">This image is shown on your dedicated page in the EF app.
                    Aim for a size of 400×400 pixels (max file size is 1 MB).
                </div>
                @error('image_artist')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <img id='image_artist_preview' class="mx-auto mb-5" data-bs-toggle="modal"
                    data-bs-target="#imageArtistModal" src="{{ asset("storage/$profile?->image_artist") }}"
                    alt="" style="height: 100px;">

                <!-- Modal -->
                <div class="modal fade" id="imageArtistModal" tabindex="-1" aria-labelledby="imageArtistModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <img id='image_artist_preview_large' class="mx-auto d-block w-100"
                                    src="{{ asset("storage/$profile?->image_artist") }}" alt="">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <label for="artist_desc" class="col-sm-2 col-form-label fw-bold">About the Artist</label>
            <div class="col-sm-10">
                <textarea rows="5" type="text" name="artist_desc"
                    class="form-control @error('artist_desc') is-invalid @enderror" id="artist_desc">{{ old('_token') ? old('artist_desc') : $profile?->artist_desc }}</textarea>
                @error('artist_desc')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div id="artist_descHelp" class="form-text">
                    Information about yourself; e.g., your background, how you became an artist,...
                </div>
            </div>
        </div>
        <div class="row mb-1 mt-5">
            <label for="image_art" class="col-sm-2 col-form-label fw-bold">Show&shy;case Image</label>
            <div class="col-sm-10">
                <input id="image_art" type="file" class="form-control @error('image_art') is-invalid @enderror"
                    name="image_art" accept="image/jpeg, image/png"
                    onchange="document.getElementById('image_art_preview').src = window.URL.createObjectURL(this.files[0]); document.getElementById('image_art_preview_large').src = window.URL.createObjectURL(this.files[0]);">
                <div id="image_artHelp" class="form-text">You can upload a preview image of your art or merchandise,
                    which will be shown on your dedicated page in the EF app. The size of this image should be 400×450
                    pixels (max file size is 1 MB).
                </div>
                @error('image_art')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <img id='image_art_preview' class="mx-auto mb-5" data-bs-toggle="modal"
                    data-bs-target="#imageArtModal" src="{{ asset("storage/$profile?->image_art") }}" alt=""
                    style="height: 100px;">

                <!-- Modal -->
                <div class="modal fade" id="imageArtModal" tabindex="-1" aria-labelledby="imageArtModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <img id='image_art_preview_large' class="mx-auto d-block w-100"
                                    src="{{ asset("storage/$profile?->image_art") }}" alt="">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <label for="art_preview_caption"
                class="col-sm-2 col-form-label fw-bold">Show&shy;case
                Cap&shy;tion</label>
            <div class="col-sm-10">
                <textarea rows="1" type="text" name="art_preview_caption"
                    class="form-control @error('art_preview_caption') is-invalid @enderror" id="art_preview_caption">{{ old('_token') ? old('art_preview_caption') : $profile?->art_preview_caption }}</textarea>
                @error('art_preview_caption')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div id="art_preview_captionHelp" class="form-text">
                    If you have uploaded an art/merchandise preview image above, you can enter a description that is
                    displayed beneath it.
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <label for="art_desc" class="col-sm-2 col-form-label fw-bold">About the Art</label>
            <div class="col-sm-10">
                <textarea rows="5" type="text" name="art_desc" class="form-control @error('art_desc') is-invalid @enderror"
                    id="art_desc">{{ old('_token') ? old('art_desc') : $profile?->art_desc }}</textarea>
                @error('art_desc')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div id="art_descHelp" class="form-text">
                    Information about your art or other merchandise. This is also the place for details about
                    commissions, prices, and other things worthwhile to know.
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <label for="profile_website" class="col-sm-2 col-form-label fw-bold">Web&shy;site</label>
            <div class="col-sm-10">
                <input type="text" name="profile_website" placeholder="https://yourprofile.example.com/itsme"
                    class="form-control @error('profile_website') is-invalid @enderror" id="profile_website"
                    value="{{ old('_token') ? old('profile_website') : $profile?->website }}">
                @error('profile_website')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div id="websiteHelp" class="form-text">
                    The address of your homepage.
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <label for="twitter" class="col-sm-2 col-form-label fw-bold">Twit&shy;ter</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span clasS="input-group-text">@</span>
                    </div>
                    <input type="text" name="twitter" placeholder="YourTwitterHandle"
                        class="form-control @error('twitter') is-invalid @enderror" id="twitter"
                        value="{{ old('_token') ? old('twitter') : $profile?->twitter }}">
                    @error('twitter')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div id="twitterHelp" class="form-text">
                    Want to make sure people find you on Twitter? Add your handle above to guide them there!
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <label for="telegram" class="col-sm-2 col-form-label fw-bold">Tele&shy;gram</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span clasS="input-group-text">@</span>
                    </div>
                    <input type="text" name="telegram" placeholder="YourTelegramHandle"
                        class="form-control @error('telegram') is-invalid @enderror" id="telegram"
                        value="{{ old('_token') ? old('telegram') : $profile?->telegram }}">
                    @error('telegram')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div id="telegramHelp" class="form-text">
                    Promote your Telegram channel or allow people to get in touch with you personally via Telegram.
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <label for="discord" class="col-sm-2 col-form-label fw-bold">Dis&shy;cord</label>
            <div class="col-sm-10">
                <input type="text" name="discord" placeholder="YourDiscordHandle"
                    class="form-control @error('discord') is-invalid @enderror" id="discord"
                    value="{{ old('_token') ? old('discord') : $profile?->discord }}">
                @error('discord')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div id="discordHelp" class="form-text">
                    Let people find and contact you on Discord by providing your handle here!
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <label for="tweet" class="col-sm-2 col-form-label fw-bold">Ad&shy;ver&shy;tise&shy;ment text</label>
            <div class="col-sm-10">
                <textarea rows="5" type="text" name="tweet" class="form-control @error('tweet') is-invalid @enderror"
                    id="tweet">{{ old('_token') ? old('tweet') : $profile?->tweet }}</textarea>
                @error('tweet')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div id="tweetHelp" class="form-text">
                    A short text which the Dealers' Den team can publish on Twitter and Telegram. By filling in this
                    field, you agree that your data will be used publicly for advertisement.
                    <br />
                    You might want to include your Twitter/Telegram handle or a website.
                </div>
            </div>
        </div>
    </div>
</div>
