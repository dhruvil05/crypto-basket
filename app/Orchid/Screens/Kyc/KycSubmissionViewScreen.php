<?php

namespace App\Orchid\Screens\Kyc;

use App\Models\KycSubmission;
use App\Models\User;
use App\Orchid\Layouts\Kyc\KycStatusLayout;
use App\Orchid\Layouts\Kyc\KycSubmissionImgLayout;
use App\Orchid\Layouts\Kyc\KycSubmissionViewLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Color;
use Orchid\Support\Facades\Toast;

class KycSubmissionViewScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Request $request, $id): iterable
    {
        $kycData = KycSubmission::findOrFail($id);
        return [
            'kyc_data' => $kycData->toArray(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'KYC Details';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::block(KycSubmissionViewLayout::class)
                ->title(__('Bank Information'))
                ->description(__("Update your bank information.")),
                
            Layout::block(KycSubmissionImgLayout::class)
                ->title(__('KYC Documents'))
                ->description(__("Uploaded KYC documents")),

            Layout::block(KycStatusLayout::class)
                ->title(__('Status Update'))
                ->description(__("Update KYC status."))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::BASIC())
                        ->icon('bs.check-circle')
                        ->method('updateKycStatus')
                ),

        ];
    }

    public function updateKycStatus(Request $request, $id)
    {
        $request->validate([
            'kyc_data.status' => 'required|in:pending,approved,rejected',
        ]);
        $status = $request->input('kyc_data.status');
        $kycSubmission = KycSubmission::findOrFail($id);
        $kycSubmission->update([
            'status' => $status,
        ]);
        
        // change user's kyc_status
        $user = User::findOrFail($kycSubmission->user_id);
        $user->kyc_status = $status;
        $user->save();

        // Flash a success message
        Toast::info("{$user->name} Kyc {$status}.");
        return redirect()->route('platform.user.kyc.requests.view', ['id' => $id]);
    }
}
