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
                ->render(function ($KycSubmission) {
                    $status = ucfirst($KycSubmission->status); // Assuming status is: 'approved', 'pending', 'rejected'

                    $colorClass = match ($KycSubmission->status) {
                        'approved' => 'badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2',
                        'completed' => 'badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2',
                        'pending'  => 'badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2',
                        'rejected' => 'badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2',
                        default    => 'badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2',
                    };

                    return "<span class=\"px-3 py-1 rounded-full text-xs font-semibold {$colorClass}\">{$status}</span>";
                })
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
