<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Country;
use App\Models\State;
use App\Models\Location;

/**
 * Associates existing Location (city) records with their parent State.
 *
 * Algorithm:
 *   For each state, scan the JSON city list → normalize names →
 *   find matching Location records → set state_id.
 *
 * Safe to re-run: only updates rows where state_id IS NULL
 * (pass $force = true via call parameter to override existing).
 */
class LocationStateSeeder extends Seeder
{
    /**
     * Full region → state → [city names] mapping.
     * Mirrors the former getRegionWiseCitiesJson() helper exactly.
     */
    private function regionStatesCities(): array
    {
        return [
            'North India' => [
                'Uttar Pradesh'   => ['Lucknow','Kanpur','Varanasi','Agra','Meerut','Prayagraj','Aligarh','Bareilly','Ghaziabad','Noida','Greater Noida','Moradabad','Gorakhpur','Mathura','Ayodhya','Saharanpur','Muzaffarnagar','Jhansi','Sitapur','Vrindavan','Govardhan','Chitrakoot'],
                'Chandigarh'      => ['Chandigarh'],
                'Haryana'         => ['Gurugram','Gurgaon','Faridabad','Hisar','Karnal','Panipat','Ambala','Rohtak','Sonipat','Yamunanagar','Panchkula','Kurukshetra','Rewari','Sirsa','Bhiwani','Jhajjar','Jind'],
                'Punjab'          => ['Amritsar','Ludhiana','Jalandhar','Patiala','Mohali','Bathinda','Hoshiarpur','Pathankot','Firozpur','Tarn Taran','Moga','Muktsar','Sangrur','Barnala','Roopnagar','Rupnagar'],
                'Himachal Pradesh'=> ['Shimla','Dharamshala','Solan','Khajjiar','Manali','Dalhousie','Mandi','Kangra','Hamirpur','Una','Chamba','Bilaspur','Nahan'],
                'Uttarakhand'     => ['Dehradun','Haridwar','Rishikesh','Nainital','Haldwani','Roorkee','Pauri','Almora','Kashipur','Ramnagar','Mussoorie','Kedarnath','Badrinath','Auli','Joshimath'],
                'Jammu & Kashmir' => ['Srinagar','Jammu','Pahalgam','Sonmarg','Gulmarg','Baramulla','Anantnag','Sopore','Kupwara','Pulwama','Udhampur','Kathua','Ganderbal','Maa Vaishno Devi'],
                'Ladakh'          => ['Leh','Pangong Tso','Kargil','Diskit','Nubra','Khaltsi'],
                'Delhi (NCT)'     => ['New Delhi','Delhi','Old Delhi','Dwarka','Rohini','Saket','Connaught Place','Karol Bagh','Laxmi Nagar'],
            ],
            'South India' => [
                'Lakshadweep'    => ['Kavaratti','Agatti','Andrott','Amini','Kalpeni','Minicoy'],
                'Kerala'         => ['Thiruvananthapuram','Trivandrum','Kochi','Cochin','Kochin','Kozhikode','Calicut','Thrissur','Kollam','Alappuzha','Alleppey','Palakkad','Munnar','Wayanad','Malappuram','Kannur','Kottayam','Pathanamthitta','Idukki','Kasargod','Poovar','Kovalam'],
                'Tamil Nadu'     => ['Chennai','Coimbatore','Madurai','Tiruchirappalli','Trichy','Salem','Kodaikanal','Erode','Tirunelveli','Vellore','Thoothukudi','Tiruppur','Karur','Dindigul','Kanchipuram','Nagercoil','Cuddalore','Thanjavur','Ooty','Mahabalipuram','Kumbakonam','Kanyakumari'],
                'Karnataka'      => ['Bengaluru','Bangalore','Mysore','Mangalore','Hubballi','Dharwad','Belagavi','Ballari','Tumakuru','Shivamogga','Vijayapura','Raichur','Bidar','Davangere','Hassan','Udupi','Chikkamagaluru','Kodagu','Madikeri','Kalaburagi','Hampi','Coorg','Bandipur'],
                'Andhra Pradesh' => ['Visakhapatnam','Vijayawada','Guntur','Nellore','Kurnool','Rajahmundry','Tirupati','Kadapa','Anantapur','Eluru','Chittoor','Ongole','Amaravati'],
                'Puducherry'     => ['Puducherry','Pondicherry'],
                'Telangana'      => ['Hyderabad','Warangal','Nizamabad','Karimnagar','Khammam','Ramagundam','Mahbubnagar','Adilabad','Nalgonda','Siddipet'],
                'Andaman Nicobar'=> ['Andaman','Port Blair','Havelock','Neil Island'],
            ],
            'East & North East India' => [
                'West Bengal'       => ['Kolkata','Howrah','Durgapur','Asansol','Siliguri','Darjeeling','Kalimpong','Jalpaiguri','Malda','Bardhaman','Kharagpur','Haldia','Cooch Behar','Murshidabad','Shantiniketan','Mayapur','Sundarbans'],
                'Odisha'            => ['Bhubaneswar','Bhubhaneshwar','Puri','Cuttack','Rourkela','Berhampur','Sambalpur','Konark','Chilika Lake','Koraput'],
                'Bihar'             => ['Patna','Gaya','Bodh Gaya','Bhagalpur','Muzaffarpur','Purnia','Darbhanga','Nalanda','Rajgir','Vaishali','Pawapuri'],
                'Jharkhand'         => ['Ranchi','Jamshedpur','Dhanbad','Bokaro Steel City','Deoghar','Hazaribagh','Giridih','Ramgarh'],
                'Assam'             => ['Guwahati','Silchar','Dibrugarh','Jorhat','Nagaon','Tinsukia','Tezpur','Sivasagar','Kaziranga'],
                'Arunachal Pradesh' => ['Itanagar','Naharlagun','Tawang','Bomdila','Ziro','Pasighat','Roing'],
                'Meghalaya'         => ['Shillong','Tura','Nongpoh','Jowai','Cherrapunji','Mawlynnong','Dawki'],
                'Tripura'           => ['Agartala','Udaipur','Kailashahar','Dharmanagar'],
                'Manipur'           => ['Imphal','Thoubal','Bishnupur','Churachandpur','Loktak Lake','Moirang'],
                'Mizoram'           => ['Aizawl','Lunglei','Champhai'],
                'Nagaland'          => ['Kohima','Dimapur','Mokokchung','Tuensang'],
                'Sikkim'            => ['Gangtok','Namchi','Pelling','Mangan','Gyalshing','Lachung','Lachen','Gurudongmar Lake','Tshangu','Yuksom'],
            ],
            'West & Central India' => [
                'Maharashtra'    => ['Mumbai','Pune','Nagpur','Nashik','Thane','Aurangabad','Solapur','Amravati','Kolhapur','Sangli','Nanded','Akola','Latur','Chandrapur','Jalgaon','Ahmednagar','Ratnagiri','Satara','Mahabaleshwar','Lonavala','Shirdi','Alibag'],
                'Madhya Pradesh' => ['Bhopal','Indore','Gwalior','Jabalpur','Ujjain','Sagar','Dewas','Satna','Ratlam','Rewa','Chhindwara','Orchha','Khajuraho','Bandhavgarh','Pachmarhi','Kanha','Pench'],
                'Chhattisgarh'   => ['Raipur','Jagdalpur','Bhilai','Durg','Bilaspur','Korba','Rajnandgaon','Raigarh','Ambikapur','Bastar','Chitrakote'],
                'Rajasthan'      => ['Jaipur','Jodhpur','Udaipur','Kota','Bikaner','Ajmer','Alwar','Sikar','Bhilwara','Sawai Madhopur','Ranthambore','Tonk','Barmer','Bharatpur','Pushkar','Jaisalmer','Chittorgarh','Mount Abu'],
                'Gujarat'        => ['Ahmedabad','Surat','Vadodara','Rajkot','Bhavnagar','Jamnagar','Junagadh','Gandhinagar','Anand','Navsari','Valsad','Vapi','Bharuch','Porbandar','Morbi','Nadiad','Gir National Park','Somnath','Dwarka','Statue of Unity','Bhuj'],
                'Goa'            => ['Panaji','Goa','Margao','Vasco da Gama','Mapusa','Ponda','Calangute','Baga','Anjuna','Agonda','Palolem'],
            ],
        ];
    }

