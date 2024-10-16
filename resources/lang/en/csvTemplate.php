<?php

return [
    'attribution_columns' => [
        'name' => 'Society Name',
        'message' => 'Attribution Message',
        'url' => 'Attribution Url'
    ],
    'instruction_columns' => [
        'eventType' => 'Event Type',
        'otherType' => 'Other type - Yes/No',
        'regionName' => 'Region Name',
        'title' => 'Title',
        'description' => 'Description',
        'webUrl' => 'Web Url',
        'stages' => [
            'mitigation' => 'Mitigation Stages',
            'seasonalForecast' => 'Seasonal Forecast Stages',
            'warning' => 'Warning Stages',
            'watch' => 'Watch Stages',
            'immediate' => 'Immediate Stages',
            'recover' => 'Recover Stages'
        ]
    ],
    'attribution_heading' => 'List attribution title and message below',
    'instructions_heading' => 'List whatnow content below. Once event type per line.',
    'separator' => '\\\\',
    'stagesInstruction' => 'Each stage within an Stages column must be on a new row leave preceding duplicate cells blank'
];
