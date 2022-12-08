<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Monitor;

class MonitorController extends Controller
{
    /**
     * Store a new monitor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function store(Request $request)
    {
        $monitor = new Monitor;
        $monitor->name = $request->name;
        $monitor->address = $request->address;
        $monitor->save();
        return true;
    }

    /**
     * Update the monitor information.
     *
     * @param  \App\Http\Requests\ProfileUpdateRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileUpdateRequest $request)
    {
        $request->monitor()->fill($request->validated());

        $request->monitor()->save();

        return Redirect::route('dashboard');
    }

    /**
     * Delete the monitor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {

    }
}
