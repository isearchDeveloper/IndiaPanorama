<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Category;
use App\Models\Country;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {

    public function index(){
        try {
            $counts = Package::selectRaw("
                COUNT(*) as total,
                SUM(is_active = 1) as active,
                SUM(is_active = 0) as inactive
            ")
            ->first();

            $totalPackages = $counts->total ?? 0;
            $activeP       = $counts->active ?? 0;
            $activeIn      = $counts->inactive ?? 0;
        } catch (\Exception) {
            $totalPackages = $activeP = $activeIn = 0;
        }

        try {
            $totalCategories = Category::count();
        } catch (\Exception) {
            $totalCategories = 0;
        }

        try {
            $tables = ['enquiry_packages', 'enquiry_generals', 'enquiry_plan_trips', 'enquiry_cars'];
            $unions = array_map(
                fn($t) => "SELECT COUNT(*) as cnt FROM `{$t}` WHERE is_confirmed = 0",
                $tables
            );
            $rows = DB::select('SELECT SUM(cnt) as total FROM (' . implode(' UNION ALL ', $unions) . ') as sub');
            $totalPendingEnquery = (int) ($rows[0]->total ?? 0);
        } catch (\Exception) {
            $totalPendingEnquery = 0;
        }

        try {
            $totalCountry = Country::count();
            $totalCity    = Location::count();
        } catch (\Exception) {
            $totalCountry = $totalCity = 0;
        }

        try {
            $packagesByAuthor = Package::select('author_name', DB::raw('COUNT(*) as total_packages'))
                ->groupBy('author_name')->get();
        } catch (\Exception) {
            $packagesByAuthor = collect();
        }

        return view('admin.dashboard', compact(
            'totalPackages','activeP','activeIn',
            'totalCategories','totalPendingEnquery','totalCountry','totalCity',
            'packagesByAuthor'
        ));
    }
}
