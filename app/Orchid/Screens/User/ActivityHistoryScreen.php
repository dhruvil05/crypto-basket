<?php

namespace App\Orchid\Screens\User;

use App\Models\WalletTransaction;
use App\Orchid\Layouts\User\ActivityHistoryLayout;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;

class ActivityHistoryScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query($activity = null): iterable
    {
        $user = Auth::user();
        if ($user->inRole('admin') && $activity) {
            $userId = $activity;
        } else {
            $userId = $user->id;
        }

        return [
            'activityHistory' => WalletTransaction::where('user_id', $userId)
                ->latest()
                ->paginate(20),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Activity History';
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
            ActivityHistoryLayout::class,
        ];
    }
}
