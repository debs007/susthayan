<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\SupportQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportQueryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $queries = SupportQuery::where('user_id', $request->user()->id)
            ->latest()
            ->get(['id', 'subject', 'message', 'status', 'resolved_at', 'created_at']);

        return response()->json(['data' => $queries]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $query = SupportQuery::create([
            'user_id' => $request->user()->id,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        return response()->json(['data' => $query], 201);
    }
}
