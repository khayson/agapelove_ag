<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChurchMember;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with member data.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get total members count
        $totalMembers = ChurchMember::count();

        // Count new members since the start of the month
        $newMembers = ChurchMember::where('created_at', '>=', now()->startOfMonth())->count();

        // Count active and inactive members
        $activeMembers = ChurchMember::where('status', 'active')->count();
        $inactiveMembers = ChurchMember::where('status', 'inactive')->count();

        // Retrieve the 10 most recent member registrations
        $recentRegistrations = ChurchMember::orderBy('created_at', 'desc')->limit(10)->get();

        // Prepare demographics data by calculating age from date_of_birth
        $demographics = ChurchMember::selectRaw("FLOOR(TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) / 10) * 10 as age_group, COUNT(*) as total")
            ->whereNotNull('date_of_birth')
            ->groupBy('age_group')
            ->orderBy('age_group')
            ->get();

        return view('dashboard', compact(
            'totalMembers',
            'newMembers',
            'activeMembers',
            'inactiveMembers',
            'recentRegistrations',
            'demographics'
        ));
    }
}
