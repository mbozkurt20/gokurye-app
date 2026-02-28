<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkingHoursController extends Controller
{
    private array $days = [
        'monday'    => 'Pazartesi',
        'tuesday'   => 'Salı',
        'wednesday' => 'Çarşamba',
        'thursday'  => 'Perşembe',
        'friday'    => 'Cuma',
        'saturday'  => 'Cumartesi',
        'sunday'    => 'Pazar',
    ];

    public function index()
    {
        $restaurant   = Auth::user();
        $workingHours = $restaurant->working_hours ?? $this->defaultHours();

        return view('restaurant.working-hours.index', [
            'workingHours' => $workingHours,
            'days'         => $this->days,
        ]);
    }

    public function update(Request $request)
    {
        $restaurant = Auth::user();
        $hours = [];

        foreach (array_keys($this->days) as $day) {
            $hours[$day] = [
                'open'       => (bool)$request->input("hours.{$day}.open"),
                'open_time'  => $request->input("hours.{$day}.open_time", '09:00'),
                'close_time' => $request->input("hours.{$day}.close_time", '22:00'),
            ];
        }

        $restaurant->working_hours = $hours;
        $restaurant->save();

        return redirect()->back()->with('success', 'Çalışma saatleri güncellendi.');
    }

    private function defaultHours(): array
    {
        $hours = [];
        foreach (array_keys($this->days) as $day) {
            $hours[$day] = [
                'open'       => true,
                'open_time'  => '09:00',
                'close_time' => '22:00',
            ];
        }
        return $hours;
    }
}
