<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user      = $request->user();
        $companies = $user->companies()->with('analyses')->latest()->paginate(10);

        $stats = [
            'total_analyses'    => $user->companies()->count(),
            'analyses_ce_mois'  => $user->analyses_this_month,
            'score_moyen'       => $user->companies()->avg('score_digital') ?? 0,
            'derniere_analyse'  => $user->companies()->latest()->first()?->nom,
        ];

        return view('dashboard.index', compact('user', 'companies', 'stats'));
    }
}
