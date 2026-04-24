<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index', [
            'groups' => Setting::orderBy('group')->orderBy('key')->get()->groupBy('group'),
            'groupLabels' => [
                'pricing' => 'Qiymətlər',
                'commission' => 'Komissiya',
                'payment' => 'Ödəniş dövriyyəsi',
                'general' => 'Ümumi',
            ],
        ]);
    }

    public function update(Request $request)
    {
        $settings = Setting::all()->keyBy('key');

        foreach ($request->input('settings', []) as $key => $value) {
            if ($settings->has($key)) {
                Setting::set($key, $value);
            }
        }

        return redirect()->route('settings.index')
            ->with('success', 'Parametrlər yadda saxlanıldı.');
    }
}
