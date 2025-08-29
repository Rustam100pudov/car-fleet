<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class AdminBookingController extends Controller
{
    // Simple safety: only allow in local environment or when APP_DEBUG is true
    protected function ensureAllowed()
    {
        if (!app()->isLocal() && !config('app.debug')) {
            abort(403, 'Not allowed in this environment');
        }
    }

    public function index(Request $request)
    {
        $this->ensureAllowed();
        $bookings = Booking::with(['car.model.comfortCategory','car.driver','user'])->orderBy('starts_at')->get();
        return response()->json(['data' => $bookings]);
    }

    public function destroy($id, Request $request)
    {
        $this->ensureAllowed();
        $b = Booking::find($id);
        if (!$b) return response()->json(['message' => 'Not found'], 404);
        $b->delete();
        return response()->noContent();
    }

    public function destroyAll(Request $request)
    {
        $this->ensureAllowed();
        Booking::query()->delete();
        return response()->noContent();
    }
}
