<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'api_secret' => 'required|string',
        ]);

        $application = Application::where('api_key', $request->api_key)
            ->where('is_active', true)
            ->first();

        if (!$application || !Hash::check($request->api_secret, $application->api_secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API credentials'
            ], 401);
        }

        // Create a simple token for the application (in production, use a more secure method)
        $token = base64_encode($application->id . ':' . time());

        return response()->json([
            'success' => true,
            'application' => [
                'id' => $application->id,
                'name' => $application->name,
                'slug' => $application->slug,
            ],
            'token' => $token,
            'message' => 'Authentication successful'
        ]);
    }

    public function verify(Request $request)
    {
        $authHeader = $request->header('Authorization');
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization header missing or invalid'
            ], 401);
        }

        $token = substr($authHeader, 7);
        
        try {
            $decoded = base64_decode($token);
            [$applicationId, $timestamp] = explode(':', $decoded);
            
            $application = Application::where('id', $applicationId)
                ->where('is_active', true)
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token'
                ], 401);
            }

            // Token expires after 24 hours
            if ((time() - $timestamp) > 86400) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token expired'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'application' => [
                    'id' => $application->id,
                    'name' => $application->name,
                    'slug' => $application->slug,
                ],
                'message' => 'Token is valid'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token format'
            ], 401);
        }
    }
}
