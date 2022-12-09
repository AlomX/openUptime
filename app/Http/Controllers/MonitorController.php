<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Monitor;

class MonitorController extends Controller
{

    public function index()
    {
        // Retrieve the data you want to pass to the component
        $data = Monitor::all();
        dd($data);
        return Inertia::share('Dashboard', [
            'monitors' => $data,
        ]);
    }

    /**
     * Convert the object instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'refund' => $this->refund,
        ];
    }

    /**
     * Convert the object instance to JSON.
     *
     * @param  int  $options
     * @return string
     *
     * @throws \Exception
     */
    public function toJson($options = 0)
    {
        $json = json_encode($this->toArray(), $options);
    
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception(json_last_error_msg());
        }
    
        return $json;
    }

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
