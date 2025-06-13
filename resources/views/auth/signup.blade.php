<div class="mb-3">
    <label class="form-label">
        {{__('Name')}}
    </label>
    {!!  \Orchid\Screen\Fields\Input::make('name')
        ->type('text')
        ->required()
        ->tabindex(1)
        ->autofocus()
        ->autocomplete('name')
        ->placeholder(__('Enter your name'))
    !!}
</div>

<div class="mb-3">
    <label class="form-label">
        {{__('Email address')}}
    </label>
    {!!  \Orchid\Screen\Fields\Input::make('email')
        ->type('email')
        ->required()
        ->tabindex(2)
        ->autofocus()
        ->autocomplete('email')
        ->inputmode('email')
        ->placeholder(__('Enter your email'))
    !!}
</div>

<div class="mb-3">
    <label class="form-label w-100">
        {{__('Password')}}
    </label>
    {!!  \Orchid\Screen\Fields\Password::make('password')
        ->required()
        ->autocomplete('current-password')
        ->tabindex(3)
        ->placeholder(__('Enter your password'))
    !!}
</div>

<div class="mb-3">
    <label class="form-label w-100">
        {{__('Confirm Password')}}
    </label>
    {!!  \Orchid\Screen\Fields\Password::make('password_confirmation')
        ->required()
        ->autocomplete('off')
        ->tabindex(4)
        ->placeholder(__('Confirm your password'))
    !!}
</div>

<div class="mb-3">
    <label class="form-label w-100">
        {{__('Referral Code (optional)')}}
    </label>
    {!!  \Orchid\Screen\Fields\Input::make('referral_code')
        ->tabindex(5)
        ->autocomplete('off')
        ->placeholder(__('Enter your referral code'))
    !!}
</div>

<div class="row align-items-center">
    <div class="col-md-6 col-xs-12">
        <label class="form-check">
            <input type="hidden" name="remember">
            <input type="checkbox" name="remember" value="true"
                   class="form-check-input" {{ !old('remember') || old('remember') === 'true'  ? 'checked' : '' }}>
            <span class="form-check-label"> {{__('Remember Me')}}</span>
        </label>
    </div>
    <div class="col-md-6 col-xs-12">
        <button id="button-login" type="submit" class="btn btn-default btn-block" tabindex="6">
            <x-orchid-icon path="bs.box-arrow-in-right" class="small me-2"/>
            {{__('Sign Up')}}
        </button>
    </div>
</div>
