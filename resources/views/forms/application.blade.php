<div class="card">
    <div class="card-body">
        <div class="card-title">
            <h4>General Info</h4>
            <p>This will be used for contact, display or notification purposes.</p>
        </div>
        <!-- Hidden --->
        <input type="hidden" name="code" value="{{ $code }}">
        <input type="hidden" name="applicationType" value="{{ $applicationType->value }}">
        @if(!is_null($application->type) && $application->type !== $applicationType)
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="alert alert-warning">
                        You are changing your Role away from
                        <b>Dealer</b> this means you will loose your previous application as a dealer and join another dealer as a {{ $applicationType->value }}.
                    </div>
                </div>
            </div>
        @endif
        <!-- ROLE --->
        <div class="row mb-3">
            <label for="email" class="col-sm-2 col-form-label fw-bold">Role</label>
            <div class="col-sm-10 col-form-label">
                @if(!is_null($application->type) && $application->type !== $applicationType)
                    Updates
                    <span
                        class="badge bg-primary">{{ \Illuminate\Support\Str::ucfirst($application->type->value) }}</span>
                    to
                @endif
                <span class="badge bg-primary">{{ \Illuminate\Support\Str::ucfirst($applicationType->value) }}</span>
            </div>
        </div>
        <!-- EMAIL --->
        <div class="row mb-3">
            <label for="email" class="col-sm-2 col-form-label fw-bold">Email</label>
            <div class="col-sm-10">
                <input type="email" disabled value="{{ Auth::user()->email }}"
                       class="form-control @error('email') is-invalid @enderror" id="email">
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div id="emailHelp"
                     class="form-text">We will contact you by email on any updates to your application. Your email will not be shared publicly. Your email can only be changed in the Identity Provider.
                </div>
            </div>
        </div>
        @if($applicationType !== \App\Enums\ApplicationType::Assistant)
            <div class="row mb-3">
                <label for="displayName" class="col-sm-2 col-form-label fw-bold">Display Name</label>
                <div class="col-sm-10">
                    <input type="text" name="displayName"
                           value="{{ old('displayName') ?? $application?->display_name }}"
                           class="form-control @error('displayName') is-invalid @enderror" id="displayName">
                    @error('displayName')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="displayNameHelp" class="form-text">
                        If you'd like to appear under a name different from your nickname (e.g., a company name) in the Dealer's Den, please enter this name here. Leave the field blank to appear under your nickname.
                        <b>Hint: If you and your must-have neighbor enter the same display name, it will show up as one large table in the seating plan.</b>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="website" class="col-sm-2 col-form-label fw-bold">Website</label>
                <div class="col-sm-10">
                    <input type="text" name="website" value="{{ old('website') ?? $application?->website }}"
                           class="form-control @error('website') is-invalid @enderror" id="website">
                    @error('website')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="websiteHelp" class="form-text">
                        If you have a website or preferred gallery, please tell us the the link so we can learn a bit more about you.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="merchandise" class="col-sm-2 col-form-label fw-bold">Merchandise/Service</label>
                <div class="col-sm-10">
                    <input type="text" name="merchandise" value="{{ old('merchandise') ?? $application?->merchandise }}"
                           class="form-control @error('merchandise') is-invalid @enderror" id="merchandise">
                    @error('merchandise')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="merchandiseHelp" class="form-text">
                        Please explain what kind of merchandise you are planning to sell, or what kind of services you are going to provide; i.e., prints, badge commissions, comics, etc.
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-sm-10 offset-sm-2">
                    <div class="form-check">
                        <input class="form-check-input @error('mature') is-invalid @enderror" name="mature"
                               @checked(($application?->mature === "on" && empty(old('mature'))) || (!empty(old('mature')) && old('mature') === "on"))
                               type="checkbox" id="mature">
                        <label class="form-check-label" for="mature">
                            Tick this checkbox if you are planning to sell art or merchandise with
                            <b>mature</b> content.
                        </label>
                        @error('mature')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        @endif

        @if($applicationType === \App\Enums\ApplicationType::Dealer)
            <div class="card-title">
                <h4>Your space</h4>
                <p>Please let us know what kind of space you would like. This info will be used for table assignment.</p>
            </div>

            <fieldset class="row mb-4">
                <legend class="col-form-label fw-bold col-sm-2 pt-0">Location</legend>
                <div class="col-sm-10">
                    <div class="form-check">
                        <input class="form-check-input @error('denType') is-invalid @enderror" type="radio"
                               name="denType" id="denTypeRegular"
                               value="denTypeRegular"
                            @checked(($application?->is_afterdark === false && empty(old('denType'))) || (!empty(old('denType')) && old('denType') === "denTypeRegular"))>
                        <label class="form-check-label" for="denTypeRegular">
                            Regular Dealers' Den
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('denType') is-invalid @enderror" type="radio"
                               name="denType" id="denTypeAfterDark"
                               value="denTypeAfterDark"
                            @checked(($application?->is_afterdark === true && empty(old('denType'))) || (!empty(old('denType')) && old('denType') === "denTypeAfterDark")) >
                        <label class="form-check-label" for="denTypeAfterDark">
                            After Dark Dealers' Den
                        </label>
                        @error('denType')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div id="denTypeHelp" class="form-text">
                        Please choose if you would like a table in the regular Dealers' Den or if you would like to display adult material openly in an After Dark Dealers' Den.
                    </div>
                </div>
            </fieldset>
            <div class="row mb-3">
                <label for="space" class="col-sm-2 col-form-label fw-bold">Required Space</label>
                <div class="col-sm-10">
                    <select name="space" id="space" class="form-select @error('space') is-invalid @enderror">
                        @foreach($table_types as $type)
                            <option
                                value="{{ $type['id'] }}"
                            @selected(($application?->table_type_requested === $type['id'] && empty(old('space'))) || (!empty(old('denType')) && old('space',$type['id'] === 2)))>{{ $type['name'] }}</option>
                        @endforeach
                    </select>
                    @error('space')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="spaceHelp" class="form-text">
                        Select your preferred dealership package - how much space you'll likely need to sell your wares. Please don't request more than you really need, and note that anything larger than a Full package will require a reasonable explanation in the comments field.
                        <b>If you plan to share a table, request the amount of space you and your partners need, after your submitted your application you can invite your partner!</b>
                    </div>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-sm-10 offset-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" name="wallseat"
                               @checked(old('wallseat') ?? $application?->is_wallseat === true) type="checkbox"
                               id="wallseat">
                        <label class="form-check-label" for="wallseat">
                            <b>Wall preferred:</b> Tick this checkbox if you would prefer to sit at a table in front of a wall.
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-10 offset-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" name="power"
                               @checked(old('power') ?? $application?->is_power === true) type="checkbox"
                               id="power">
                        <label class="form-check-label" for="power">
                            <b>Power&nbsp;Socket&nbsp;Needed:</b> Tick this checkbox if you need a german 230V power socket at your table.
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="wanted" class="col-sm-2 col-form-label fw-bold">Preferred&nbsp;Neighbors</label>
                <div class="col-sm-10">
                    <textarea rows="5" type="text" name="wanted"
                              class="form-control @error('wanted') is-invalid @enderror"
                              id="wanted">{{ old('wanted') ?? $application?->unwanted_neighbors }}</textarea>
                    @error('wanted')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="wantedHelp" class="form-text">
                        Please list the nicknames and (ideally) registration numbers of other dealers who you'd prefer to sit close to.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="unwanted"
                       class="col-sm-2 col-form-label fw-bold">Avoid&nbsp;As&nbsp;Neighbors</label>
                <div class="col-sm-10">
                    <textarea rows="5" type="text" name="unwanted"
                              class="form-control @error('unwanted') is-invalid @enderror"
                              id="unwanted">{{ old('unwanted') ?? $application?->unwanted_neighbors }}</textarea>
                    @error('unwanted')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="unwantedHelp" class="form-text">
                        Please list the nicknames and (ideally) registration numbers of other dealers who you'd rather keep your distance to. Don't worry, it's kept confidential.
                    </div>
                </div>
            </div>
        @endif

        @if($applicationType === \App\Enums\ApplicationType::Dealer || $applicationType === \App\Enums\ApplicationType::Share)
            <div class="card-title">
                <h4>Profile</h4>
                <p>The following information will be used to present you and your merchandise in the EF App,
                   			as well as possibly other media in the context of Eurofurence.</p>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label fw-bold">Attendance</label>
             </div>
            <div class="row mb-1">
                <div class="col-sm-10 offset-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" name="attends_thu"
                               @checked(old('attends_thu') ?? $profile?->attends_thu === true) type="checkbox"
                               id="attends_thu">
                        <label class="form-check-label" for="attends_thu">
                            Day 1
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-sm-10 offset-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" name="attends_fri"
                               @checked(old('attends_fri') ?? $profile?->attends_fri === true) type="checkbox"
                               id="attends_fri">
                        <label class="form-check-label" for="attends_fri">
                            Day 2
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-sm-10 offset-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" name="attends_sat"
                               @checked(old('attends_sat') ?? $profile?->attends_sat === true) type="checkbox"
                               id="attends_sat">
                        <label class="form-check-label" for="attends_sat">
                            Day 3
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-sm-10 offset-sm-2">
                    <div id="attendanceHelp"
                        class="form-text">Please check all days on which you plan to attend the Dealer' Den.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label fw-bold">Categories</label>
             </div>
            <div class="row mb-1">
                <div class="col-sm-10 offset-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" name="is_print"
                               @checked(old('is_print') ?? $profile?->is_print === true) type="checkbox"
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
                        <input class="form-check-input" name="is_artwork"
                               @checked(old('is_artwork') ?? $profile?->is_artwork === true) type="checkbox"
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
                        <input class="form-check-input" name="is_fursuit"
                               @checked(old('is_fursuit') ?? $profile?->is_fursuit === true) type="checkbox"
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
                        <input class="form-check-input" name="is_commissions"
                               @checked(old('is_commissions') ?? $profile?->is_commissions === true) type="checkbox"
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
                        <input class="form-check-input" name="is_misc"
                               @checked(old('is_misc') ?? $profile?->is_misc === true) type="checkbox"
                               id="is_misc">
                        <label class="form-check-label" for="is_misc">
                            Miscellaneous merchandise
                        </label>
                    </div>
                </div>
            </div>
             <div class="row mb-1">
                <div class="col-sm-10 offset-sm-2">
                    <div id="attendanceHelp"
                        class="form-text">Select the categories that apply to your goods to make it easier to find you!
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="image_thumbnail" class="col-sm-2 col-form-label fw-bold">Thumbnail</label>
                <div class="col-sm-10">
                    <input id="image_thumbnail" type="file"
                        class="form-control @error('image_thumbnail') is-invalid @enderror"
                        name="image_thumbnail" value="{{ old('image_thumbnail') ?? $profile?->image_thumbnail }}"
                        onchange="document.getElementById('image_thumbnail_preview').src = window.URL.createObjectURL(this.files[0]); document.getElementById('image_thumbnail_preview').hidden=''">
                    <div id="image_thumbnailHelp"
                        class="form-text">Upload an image to be shown next to your name in the dealer list. This image should have a size of 60&times;60 pixels.
                    </div>
                    @if ($profile?->image_thumbnail != NULL && file_exists(public_path('/images/upload/'.$profile?->image_thumbnail)))
                        <img id='image_thumbnail_preview' src="{{ asset('/images/upload/'.$profile?->image_thumbnail)}}" style="height: 100px;">
                    @else
                        <img id='image_thumbnail_preview' src="{{ asset('/images/profile/placeholder.png')}}" style="height: 100px;" hidden='hidden'>
                    @endif
                    @error('image_thumbnail')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
             </div>
            <div class="row mb-3">
                <label for="short_desc" class="col-sm-2 col-form-label fw-bold">Short&nbsp;Description</label>
                <div class="col-sm-10">
                    <textarea rows="5" type="text" name="short_desc"
                              class="form-control @error('short_desc') is-invalid @enderror"
                              id="short_desc">{{ old('short_desc') ?? $profile?->short_desc }}</textarea>
                    <div id="short_descHelp" class="form-text">
                        A short description, 1 - 3 sentences, to appear in the dealer list next to your thumbnail image.
                    </div>
                </div>
             </div>
             <div class="row mb-3">
                <label for="image_artist" class="col-sm-2 col-form-label fw-bold">Artist Image</label>
                <div class="col-sm-10">
                    <input id="image_artist" type="file"
                        class="form-control @error('image_artist') is-invalid @enderror"
                        name="image_artist" value="{{ old('image_artist') ?? $profile?->image_artist }}"
                        onchange="document.getElementById('image_artist_preview').src = window.URL.createObjectURL(this.files[0]); document.getElementById('image_artist_preview').hidden=''">
                    <div id="image_artistHelp"
                        class="form-text">You can upload a preview image of your art or merchandise, which will be shown on a separate page in the EF app. The size of this image should be 400&times;400 pixels.
                    </div>
                    @if ($profile?->image_artist != NULL && file_exists(public_path('/images/upload/'.$profile?->image_artist)))
                        <img id='image_artist_preview' src="{{ asset('/images/upload/'.$profile?->image_artist)}}" style="height: 100px;">
                    @else
                        <img id='image_artist_preview' src="{{ asset('/images/profile/placeholder.png')}}" style="height: 100px;" hidden='hidden'>
                    @endif
                    @error('image_artist')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="artist_desc" class="col-sm-2 col-form-label fw-bold">About&nbsp;the&nbsp;Artist</label>
                <div class="col-sm-10">
                    <textarea rows="5" type="text" name="artist_desc"
                              class="form-control @error('artist_desc') is-invalid @enderror"
                              id="artist_desc">{{ old('artist_desc') ?? $profile?->artist_desc }}</textarea>
                    <div id="artist_descHelp" class="form-text">
                        Information about yourself; e.g., your background, how you became an artist,...
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="image_art" class="col-sm-2 col-form-label fw-bold">Art Preview</label>
                <div class="col-sm-10">
                    <input id="image_art" type="file"
                        class="form-control @error('image_art') is-invalid @enderror"
                        name="image_art" value="{{ old('image_art') ?? $profile?->image_art }}"
                        onchange="document.getElementById('image_art_preview').src = window.URL.createObjectURL(this.files[0]); document.getElementById('image_art_preview').hidden=''">
                    <div id="image_artHelp"
                        class="form-text">This image is shown on your dedicated page in the EF app. Aim for a size of 400&times;450 pixels.
                    </div>
                    @if ($profile?->image_art != NULL && file_exists(public_path('/images/upload/'.$profile?->image_art)))
                        <img id='image_art_preview' src="{{ asset('/images/upload/'.$profile?->image_art)}}" style="height: 100px;">
                    @else
                        <img id='image_art_preview' src="{{ asset('/images/profile/placeholder.png')}}" style="height: 100px;" hidden='hidden'>
                    @endif
                    @error('image_art')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="art_preview_caption" class="col-sm-2 col-form-label fw-bold">Art Preview Caption</label>
                <div class="col-sm-10">
                    <textarea rows="1" type="text" name="art_preview_caption"
                              class="form-control @error('art_preview_caption') is-invalid @enderror"
                              id="art_preview_caption">{{ old('art_preview_caption') ?? $profile?->art_preview_caption }}</textarea>
                    <div id="art_preview_captionHelp" class="form-text">
                        If you have uploaded an art/merchandise preview image above, you can enter a description that is displayed beneath it.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="art_desc" class="col-sm-2 col-form-label fw-bold">About&nbsp;the&nbsp;Art</label>
                <div class="col-sm-10">
                    <textarea rows="5" type="text" name="art_desc"
                              class="form-control @error('art_desc') is-invalid @enderror"
                              id="art_desc">{{ old('art_desc') ?? $profile?->art_desc }}</textarea>
                    <div id="art_descHelp" class="form-text">
                        Information about your art or other merchandise. This is also the place for details about commissions, prices, and other things worthwhile to know.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="profile_website" class="col-sm-2 col-form-label fw-bold">Website</label>
                <div class="col-sm-10">
                    <textarea rows="1" type="text" name="profile_website"
                              class="form-control @error('profile_website') is-invalid @enderror"
                              id="profile_website">{{ old('profile_website') ?? $profile?->website }}</textarea>
                    <div id="websiteHelp" class="form-text">
                        The address of your homepage.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="twitter" class="col-sm-2 col-form-label fw-bold">Twitter</label>
                <div class="col-sm-10">
                    <textarea rows="1" type="text" name="twitter"
                              class="form-control @error('twitter') is-invalid @enderror"
                              id="twitter">{{ old('twitter') ?? $profile?->twitter }}</textarea>
                    <div id="twitterHelp" class="form-text">
                        Your Twitter handle.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="telegram" class="col-sm-2 col-form-label fw-bold">Telegram</label>
                <div class="col-sm-10">
                    <textarea rows="1" type="text" name="telegram"
                              class="form-control @error('telegram') is-invalid @enderror"
                              id="telegram">{{ old('telegram') ?? $profile?->telegram }}</textarea>
                    <div id="telegramHelp" class="form-text">
                        Your Telegram handle.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="discord" class="col-sm-2 col-form-label fw-bold">Discord</label>
                <div class="col-sm-10">
                    <textarea rows="1" type="text" name="discord"
                              class="form-control @error('discord') is-invalid @enderror"
                              id="discord">{{ old('discord') ?? $profile?->discord }}</textarea>
                    <div id="discordHelp" class="form-text">
                        Your Discord handle.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="tweet" class="col-sm-2 col-form-label fw-bold">Advertisement text</label>
                <div class="col-sm-10">
                    <textarea rows="5" type="text" name="tweet"
                              class="form-control @error('tweet') is-invalid @enderror"
                              id="tweet">{{ old('tweet') ?? $profile?->tweet }}</textarea>
                    <div id="tweetHelp" class="form-text">
                        A short text which the Dealers' Den team can publish on Twitter and Telegram. By filling this field, you agree that your data will be used publicly for advertisement.
                        <br/>
                        You might want to include your Twitter/Telegram handle or a website.
                    </div>
                </div>
            </div>

        @endif

        <div class="row mb-3">
            <label for="comment" class="col-sm-2 col-form-label fw-bold">Comments</label>
            <div class="col-sm-10">
                    <textarea rows="5" type="text" name="comment"
                              class="form-control @error('comment') is-invalid @enderror"
                              id="comment">{{ old('comment') ?? $application?->comment }}</textarea>
                @error('comment')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div id="commentHelp" class="form-text">
                    If you have any additional things to tell us, please write them down here.
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-10 offset-sm-2">
                <div class="form-check">
                    <input class="form-check-input @error('tos') is-invalid @enderror" name="tos"
                           @checked(old('tos') ?? Route::is('applications.edit'))
                           @disabled(Route::is('applications.edit'))
                           type="checkbox"
                           id="tos">
                    <label class="form-check-label" for="tos">
                        <b>I confirm that I have read and agree to abide by the Dealers' Den Terms of Service.</b>
                    </label>
                    @error('tos')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="unwantedHelp" class="form-text">
                        Click <a href="https://www.eurofurence.org/EF27/dealersden/"
                                 target="_blank"
                                 onclick="window.open(this.href, 'toswin', 'width=600,toolbar=0,resizable=1,scrollbars=1'); return false;">this link</a> to open the Dealers' Den Terms of Service in a new window or a new tab.
                    </div>
                </div>
            </div>

        </div>
        @csrf
        @if(Route::is('applications.create'))
            <button class="w-100 btn btn-primary btn-lg mt-4" type="submit">Submit your application</button>
        @else
            <button class="w-100 btn btn-primary btn-lg mt-4" type="submit">Update your application</button>
        @endif
    </div>
</div>
