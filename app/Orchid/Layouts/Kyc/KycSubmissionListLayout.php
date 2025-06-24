<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Kyc;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Persona;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class KycSubmissionListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'kyc_submissions';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('name', __('Name'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn($KycSubmission) => new Persona($KycSubmission->user->presenter())),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),
            TD::make('status', __('Status'))
                ->sort()
                ->render(fn($KycSubmission) => $KycSubmission->status)
                ->width('150px')
                ->align(TD::ALIGN_CENTER)
                ->filter(TD::FILTER_SELECT, [
                    'pending' => __('Pending'),
                    'approved' => __('Approved'),
                    'rejected' => __('Rejected'),
                ]),
            
            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn($KycSubmission) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([

                        Link::make(__('View'))
                            ->route('platform.user.kyc.requests.view', $KycSubmission->id)
                            ->icon('bs.pencil'),

                    ])),
        ];
    }
}
