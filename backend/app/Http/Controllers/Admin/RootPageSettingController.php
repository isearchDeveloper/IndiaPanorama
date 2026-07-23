<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Region;
use App\Models\State;
use Illuminate\Http\Request;

class RootPageSettingController extends Controller
{
    private function indiaCountryId(): int
    {
        return 1;
    }

    public function index(Request $request)
    {
        // AJAX: FAQs for a city
        if ($request->exists('faqs') && $request->id) {
            $loc = Location::with('faqs')->findOrFail($request->id);
            return response()->json(['status' => 'success', 'location' => $loc]);
        }

        // AJAX: Best Time to Visit for a city
        if ($request->exists('best_times') && $request->id) {
            $loc = Location::with('bestTimes')->findOrFail($request->id);
            return response()->json(['status' => 'success', 'location' => $loc]);
        }

        $regionQuery = Region::withCount('states')->orderBy('name');
        $regions = $regionQuery->paginate(20, ['*'], 'region_page')->withQueryString();

        $stateQuery = State::with('region')
            ->withCount('cities')
            ->where('country_id', $this->indiaCountryId())
            ->orderBy('name');

        if ($search = $request->get('search')) {
            $stateQuery->where('name', 'LIKE', "%{$search}%");
        }
        $states = $stateQuery->paginate(15, ['*'], 'state_page')->withQueryString();

        $cityQuery = Location::with('state')
            ->withCount(['packagesLocation', 'packagesSource'])
            ->where('country_id', $this->indiaCountryId())
            ->orderBy('name');

        if ($citySearch = $request->get('city')) {
            $cityQuery->where('name', 'LIKE', "%{$citySearch}%");
        }
        $locations = $cityQuery->paginate(15, ['*'], 'city_page')->withQueryString();

        return view('admin.root-page-setting.index', compact(
            'regions', 'states', 'locations', 'citySearch'
        ));
    }

    public function statesSearch(Request $request)
    {
        $query = State::with('region')
            ->where('country_id', $this->indiaCountryId())
            ->orderBy('name');

        if ($search = $request->get('search')) {
            $query->where('name', 'LIKE', "%{$search}%");
        }
        $states = $query->paginate(15)->withQueryString();

        return response()->json([
            'html' => view('admin.root-page-setting.state-list', compact('states'))->render(),
        ]);
    }

    public function citiesSearch(Request $request)
    {
        $query = Location::with('state')
            ->withCount(['packagesLocation', 'packagesSource'])
            ->where('country_id', $this->indiaCountryId())
            ->orderBy('name');

        if ($citySearch = $request->get('city')) {
            $query->where('name', 'LIKE', "%{$citySearch}%");
        }
        $locations = $query->paginate(15)->withQueryString();

        return response()->json([
            'html' => view('admin.root-page-setting.city-table', compact('locations'))->render(),
        ]);
    }
}
