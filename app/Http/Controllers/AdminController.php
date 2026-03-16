<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users'     => User::count(),
            'total_analyses'  => Company::count(),
            'total_tokens'    => Analysis::sum('tokens_utilises'),
            'users_pro'       => User::whereIn('plan', ['pro', 'agency'])->count(),
        ];

        $parPlan = User::select('plan', DB::raw('count(*) as total'))
            ->groupBy('plan')
            ->pluck('total', 'plan');

        $top_secteurs = Company::select('secteur', DB::raw('count(*) as total'))
            ->whereNotNull('secteur')
            ->groupBy('secteur')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $top_pays = Company::select('pays', DB::raw('count(*) as total'))
            ->whereNotNull('pays')
            ->groupBy('pays')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $analyses_par_jour = Company::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total')
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return view('admin.dashboard', compact('stats', 'parPlan', 'top_secteurs', 'top_pays', 'analyses_par_jour'));
    }

    public function utilisateurs(Request $request)
    {
        $users = User::withCount('companies')
            ->latest()
            ->paginate(20);

        return view('admin.utilisateurs', compact('users'));
    }

    public function analyses(Request $request)
    {
        $analyses = Company::with(['user', 'analyses'])
            ->latest()
            ->paginate(20);

        return view('admin.analyses', compact('analyses'));
    }
}
