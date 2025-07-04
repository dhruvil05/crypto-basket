<?php

namespace App\Orchid\Screens\User;

use App\Models\WalletTransaction;
use App\Models\WalletWithdrawal;
use App\Orchid\Layouts\User\ActivityHistoryLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Illuminate\Support\Facades\Storage;

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
            Layout::modal('withdrawDetailsModal', [
                Layout::legend('withdrawal', [
                    Sight::make('amount', 'Amount'),
                    Sight::make('utr_number', 'UTR / Transaction ID'),
                    Sight::make('status', 'Status')->render(
                        function ($withdrawal) {
                            if (is_null($withdrawal->status)) {
                                return '';
                            }
                            $status = ucfirst($withdrawal->status); // Assuming status is: 'approved', 'pending', 'rejected'

                            $colorClass = match ($withdrawal->status) {
                                'approved' => 'badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2',
                                'completed' => 'badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2',
                                'pending'  => 'badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2',
                                'rejected' => 'badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2',
                                default    => 'badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2',
                            };

                            return "<span class=\"px-3 py-1 rounded-full text-xs font-semibold {$colorClass}\">{$status}</span>";
                        }
                    ),
                    Sight::make('created_at', 'Requested At'),
                    Sight::make('screenshot', 'Screenshot')->render(function ($withdrawal) {
                        $path = asset($withdrawal->screenshot);
                        return $withdrawal->screenshot
                            ? '<img src="' . $path . '" width="150">'
                            : '<span class="text-muted">No screenshot uploaded</span>';
                    }),
                ]),
            ])->async('loadWithdrawDetails')
                ->withoutApplyButton(),

        ];
    }

    public function asyncLoadWithdrawDetails(Request $request): array
    {
        $withdrawal = WalletWithdrawal::find($request->get('withdrawal_id'));

        return [
            'withdrawal' => $withdrawal,
        ];
    }
}
