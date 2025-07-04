<?php

namespace App\Orchid\Screens;

use App\Models\ReferralSetting;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layout;
use Orchid\Support\Facades\Layout as OrchidLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Toast;

class ReferralSettingsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'setting' => ReferralSetting::firstOrCreate([]),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Referral Settings';
    }

    /**
     * The permissions required to access this screen.
     */
    public function permission(): ?iterable
    {
        return [
            'platform.systems.settings',
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
            OrchidLayout::rows([
                Input::make('setting.referrer_discount')
                    ->title('Discount for Referrer (%)')
                    ->type('number')
                    ->step(0.01),

                Input::make('setting.referee_reward')
                    ->title('Reward for Referee (%)')
                    ->type('number')
                    ->step(0.01),

                CheckBox::make('setting.is_active')
                    ->title('Enable Referral System')
                    ->sendTrueOrFalse(),

                Button::make('Save')
                    ->method('save')
                    ->type(\Orchid\Support\Color::DEFAULT())
                    ->icon('bs.check-circle')
                    ->class('btn btn-info rounded px-4 py-2 fw-bold')
                    ->style('gap: 8px; transition: transform 0.2s ease;')
            ]),
        ];
    }

    public function save()
    {
        $data = request()->get('setting');
        $setting = ReferralSetting::first();
        $setting->update($data);

        Toast::info(__('Referral settings updated!'));
    }
}
