<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
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
     * Send all pings of the monitor.
     */
    public function pings(monitor $monitor)
    {
        $pings = $monitor->pings()->get();
        return response()->json([
            'pings' => $pings
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
            'address' => self::cleanUrl($request->address),
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

        $monitor->adress = self::cleanUrl($request->adress);
        $monitor->save();

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
    public static function ping(monitor $monitor) {
        //detect OS
        $os = PHP_OS;
        //ping the address
        if($os == 'WINNT') {
            // Windows
            $pingInfo = shell_exec('ping -n 1 ' . $monitor->address . ' | findstr "TTL="');
            // PowerShell = $pingInfo = shell_exec('ping -n 1 ' . $monitor->address . ' | Select-String "TTL="');
        } else {
            // Linux 
            $pingInfo = shell_exec('ping -c 1 ' . $monitor->address . ' | grep "ttl="');
        }
        
        echo $pingInfo;

        // if ping contains unknown host or not found
        if(strpos($pingInfo, 'unknown host') !== false 
            || strpos($pingInfo, 'not found') !== false
            || strpos($pingInfo, 'incorrect') !== false) {
            $pingInfo = null;
        }
        // if ping failed
        if(!$pingInfo) {
            //create a new ping
            $monitor->pings()->create([
                'monitor_id' => $monitor->id,
                'response_time' => 0
            ]);
            //return the time
            return response()->json([
                'ping' => 0
            ],200);
        }
        //split the result and get all after temps or time
        $ping = explode('temps=', $pingInfo);
        if(empty($ping[1])) {
            $ping = explode('time=', $pingInfo);
        }
        if(empty($ping[1])) {
            $ping = explode('temps<', $pingInfo);
        }
        if(empty($ping[1])) {
            $ping = explode('time<', $pingInfo);
        }
        //remove all after TTL=
        $ping = explode('TTL=', $ping[1]);
        //clean the result
        $ping = str_replace(' ', '', $ping[0]);
        $ping = str_replace('ms', '', $ping);
        
        //create a new ping
        $monitor->pings()->create([
            'monitor_id' => $monitor->id,
            'response_time' => $ping
        ]);
        //return the time
        return response()->json([
            'ping' => $ping
        ],200);
    }

    /**
     * Cleanup urls
     */
    private static function cleanUrl($url) {
        //clear string
        $url = str_replace(' ', '', $url);
        //remove all http:// or https:// from the address
        $url = str_replace('https://', '', $url);
        $url = str_replace('http://', '', $url);
        //remove all after / from the address
        $url = explode('/', $url);
        $url = $url[0];

        return $url;
    }
}
