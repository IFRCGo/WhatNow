<?php

use App\Models\EventType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventTypesSeeder extends Seeder
{
    
    public function run()
    {
        $this->createRecordsInDB();
    }

    protected function createRecordsInDB()
    {
        $now = now();
        $eventData = collect($this->getData())->map(function ($icon, $name) use ($now) {
            return [
                'name' => $name,
                'code' => Str::slug($name),
                'icon' => $icon,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->values()->toArray();

        EventType::insert($eventData);
    }

    protected function getData()
    {
        return [
            'Shooter' => 'active-shooter@3x.png',
            'Active Shooter' => 'active-shooter@3x.png',
            'Terror' => 'active-shooter@3x.png',
            'Terror Alert' => 'active-shooter@3x.png',
            'Quality' => 'air-quality@3x.png',
            'Air Quality Alert' => 'air-quality@3x.png',
            'Airborne' => 'airborne-disease@3x.png',
            'Animal' => 'animal-borne-disease@3x.png',
            'Avalanche' => 'avalanche@3x.png',
            'Volcano' => 'volcano@3x.png',
            'Biological' => 'biological-hazard@3x.png',
            'Biological Hazard' => 'biological-hazard@3x.png',
            'Blizzard' => 'blizzard@3x.png',
            'Blood' => 'bloodborne-disease@3x.png',
            'Wildfire' => 'extreme-fire@3x.png',
            'Child' => 'child-safety@3x.png',
            'Coastal' => 'coastal@3x.png',
            'Drought' => 'drought@3x.png',
            'Earthquake' => 'earthquake@3x.png',
            'Fog' => 'dense-fog@3x.png',
            'Hurricane' => 'hurricane@3x.png',
            'Typhoon' => 'hurricane@3x.png',
            'Tornado' => 'tornado@3x.png',
            'Cyclone' => 'tropical-cyclone@3x.png',
            'Tropical-cyclone' => 'tropical-cyclone@3x.png',
            'Storm' => 'storm@3x.png',
            'Winter Storm' => 'storm@3x.png',               'Tropical' => 'storm@3x.png',
            'Tsunami' => 'tsunami@3x.png',
            'Wind' => 'wind@3x.png',
            'Child Safety' => 'child-safety@3x.png',
            'Body Fluid Borne Disease' => 'body-fluid-borne-disease@3x.png',
            'Flu' => 'body-fluid-borne-disease@3x.png',
            'Cough' => 'body-fluid-borne-disease@3x.png',
            'Chemical Hazard' => 'chemical-hazard@3x.png',
            'Chemical' => 'chemical-hazard@3x.png',
            'Coastal Flood' => 'coastal@3x.png',
            'Dust Storm' => 'dust-storm@3x.png',
            'Epidemic' => 'epidemic@3x.png',
            'Extreme Cold' => 'extreme-cold@3x.png',
            'Cold' => 'extreme-cold@3x.png',
            'Extreme Heat' => 'heatwave@3x.png',
            'Excessive Heat' => 'extreme-heat@3x.png',
            'Flash Flood' => 'flood@3x.png',                'Flood' => 'flood@3x.png',
            'Foodborne Disease' => 'foodborne-disease@3x.png',
            'Global' => 'general@3x.png',
            'Hailstorm' => 'storm@3x.png',              'Heatwave' => 'heatwave@3x.png',
            'Heat' => 'heatwave@3x.png',
            'Hurricane Force Wind' => 'hurricane-force-wind@3x.png',
            'Hydrologic Advisory' => 'hydrologic-advisory@3x.png',
            'Lakeshore' => 'lakeshore@3x.png',
            'Landslide' => 'landslide@3x.png',
            'Marine Weather' => 'marine-weather@3x.png',
            'Nuclear Power Plant' => 'nuclear-power-plant@3x.png',
            'Nuclear' => 'nuclear@3x.png',
            'Pandemic' => 'pandemic@3x.png',
            'Pest Infestation' => 'pest-infestation@3x.png',
            'Public Health Statement' => 'public-health-statement@3x.png',
            'Radiological Hazard' => 'general@3x.png',              'Road Works' => 'road-works@3x.png',
            'Severe Thunderstorm Warning' => 'storm@3x.png',              'Tropical Storm' => 'storm@3x.png',               'Covid-19' => 'epidemic@3x.png',
        ];
    }
}
