<div class="card card-body mt-2">
    <div class="card-body">
        <div class="card-title">
            <h4>General Info</h4>
            <p>This will be used for contact, display or notification purposes.</p>
        </div>
        <!-- Hidden --->
        <input type="hidden" name="code" value="{{ $code }}">
        <input type="hidden" name="applicationType" value="{{ $applicationType->value }}">
        @if (!is_null($application->type) && $application->type !== $applicationType)
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="alert alert-warning">
                        You are changing your Role away from
                        <b>Dealer</b> this means you will loose your previous application as a dealer and join another
                        dealer as a {{ $applicationType->value }}.
                    </div>
                </div>
            </div>
        @endif
        <!-- ROLE --->
        <div class="row mb-3">
            <label for="email" class="col-sm-2 col-form-label fw-bold">Role</label>
            <div class="col-sm-10 col-form-label">
                @if (!is_null($application->type) && $application->type !== $applicationType)
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
                <div id="emailHelp" class="form-text">We will contact you by email on any updates to your application.
                    Your email will not be shared publicly. Your email can only be changed in the Identity Provider.
                </div>
            </div>
        </div>
        @if ($applicationType !== \App\Enums\ApplicationType::Assistant)
            <div class="row mb-3">
                <label for="displayName" class="col-sm-2 col-form-label fw-bold">Display Name</label>
                <div class="col-sm-10">
                    <input type="text" name="displayName"
                        value="{{ old('displayName') ?? $application?->display_name }}"
                        class="form-control @error('displayName') is-invalid @enderror" id="displayName"
                        @disabled(now() >= config('dates.reg_end_date'))>
                    @error('displayName')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="displayNameHelp" class="form-text">
                        If you'd like to appear under a name different from your nickname (e.g., a company name) in the
                        Dealer's Den, please enter this name here. Leave the field blank to appear under your nickname.
                        <b>Hint: If you and your must-have neighbor enter the same display name, it will show up as one
                            large table in the seating plan.</b>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="website" class="col-sm-2 col-form-label fw-bold">Website</label>
                <div class="col-sm-10">
                    <input type="text" name="website" value="{{ old('website') ?? $application?->website }}"
                        class="form-control @error('website') is-invalid @enderror" id="website"
                        @disabled(now() >= config('dates.reg_end_date'))>
                    @error('website')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="websiteHelp" class="form-text">
                        If you have a website or preferred gallery, please tell us the the link so we can learn a bit
                        more about you.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="merchandise" class="col-sm-2 col-form-label fw-bold">Merchandise/Service</label>
                <div class="col-sm-10">
                    <input type="text" name="merchandise"
                        value="{{ old('merchandise') ?? $application?->merchandise }}"
                        class="form-control @error('merchandise') is-invalid @enderror" id="merchandise"
                        @disabled(now() >= config('dates.reg_end_date'))>
                    @error('merchandise')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="merchandiseHelp" class="form-text">
                        Please explain what kind of merchandise you are planning to sell, or what kind of services you
                        are going to provide; i.e., prints, badge commissions, comics, etc.
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-sm-10 offset-sm-2">
                    <div class="form-check">
                        <input class="form-check-input @error('mature') is-invalid @enderror" name="mature"
                            @checked(($application?->mature === 'on' && empty(old('mature'))) || (!empty(old('mature')) && old('mature') === 'on')) type="checkbox" id="mature" @disabled(now() >= config('dates.reg_end_date'))>
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

        @if ($applicationType === \App\Enums\ApplicationType::Dealer)
            <div class="card-title">
                <h4>Your space</h4>
                <p>Please let us know what kind of space you would like. This info will be used for table assignment.
                </p>
            </div>

            <fieldset class="row mb-4" @disabled(now() >= config('dates.reg_end_date'))>
                <legend class="col-form-label fw-bold col-sm-2 pt-0">Location</legend>
                <div class="col-sm-10">
                    <div class="form-check">
                        <input class="form-check-input @error('denType') is-invalid @enderror" type="radio"
                            name="denType" id="denTypeRegular" value="denTypeRegular" @checked(
                                ($application?->is_afterdark === false && empty(old('denType'))) ||
                                    (!empty(old('denType')) && old('denType') === 'denTypeRegular'))>
                        <label class="form-check-label" for="denTypeRegular">
                            Regular Dealers' Den
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('denType') is-invalid @enderror" type="radio"
                            name="denType" id="denTypeAfterDark" value="denTypeAfterDark" @checked(
                                ($application?->is_afterdark === true && empty(old('denType'))) ||
                                    (!empty(old('denType')) && old('denType') === 'denTypeAfterDark'))>
                        <label class="form-check-label" for="denTypeAfterDark">
                            After Dark Dealers' Den
                        </label>
                        @error('denType')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div id="denTypeHelp" class="form-text">
                        Please choose if you would like a table in the regular Dealers' Den or if you would like to
                        display adult material openly in an After Dark Dealers' Den.
                    </div>
                </div>
            </fieldset>
            <div class="row mb-3">
                <label for="space" class="col-sm-2 col-form-label fw-bold">Required Space</label>
                <div class="col-sm-10">
                    <select name="space" id="space" class="form-select @error('space') is-invalid @enderror"
                        @disabled(now() >= config('dates.reg_end_date'))>
                        @foreach ($table_types as $type)
                            <option value="{{ $type['id'] }}" @selected(($application?->table_type_requested === $type['id'] && empty(old('space'))) || (!empty(old('denType')) && old('space', $type['id'] === 2)))>{{ $type['name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('space')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="spaceHelp" class="form-text">
                        Select your preferred dealership package - how much space you'll likely need to sell your wares.
                        Please don't request more than you really need, and note that anything larger than a Full
                        package will require a reasonable explanation in the comments field.
                        <b>If you plan to share a table, request the amount of space you and your partners need, after
                            your submitted your application you can invite your partner!</b>
                    </div>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-sm-10 offset-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" name="wallseat" @checked(old('wallseat') ?? $application?->is_wallseat === true) type="checkbox"
                            id="wallseat" @disabled(now() >= config('dates.reg_end_date'))>
                        <label class="form-check-label" for="wallseat">
                            <b>Wall preferred:</b> Tick this checkbox if you would prefer to sit at a table in front of
                            a wall.
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-10 offset-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" name="power" @checked(old('power') ?? $application?->is_power === true) type="checkbox"
                            id="power" @disabled(now() >= config('dates.reg_end_date'))>
                        <label class="form-check-label" for="power">
                            <b>Power&nbsp;Socket&nbsp;Needed:</b> Tick this checkbox if you need a german 230V power
                            socket at your table.
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="wanted" class="col-sm-2 col-form-label fw-bold">Preferred&nbsp;Neighbors</label>
                <div class="col-sm-10">
                    <textarea rows="5" type="text" name="wanted" class="form-control @error('wanted') is-invalid @enderror"
                        @disabled(now() >= config('dates.reg_end_date')) id="wanted">{{ old('wanted') ?? $application?->unwanted_neighbors }}</textarea>
                    @error('wanted')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="wantedHelp" class="form-text">
                        Please list the nicknames and (ideally) registration numbers of other dealers who you'd prefer
                        to sit close to.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="unwanted" class="col-sm-2 col-form-label fw-bold">Avoid&nbsp;As&nbsp;Neighbors</label>
                <div class="col-sm-10">
                    <textarea rows="5" type="text" name="unwanted" class="form-control @error('unwanted') is-invalid @enderror"
                        @disabled(now() >= config('dates.reg_end_date')) id="unwanted">{{ old('unwanted') ?? $application?->unwanted_neighbors }}</textarea>
                    @error('unwanted')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="unwantedHelp" class="form-text">
                        Please list the nicknames and (ideally) registration numbers of other dealers who you'd rather
                        keep your distance to. Don't worry, it's kept confidential.
                    </div>
                </div>
            </div>
        @endif

        <div class="row mb-3">
            <label for="comment" class="col-sm-2 col-form-label fw-bold">Comments</label>
            <div class="col-sm-10">
                <textarea rows="5" type="text" name="comment" class="form-control @error('comment') is-invalid @enderror"
                    @disabled(now() >= config('dates.reg_end_date')) id="comment">{{ old('comment') ?? $application?->comment }}</textarea>
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
                        @checked(old('tos') ?? Route::is('applications.edit')) @disabled(Route::is('applications.edit')) type="checkbox" id="tos">
                    <label class="form-check-label" for="tos">
                        <b>I confirm that I have read and agree to abide by the Dealers' Den Terms of Service.</b>
                    </label>
                    @error('tos')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="unwantedHelp" class="form-text">
                        Click <a href="https://www.eurofurence.org/EF27/dealersden/" target="_blank"
                            onclick="window.open(this.href, 'toswin', 'width=600,toolbar=0,resizable=1,scrollbars=1'); return false;">this
                            link</a> to open the Dealers' Den Terms of Service in a new window or a new tab.
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
