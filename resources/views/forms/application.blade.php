<div class="card card-body mt-2">
    <div class="card-body">
        <div class="card-title">
            <h4>General Info</h4>
            <p>This information will be used to contact you, display your information, or send you notifications as needed.</p>
        </div>
        <!-- Hidden --->
        <input type="hidden" name="code" value="{{ $code }}">
        <input type="hidden" name="applicationType" value="{{ $applicationType->value }}">
        @if (!is_null($application->type) && $application->type !== $applicationType)
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="alert alert-warning">
                        You are changing your Role away from
                        <b>Dealer</b> this means you will lose your previous application as a dealer and join another
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
                        value="{{ old('_token') ? old('displayName') : $application?->display_name }}"
                        class="form-control @error('displayName') is-invalid @enderror" id="displayName"
                        @disabled(Carbon\Carbon::parse(config('ef.reg_end_date'))->isPast())>
                    @error('displayName')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="displayNameHelp" class="form-text">
                        If you'd like to appear under a name different from your nickname (e.g., a company name) in the Dealers' Den, please enter this name here. Leave the field blank to appear under your nickname.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="website" class="col-sm-2 col-form-label fw-bold">Website/Portfolio</label>
                <div class="col-sm-10">
                    <input type="text" name="website" placeholder="https://yourprofile.example.com/itsme"
                        value="{{ old('_token') ? old('website') : $application?->website }}"
                        class="form-control @error('website') is-invalid @enderror" id="website"
                        @disabled(Carbon\Carbon::parse(config('ef.reg_end_date'))->isPast())>
                    @error('website')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="websiteHelp" class="form-text">
                        We would be delighted to explore your website, gallery, or portfolio! Please share the link so that we can appreciate your work and gain a deeper understanding of your unique style and offerings.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="merchandise" class="col-sm-2 col-form-label fw-bold">Merchandise/Service</label>
                <div class="col-sm-10">
                    <input type="text" name="merchandise"
                        value="{{ old('_token') ? old('merchandise') : $application?->merchandise }}"
                        class="form-control @error('merchandise') is-invalid @enderror" id="merchandise"
                        @disabled(Carbon\Carbon::parse(config('ef.reg_end_date'))->isPast())>
                    @error('merchandise')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="merchandiseHelp" class="form-text">
                        Please provide details about the merchandise or services you plan to offer at the Dealers' Den. Examples of items may include prints, badge commissions, comics, or other related goods or services. This information will assist us in better understanding your offerings and assist with placement within the Dealer’s Den.
                    </div>
                </div>
            </div>
        @endif

        @if ($applicationType === \App\Enums\ApplicationType::Dealer)
            <div class="card-title mt-5">
                <h4>Your Dealership Package</h4>
                <p>
                    Please choose your preferred location and table size in the Dealers' Den. You can customize your Dealership Package with additional options. This information will be used to assign your table.
                </p>
            </div>

            <fieldset class="row mb-4" @disabled(Carbon\Carbon::parse(config('ef.reg_end_date'))->isPast())>
                <legend class="col-form-label fw-bold col-sm-2 pt-0">Location</legend>
                <div class="col-sm-10">
                    <div class="form-check">
                        <input class="form-check-input @error('denType') is-invalid @enderror" type="radio"
                            name="denType" id="denTypeRegular" value="denTypeRegular" @checked(
                                old('_token') ?  old('denType') === 'denTypeRegular' : $application?->is_afterdark === false)>
                        <label class="form-check-label" for="denTypeRegular">
                            Dealers’ Den (Rated PG-13 with natural nudity)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('denType') is-invalid @enderror" type="radio"
                            name="denType" id="denTypeAfterDark" value="denTypeAfterDark" @checked(
                                old('_token') ?  old('denType') === 'denTypeAfterDark' : $application?->is_afterdark === true)>
                        <label class="form-check-label" for="denTypeAfterDark">
                            After Dark Dealers’ Den (Rated R)
                        </label>
                        @error('denType')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div id="denTypeHelp" class="form-text">
                        Please choose if you would like to be placed in the regular Dealers' Den or if you would like to display adult material openly in the After Dark Dealers' Den.
                    </div>
                </div>
            </fieldset>
            <div class="row mb-3">
                <label for="space" class="col-sm-2 col-form-label fw-bold">Table size</label>
                <div class="col-sm-10">
                    <select name="space" id="space" class="form-select @error('space') is-invalid @enderror"
                        @disabled(Carbon\Carbon::parse(config('ef.reg_end_date'))->isPast())>
                        @foreach ($table_types as $type)
                            <option value="{{ $type['id'] }}" @selected(old('space', $application?->table_type_requested ?? (new \App\Models\Application())->table_type_requested) == $type['id'])>{{ $type['name'] . ' - '.  $type['price']/100 . ' EUR'}}
                            </option>
                        @endforeach
                    </select>
                    @error('space')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="spaceHelp" class="form-text">
                        Please select the Dealership package that best suits your needs for selling your wares. We kindly ask that you avoid requesting more space than necessary, as packages larger than Full require additional information in the comments section.
                        <b>If you plan to share a table with one or more partners, please request the appropriate amount of space needed for all individuals. After submitting your application, you will be able to invite your partner(s) to join you.</b>
                    </div>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-sm-10 offset-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" name="wallseat" @checked(old('_token') ? old('wallseat') : $application?->is_wallseat === true) type="checkbox"
                            id="wallseat" @disabled(Carbon\Carbon::parse(config('ef.reg_end_date'))->isPast())>
                        <label class="form-check-label" for="wallseat">
                            <b>Wall preferred</b>
                        </label>
                        <div id="wallseatHelp" class="form-text">
                            If you would prefer to sit at a table in front of a wall, please select this option.
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-10 offset-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" name="power" @checked(old('_token') ? old('power') : $application?->is_power === true) type="checkbox"
                            id="power" @disabled(Carbon\Carbon::parse(config('ef.reg_end_date'))->isPast())>
                        <label class="form-check-label" for="power">
                            <b>Increased power demand</b>
                        </label>
                        <div id="powerHelp" class="form-text">
                            If you require more electrical power than the average 100 Watts per dealer provided by the Dealers' Den for low-power devices (i.e. laptop or phone charger), please select this option to let us know about your increased energy needs.
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="wanted" class="col-sm-2 col-form-label fw-bold">Preferred&nbsp;Neighbors</label>
                <div class="col-sm-10">
                    <textarea rows="5" type="text" name="wanted" class="form-control @error('wanted') is-invalid @enderror"
                        @disabled(Carbon\Carbon::parse(config('ef.reg_end_date'))->isPast()) id="wanted">{{ old('_token') ? old('wanted') : $application?->wanted_neighbors }}</textarea>
                    @error('wanted')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="wantedHelp" class="form-text">
                        Please provide us with the <b>nicknames</b> and/or <b>dealership names</b> of any other dealers or dealerships that you would prefer to have your table placed next to. This will help us to accommodate your preferences and make your experience at the Dealers' Den as enjoyable as possible.
                    </div>
                </div>
            </div>
        @endif

        <div class="row mb-3">
            <label for="comment" class="col-sm-2 col-form-label fw-bold">Comments</label>
            <div class="col-sm-10">
                <textarea rows="5" type="text" name="comment" class="form-control @error('comment') is-invalid @enderror"
                    @disabled(Carbon\Carbon::parse(config('ef.reg_end_date'))->isPast()) id="comment">{{ old('_token') ? old('comment') : $application?->comment }}</textarea>
                @error('comment')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div id="commentHelp" class="form-text">
                    If you have any further information you would like to share with Dealers' Den Management, please use the space provided below. This can include requests for special accommodations, questions about the application process, or any other relevant details.
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
                    <div id="tosHelp" class="form-text">
                        Click <a href="{{ config('ef.dealers_tos_url') }}" target="_blank"
                            onclick="window.open(this.href, 'toswin', 'width=600,toolbar=0,resizable=1,scrollbars=1'); return false;">this
                            link</a> to open the Dealers' Den Terms of Service in a new window or a new tab.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
