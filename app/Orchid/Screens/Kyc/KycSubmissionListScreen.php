<?php

namespace App\Orchid\Screens\Kyc;

use App\Models\KycSubmission;
use App\Orchid\Layouts\Kyc\KycSubmissionListLayout;
use Orchid\Screen\Screen;

class KycSubmissionListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        // Fetch all KYC submissions with their associated users, ordered by the latest submission
        $KycSubmission = KycSubmission::with('user')->whereIn('status', ['pending'])->latest()->paginate();

        return [
            'kyc_submissions' => $KycSubmission,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'KYC Requests';
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
            KycSubmissionListLayout::class
        ];
    }
}
