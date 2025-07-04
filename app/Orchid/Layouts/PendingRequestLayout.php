<?php

declare(strict_types=1);

namespace App\Orchid\Layouts;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Persona;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class PendingRequestLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'pendingData';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('name', __('Username'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn($pendingData) => $pendingData->user?->name ?? '-'),

            TD::make('type', __('Request type'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('utr', __('UTR'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('status', __('Status'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(function ($pendingData) {
                    $status = ucfirst($pendingData->status); // Assuming status is: 'approved', 'pending', 'rejected'

                    $colorClass = match ($pendingData->status) {
                        'approved' => 'badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2',
                        'completed' => 'badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2',
                        'pending'  => 'badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2',
                        'rejected' => 'badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2',
                        default    => 'badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2',
                    };

                    return "<span class=\"px-3 py-1 rounded-full text-xs font-semibold {$colorClass}\">{$status}</span>";
                })->align(TD::ALIGN_CENTER),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('120px')
                ->render(function ($pendingData) {
                    return Link::make('')
                        ->attributes([
                            'data-tippy-content' => 'View',
                        ])
                        ->icon('bs.eye')
                        ->route('platform.funds.edit', ['transaction' => $pendingData->id])
                        ->canSee(Auth::user()->inRole('admin'));
                }),
        ];
    }
}
