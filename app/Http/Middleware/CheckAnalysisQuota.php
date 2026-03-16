<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAnalysisQuota
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        if (!$user->peutAnalyser()) {
            $plan = config("plans.{$user->plan}");
            return response()->json([
                'success' => false,
                'message' => "Quota atteint ({$plan['analyses_limit']} analyses/mois). Passez au plan supérieur.",
                'upgrade' => true,
            ], 429);
        }

        return $next($request);
    }
}