    /** Normalize a city name for matching: lowercase, strip parens, collapse spaces */
    private function normalize(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/\s*\(.*?\)/', '', $name); // remove (...)
        $name = preg_replace('/[^a-z0-9\s]/', '', $name);
        $name = preg_replace('/\s+/', ' ', trim($name));
        return $name;
    }

    public function run(): void
    {
        $india = Country::where('name', 'India')->orWhere('code', 'IN')->first();
        if (!$india) {
            $this->command->warn('LocationStateSeeder: India not found. Skipping.');
            return;
        }

        // Build state name → State record map (for India)
        $stateMap = State::where('country_id', $india->id)
                         ->get()
                         ->keyBy(fn($s) => $this->normalize($s->name));

        if ($stateMap->isEmpty()) {
            $this->command->warn('LocationStateSeeder: No states found. Run IndianStatesSeeder first.');
            return;
        }

        // Build a normalized lookup: normalized city name → Location record
        $allLocations = Location::whereNull('state_id')
                                ->orWhere('state_id', 0)
                                ->get(['id', 'name', 'state_id']);

        $locationLookup = $allLocations->mapWithKeys(
            fn($loc) => [$this->normalize($loc->name) => $loc]
        );

        $updated = 0;

        foreach ($this->regionStatesCities() as $regionName => $statesCities) {
            foreach ($statesCities as $stateName => $cityNames) {
                $normalizedStateName = $this->normalize($stateName);
                $state = $stateMap->get($normalizedStateName);

                if (!$state) {
                    $this->command->warn("  State not found in DB: {$stateName}");
                    continue;
                }

                foreach ($cityNames as $cityName) {
                    $key = $this->normalize($cityName);
                    $location = $locationLookup->get($key);

                    if ($location) {
                        Location::where('id', $location->id)
                                ->update(['state_id' => $state->id]);
                        $updated++;
                    }
                }
            }
        }

        $this->command->info("LocationStateSeeder: {$updated} locations associated with states.");
    }
}
