<?php

namespace App\Http\Controllers;

use App\Models\monitor;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $monitors = monitor::all();
        return response()->json([
            'monitors' => $monitors
        ],200);
        
    }

    /**
     * Send the last 50 pings of the monitor.
     */
    public function latestPings(monitor $monitor)
    {
        $pings = $monitor->latestPings()->get();
        return response()->json([
            'pings' => $pings
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
        ]);

        $monitor = monitor::create([
            'name' => $request->name,
            'address' => $request->address,
            'user_id' => auth()->user()->id,
        ]);
        
        return response()->json([
            'monitor' => $monitor
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(monitor $monitor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(monitor $monitor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, monitor $monitor)
    {
        $monitor = $monitor->update($request->all());
        return response()->json([
            'monitor' => $monitor
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(monitor $monitor)
    {
        //delete all ping link to this monitor
        $monitor->pings()->delete();
        //delete the monitor
        $monitor->delete();
        return response()->json(true,204);
    }

    /**
     * Ping the adress of the monitor using shell_exec.
     * Adapt depending on the OS.
     */
    public function ping(monitor $monitor) {
        $ping = shell_exec('ping -n 1 ' . $monitor->address);
        dd($ping);
        $ping = explode(' ', $ping);
        $ping = $ping[6];
        $ping = explode('=', $ping);
        $ping = $ping[1];
        $ping = explode('.', $ping);
        $ping = $ping[0];
        return response()->json([
            'ping' => $ping
        ],200);
    }

}
