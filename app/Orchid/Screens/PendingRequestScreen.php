<?php

namespace App\Orchid\Screens;

use App\Models\WalletTransaction;
use App\Orchid\Layouts\PendingRequestLayout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class PendingRequestScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $data = WalletTransaction::with('user')
            ->where('type', 'deposit')
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);
        // dd($data);
        return [
            'pendingData' => $data
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Fund Requests';
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
            PendingRequestLayout::class,
        ];
    }

}
