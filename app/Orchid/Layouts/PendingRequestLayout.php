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
