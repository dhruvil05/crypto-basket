<?php

namespace App\Orchid\Screens\Kyc;

use App\Models\KycSubmission;
use App\Orchid\Layouts\Kyc\KycSubmissionLayout;
use Illuminate\Http\Request;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;
use Illuminate\Support\Facades\Storage;

class KycSubmissionScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'KYC';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Save'))
                ->icon('bs.check-circle')
                ->method('save')
                ->class('btn btn-info rounded px-4 py-2 fw-bold')
                ->style('gap: 8px; transition: transform 0.2s ease;'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            KycSubmissionLayout::class
        ];
    }

    public function save(Request $request)
    {
        // Check if the kyc submission already exists
        $existingKyc = KycSubmission::where('user_id', auth()->user()->id)->first();
        if ($existingKyc && $existingKyc->status !== 'pending') {
            Toast::warning('You have already submitted your KYC. Please wait for the review.');
            return redirect()->route('platform.profile'); 
        }
        // Validate the request data
        $request->validate([
            'kyc.pan_card_img'        => 'required|array|max:2048',
            'kyc.aadhar_card_img'     => 'required|array|max:2048',
            'kyc.passport_img'        => 'nullable|array|max:2048',
            'kyc.bank_book_img'       => 'nullable|array|max:2048',

            'kyc.bank_account_holder' => 'required|string|max:255',
            'kyc.bank_account_number' => 'required|string|max:20',
            'kyc.bank_ifsc'           => 'required|string|max:11',
            'kyc.bank_name'           => 'required|string|max:255',
        ]);

        $getPath = function ($attachmentId) {
            if (!$attachmentId) {
                return null;
            }

            $attachment = Attachment::find($attachmentId);
            if (!$attachment) {
                \Log::warning("Attachment ID {$attachmentId} not found.");
                return null;
            }

            return Storage::url($attachment->path . $attachment->name . '.' . $attachment->extension);
        };

        // fullpath of document files
        $panImg = $getPath($request->input('kyc.pan_card_img')[0] ?? null);
        $aadharImg = $getPath($request->input('kyc.aadhar_card_img')[0] ?? null);
        $passportImg = $getPath($request->input('kyc.passport_img')[0] ?? null);
        $bankBookImg = $getPath($request->input('kyc.bank_book_img')[0] ?? null);

        // Save or update KYC data
        KycSubmission::updateOrCreate(
            ['user_id' => auth()->user()->id],
            [
                'pan_card_img'      => $panImg,
                'aadhar_card_img'   => $aadharImg,
                'passport_img'      => $passportImg,
                'bank_book_img'     => $bankBookImg,
                'bank_account_holder' => $request->input('kyc.bank_account_holder'),
                'bank_account_number' => $request->input('kyc.bank_account_number'),
                'bank_ifsc'           => $request->input('kyc.bank_ifsc'),
                'bank_name'           => $request->input('kyc.bank_name'),
                'status'              => 'pending',
            ]
        );

        // For now, we will just show a success message
        Toast::info('Leave your KYC submission for review. We will notify you once it is processed.');
        return redirect()->route('platform.profile'); // Redirect to profile or another appropriate route
    }
}
