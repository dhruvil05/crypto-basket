@extends('platform::auth')
@section('title',__('Sign up to create account'))

@section('content')
    <h1 class="h4 text-body-emphasis mb-4">{{__('Sign up to create account')}}</h1>

    <form class="m-t-md"
          role="form"
          method="POST"
          data-controller="form"
          data-form-need-prevents-form-abandonment-value="false"
          data-action="form#submit"
          action="{{ route('platform.register.auth') }}">
        @csrf

        @include('auth.signup')
    </form>
@endsection
