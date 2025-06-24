<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Orchid\Layouts\User\ProfilePasswordLayout;
use App\Orchid\Layouts\User\UserEditLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Orchid\Access\Impersonation;
use App\Models\User;
use App\Orchid\Layouts\User\UserReferralLayout;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserProfileScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     *
     * @return array
     */
    public function query(Request $request): iterable
    {
        return [
            'user' => $request->user(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'My Account';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Update your account details such as name, email address and password';
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Back to my account')
                ->novalidate()
                ->canSee(Impersonation::isSwitch())
                ->icon('bs.people')
                ->route('platform.switch.logout'),

            Link::make('KYC')
                ->novalidate()
                ->icon('bs.file-earmark-text')
                ->route('platform.user.kyc')
                ->canSee(auth()->user()->kyc_status === 'pending' || auth()->user()->kyc_status === 'rejected'),

            Button::make('Sign out')
                ->novalidate()
                ->icon('bs.box-arrow-left')
                ->route('platform.logout'),

        ];
    }

    /**
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::block(UserReferralLayout::class)
                ->title(__('Referral Information'))
                ->description(__("Referral Code"))
                ->commands(
                    Button::make(__('Copy'))
                        ->type(Color::BASIC())
                        ->id('copy-referral-btn')
                        ->icon('bs.copy')
                        ->rawAttributes([
                            'type' => 'button',
                            'onclick' => 'copyReferralCode()',
                        ])
                ),

            Layout::block(UserEditLayout::class)
                ->title(__('Profile Information'))
                ->description(__("Update your account's profile information and email address."))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::BASIC())
                        ->icon('bs.check-circle')
                        ->method('save')
                ),

            Layout::block(ProfilePasswordLayout::class)
                ->title(__('Update Password'))
                ->description(__('Ensure your account is using a long, random password to stay secure.'))
                ->commands(
                    Button::make(__('Update password'))
                        ->type(Color::BASIC())
                        ->icon('bs.check-circle')
                        ->method('changePassword')
                ),

            Layout::view('vendor.platform.script.custom_script')

        ];
    }

    public function save(Request $request): void
    {
        $request->validate([
            'user.name'  => 'required|string',
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($request->user()),
            ],
        ]);

        $request->user()
            ->fill($request->get('user'))
            ->save();

        Toast::info(__('Profile updated.'));
    }

    public function changePassword(Request $request): void
    {
        $guard = config('platform.guard', 'web');
        $request->validate([
            'old_password' => 'required|current_password:' . $guard,
            'password'     => 'required|confirmed|different:old_password',
        ]);

        tap($request->user(), function ($user) use ($request) {
            $user->password = Hash::make($request->get('password'));
        })->save();

        Toast::info(__('Password changed.'));
    }

    public function script(): string
    {
        return <<<'JS'
            function copyReferralCode() {
                var input = document.querySelector('input[name="user.referral_code"]');
                if (input) {
                    input.select();
                    document.execCommand("copy");
                    window.getSelection().removeAllRanges();
                    window.platform.toast("Referral code copied!");
                }
            }
        JS;
    }
}
