<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class MonitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($category = null)
    {
        if ($category) {
            $monitors = Monitor::where('icon', $category)->orderBy('order', 'asc')->get();
        } else {
            $monitors = Monitor::orderBy('order', 'asc')->get();
        }

        return response()->json([
            'monitors' => $monitors
        ],200);
    }

    /**
     * Display a listing of the resource by category.
     */
    public function category($category)
    {
        return Inertia::render('Dashboard', [
            'monitors' => Monitor::where('icon', $category)->orderBy('order', 'asc')->get(),
            'category' => $category,
        ]);

        $monitors = Monitor::where('icon', $category)->orderBy('order', 'asc')->get();
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
     * Send the last pings of the monitor by the interval.
     */
    public function latestPings(monitor $monitor)
    {
        $pings = $monitor->latestPings()->get();
        return response()->json([
            'pings' => $pings
        ],200);
    }

    /**
     * Get the last ping and verify if his response_time is 0.
     * If it's 0, get the last time the monitor was up.
     * If it's not 0, get the last time the monitor was down.
     */
    public function lastChange(monitor $monitor)
    {
        $lastChange = null;
        $lastPing = $monitor->latestPings()->get();
        if($lastPing[0]->response_time == 0) {
            $lastChange = $monitor->pings()->where('response_time', '!=', 0)->latest()->first();
        }else{
            $lastChange = $monitor->pings()->where('response_time', '=', 0)->latest()->first();
        }
        if($lastChange == null) {
            $lastChange = $monitor->pings()->first();
            $lastChange['first'] = true;
        }
        return response()->json([
            'lastChange' => $lastChange
        ],200);
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

        if( !filter_var($request->address, FILTER_VALIDATE_URL) && !filter_var($request->address, FILTER_VALIDATE_IP) && !filter_var($request->address, FILTER_VALIDATE_DOMAIN) ) {
            return response()->json([
                'message' => 'Invalid URL'
            ],400);
        }

        $monitor = monitor::create([
            'name' => $request->name,
            'address' => self::cleanUrl($request->address),
            'user_id' => auth()->user()->id,
            'url' => $request->url,
            'interval' => $request->interval,
            'command' => $request->command,
            'note' => $request->note,
            'icon' => $request->icon,
            'links' => self::cleanLinks($request->links),
        ]);
        
        return response()->json([
            'monitor' => $monitor
        ],201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, monitor $monitor)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
        ]);

        if( !filter_var($request->address, FILTER_VALIDATE_URL) && !filter_var($request->address, FILTER_VALIDATE_IP) && !filter_var($request->address, FILTER_VALIDATE_DOMAIN) ) {
            return response()->json([
                'message' => 'Invalid URL'
            ],400);
        }

        $monitor->links = self::cleanLinks($request->links);

        $monitor->update([
            'name' => $request->name,
            'address' => self::cleanUrl($request->address),
            'url' => $request->url,
            'interval' => $request->interval,
            'command' => $request->command,
            'note' => $request->note,
            'icon' => $request->icon,
        ]);

        return response()->json([
            'monitor' => $monitor
        ],200);
    }

    /**
     * Update the order of the monitors.
     */
    public function switchOrder(Request $request, $monitorId)
    {
        $request->validate([
            'order' => 'required',
        ]);

        $currentMonitor = Monitor::find($monitorId);
        $otherMonitor = Monitor::where('order', $request->order)->first();

        if ($otherMonitor) {
            $otherMonitor->update([
                'order' => $currentMonitor->order
            ]);
        }else{
            // foreach all monitors and give them all a new order
            $monitors = Monitor::orderBy('order', 'asc')->get();
            $i = 1;
            foreach($monitors as $monitor) {
                if($i == $request->order) {
                    $i++;
                }
                $monitor->update([
                    'order' => $i
                ]);
                $i++;
            }
        }
        $currentMonitor->update([
            'order' => $request->order
        ]);

        return response()->json($request->order,200);
    }

    /**
     * Update the order of the monitors by alphabetical order.
     */
    public function switchOrderAlphabetical()
    {
        // foreach all monitors and give them all a new order
        $monitors = Monitor::orderBy('name', 'asc')->get();
        $i = 1;
        foreach($monitors as $monitor) {
            $monitor->update([
                'order' => $i
            ]);
            $i++;
        }

        return response()->json(true,200);
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

        //if the adress contains : and a port, get it and remove it from the adress
        $port = null;
        $address = $monitor->address;

        // if the address is a valid url, use curl
        if(filter_var($address, FILTER_VALIDATE_URL)) {
            $headers = @get_headers($address);
            if(!$headers || strpos( $headers[0], '404')) {
                $pingInfo = false;
                $ping = 0;
            }
            else {
                $pingInfo = true;
                $ping = 1;
            }
        }else{
            if(strpos($monitor->address, ':') !== false) {
                $port = explode(':', $monitor->address)[1];
                $address = explode(':', $monitor->address)[0];
            }
    
            //ping the address
            if($os == 'WINNT') {
                // Windows
                $pingInfo = shell_exec('ping -n 1 ' . $address . ' | findstr "TTL="');
                // PowerShell = $pingInfo = shell_exec('ping -n 1 ' . $monitor->address . ' | Select-String "TTL="');
            } else {
                // Linux 
                $pingInfo = shell_exec('ping -c 1 ' . $address . ' | grep "ttl="');
            }

            if( !empty($monitor->command) ) {
                shell_exec($monitor->command);
            }
    
            // if ping contains unknown host or not found
            if(strpos($pingInfo, 'unknown host') !== false 
                || strpos($pingInfo, 'not found') !== false
                || strpos($pingInfo, 'incorrect') !== false) {
                $pingInfo = null;
            }
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
        if(empty($ping)) {
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
        }
        
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
     * Delete ping of the monitor that are older than 15 days.
     */
    public static function deleteOldPing($monitor) {
        $pings = $monitor->pings()->where('created_at', '<', Carbon::now()->subDays(15))->get();
        foreach($pings as $ping) {
            $ping->delete();
        }
    }

    /**
     * Cleanup urls
     */
    private static function cleanUrl($url) {
        //clear string
        $url = str_replace(' ', '', $url);
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            //remove all http:// or https:// from the address
            $url = str_replace('https://', '', $url);
            $url = str_replace('http://', '', $url);
            //remove all after / from the address
            $url = explode('/', $url);
            $url = $url[0];
        }
        return $url;
    }

    /**
     * Cleanup links array
     */
    private static function cleanLinks($links) {
        if(empty($links)) {
            return null;
        }
        foreach($links as $key => $link) {
            if($link == null || $link == '' || !$link['url']) {
                unset($links[$key]);
            }else{
                if(!$link['name']){
                    $links[$key]['name'] = $link['url'];
                }
                if(!preg_match('/^(http|https|ftp)/', $link['url'])) {
                    $links[$key]['url'] = 'http://'.$link['url'];
                }
            }
        }

        return json_encode($links);
    }

    /**
     * Import monitors from a csv file.
     * format : name,address,url,command,note
     */
    public function import(Request $request) {
        $request->validate([
            'file' => 'required',
        ]);

        $file = $request->file('file');
        $file = fopen($file, 'r');
        while (($line = fgetcsv($file)) !== FALSE) {
            $monitor = monitor::create([
                'name' => $line[0],
                'address' => self::cleanUrl($line[1]),
                'user_id' => auth()->user()->id,
                'url' => $line[2],
                'note' => $line[3],
                'command' => $line[4],
            ]);
        }
        fclose($file);

        return response()->json([
            'message' => 'Importé'
        ],200);
    }
}
