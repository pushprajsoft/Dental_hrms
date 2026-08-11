<?php

namespace App\Http\Controllers;

use App\Models\PrintSetting;
use Illuminate\Http\Request;

class PrintSettingController extends Controller
{
    public function edit()
    {
        $settings = PrintSetting::current();
        return view('settings.print_layout', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = PrintSetting::current();

        $data = $request->except('logo');

        // Handle Logo Upload
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            // Save to public/uploads/logos
            $file->move(public_path('uploads/logos'), $filename);
            $data['logo_path'] = 'uploads/logos/' . $filename;
        }

        $settings->update($data);

        return redirect()->back()->with('success', 'Print Layout & Logo updated successfully!');
    }
}