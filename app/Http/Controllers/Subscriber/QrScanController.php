<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Services\TicketCheckinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QrScanController extends Controller
{
    public function checkin(Request $request, TicketCheckinService $ticketCheckinService): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:255'],
        ]);

        $result = $ticketCheckinService->checkIn($data['token'], $request->user());

        return response()->json($result);
    }
}




