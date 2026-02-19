<?php

namespace App\Http\Controllers;

use App\Models\TimeEntries;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TimeEntriesController extends Controller
{
    public function validate()
    {
        $rules = [
            'user_id' => 'required|exists:users,id|numeric',
            'clock_in_at' => 'date',
            'click_out_at' => 'date',
        ];

        $messages = [
            'user_id.required' => 'The user field is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'user_id.numeric' => 'The user must be a numeric value.',
            'clock_in_at.datetime' => 'The clock-in date and time must be a valid datetime.',
            'click_out_at.datetime' => 'The clock-out date and time must be a valid datetime.',
        ];

        return [$rules, $messages];
    }

    /**
     * return all time entries
     */
    /**
     * return all time entries
     */
    public function index(Request $request)
    {
        try {
            $query = TimeEntries::query();

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            $timeEntries = $query->get();

            return response()->json($timeEntries);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'there is a problem showing all time entries',
                'mistake' => $e->getMessage(),
            ]);
        }
    }

    /**
     * create a new time entrie
     */
    public function store(Request $request)
    {
        try {

            $validate = Validator::make($request->all(), $this->validate()[0], $this->validate()[1]);

            if ($validate->fails()) {
                return response()->json(['error' => $validate->errors()->first()]);
            }

            $timeEntrie = new TimeEntries;
            $timeEntrie->user_id = $request->user_id;
            $timeEntrie->clock_in_at = $request->clock_in;
            $timeEntrie->clock_out_at = $request->clock_out;
            $timeEntrie->save();

            return response()->json(['success' => 'the time entrie has been created']);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'there is a problem creating the time entrie',
                'mistake' => $e->getMessage(),
            ]);
        }
    }

    /**
     * return one time entrie by id
     */
    public function show(string $id)
    {
        try {
            $timeEntrie = TimeEntries::where('id', $id)->first();

            if (! $timeEntrie) {
                return response()->json(['error the time entrie does not exists']);
            }

            return response()->json($timeEntrie);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'there is a problem showing the time entrie',
                'mistake' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update one time entrie by id introducing all params
     */
    public function update(Request $request, string $id)
    {
        try {
            $validate = Validator::make($request->all(), $this->validate()[0], $this->validate()[1]);

            if ($validate->fails()) {
                return response()->json(['error' => $validate->errors()->first()]);
            }

            $timeEntrie = TimeEntries::where('id', $id)->first();

            if (! $timeEntrie) {
                return response()->json(['error the time entrie does not exists']);
            }

            $timeEntrie->user_id = $request->user_id;
            $timeEntrie->clock_in_at = $request->clock_in_at;
            if (isset($request->clock_out_at)) {
                $timeEntrie->clock_out_at = $request->clock_out_at;
            }
            $timeEntrie->save();

            return response()->json(['success' => 'the time entrie has been updated']);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'there is a problem updating the time entrie',
                'mistake' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove one time entrie by id
     */
    public function destroy(string $id)
    {
        try {
            $timeEntrie = TimeEntries::where('id', $id)->first();

            if (! $timeEntrie) {
                return response()->json(['error the time entrie does not exists']);
            }

            $timeEntrie->delete();

            return response()->json(['success' => 'the time entrie has been deleted']);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'there is a problem removing the time entrie',
                'mistake' => $e->getMessage(),
            ]);
        }
    }

    public function clock_In_Out(Request $request)
    {

        try {
            // Validation Logic
            $locationError = $this->checkLocation($request);
            if ($locationError) {
                return response()->json(['error' => $locationError], 403);
            }

            $userId = Auth::user()->id;
            $timeEntrie = TimeEntries::where('user_id', $userId)->where('clock_out_at', null)->first(); // comprobar que solo haya una mas tarde
            if ($timeEntrie) {
                $timeEntrie->clock_out_at = now();
                $timeEntrie->save();

                return response()->json(['success' => 'you clock out of the work']);
            } else {
                $this->create($userId);

                return response()->json(['success' => 'you clock in of the work']);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => 'there is a error creating the clock_in_ou',
                'fail' => $e->getMessage(),
            ]);
        }
    }

    private function checkLocation(Request $request)
    {
        $lat = $request->input('latitud');
        $lon = $request->input('altitud'); // The frontend sends longitude in 'altitud'

        if (! $lat || ! $lon) {
            return 'Location is required. Please enable geolocation.';
        }

        // Medac Arena Cordoba Coordinates
        $targetLat = 37.8802566;
        $targetLon = -4.8040947;

        $distance = $this->calculateDistrict($lat, $lon, $targetLat, $targetLon);

        // Margin in meters (e.g., 200 meters)
        $margin = 200;

        if ($distance > $margin) {
            return "Estas muy lejos de Medac Arena ($distance metros). entrada no disponible.";
        }

        return null;
    }

    private function calculateDistrict($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c);
    }

    public function create($id)
    {
        try {
            $timeEntrie = new TimeEntries;
            $timeEntrie->user_id = $id;
            $timeEntrie->clock_in_at = now();
            $timeEntrie->clock_out_at = null;
            $timeEntrie->save();

            return response()->json(['success' => 'the time entrie has been created']);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'there is a problem creating the time entrie',
                'mistake' => $e->getMessage(),
            ]);
        }
    }

    public function getLastThreeEntries()
    {
        try {
            $userId = Auth::user()->id;
            $timeEntries = TimeEntries::where('user_id', $userId)
                ->orderBy('id', 'desc')
                ->take(3)
                ->get();

            return response()->json($timeEntries);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'there is a problem showing the time entries',
                'mistake' => $e->getMessage(),
            ]);
        }
    }

    public function getStatistics(Request $request)
    {
        try {
            $userId = Auth::user()->id;
            $today = now();
            $sevenDaysAgo = now()->subDays(6);

            $entries = TimeEntries::where('user_id', $userId)
                ->where('clock_in_at', '>=', $sevenDaysAgo->startOfDay())
                ->where('clock_in_at', '<=', $today->endOfDay())
                ->get();

            $stats = [];
            $currentDate = clone $sevenDaysAgo;

            while ($currentDate <= $today) {
                $dateStr = $currentDate->format('Y-m-d');
                $dayName = $currentDate->format('D'); // Mon, Tue, etc.

                $dayEntries = $entries->filter(function ($entry) use ($dateStr) {
                    // Ensure clock_in_at is treated as Carbon instance
                    return $entry->clock_in_at->format('Y-m-d') === $dateStr;
                });

                $totalSeconds = 0;
                foreach ($dayEntries as $entry) {
                    if ($entry->clock_out_at) {
                        // Carbon instances can be used directly for diffInSeconds
                        // key fix: use abs() because diffInSeconds might return negative if clock_in is earlier
                        $totalSeconds += abs($entry->clock_out_at->diffInSeconds($entry->clock_in_at));
                    }
                }

                $hours = round($totalSeconds / 3600, 2);

                $stats[] = [
                    'date' => $dateStr,
                    'day' => $dayName,
                    'hours' => $hours,
                ];

                $currentDate->addDay();
            }

            return response()->json($stats);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error calculating statistics',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllOfOneUser(Request $request){
        try{
            $idUser = Auth::user()->id;
            
            $query = TimeEntries::where("user_id", $idUser);

            if ($request->has('date') && $request->date) {
                $date = $request->date;
                $query->whereDate('clock_in_at', $date);

                 $year = substr($date, 0, 4);
                 $query->whereYear('clock_in_at', $year);
            }

            $entries = $query->get();

            return response()->json($entries);
        }catch(Exception $e){
            return response()->json([
                "error"=>"there is a problem showing the time entries",
                "details"=>$e->getMessage()
            ]);
        }
    }
}
