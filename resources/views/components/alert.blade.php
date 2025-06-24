<a href="{{ route('platform.user.kyc') }}"
   class="m-auto d-flex align-items-center btn btn-link position-relative px-1 py-0 h-100 link-body-emphasis"
   data-controller="kyc-alert"
   {{-- data-kyc-status="{{ $kycStatus ?? 'pending' }}" --}}
>
    <x-orchid-icon path="bs.exclamation-triangle" class="text-danger" />

    <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger lh-1 aspect-ratio-1x1"
            title="KYC Pending"></span>
</a>