<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CsrfController extends Controller
{
    /**
     * Return a fresh CSRF token.
     * Used by offline sync to get a valid token before syncing.
     */
    public function refresh(Request $request)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'error' => 'session_expired',
                'message' => 'Your session has expired. Please log in again.'
            ], 401);
        }

        return response()->json([
            'csrf_token' => csrf_token()
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
