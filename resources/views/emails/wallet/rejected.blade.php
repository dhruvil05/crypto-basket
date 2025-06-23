@component('mail::message')
# Deposit Rejected

Hello {{ $transaction->user->name }},

Your wallet deposit of ₹{{ $transaction->amount }} has been **rejected**.

@if($transaction->admin_comment)
**Reason:** {{ $transaction->admin_comment }}
@endif

You may try again or contact support if you believe this was an error.

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
