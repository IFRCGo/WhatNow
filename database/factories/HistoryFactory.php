<?php

$factory->define(\App\Models\History\History::class, function (Faker\Generator $faker) {
    $lang = null;
    $action = $faker->randomElement([
        'history.whatnow.instruction.created',
        'history.whatnow.instruction.created',
        'history.whatnow.instruction.updated',
        'history.whatnow.instruction.updated',
        'history.whatnow.instruction.updated',
        'history.whatnow.instruction.updated',
        'history.whatnow.instruction.updated',
        'history.whatnow.instruction.updated',
        'history.whatnow.instruction.deleted',
        'history.whatnow.instruction.translation_published',
        'history.whatnow.instruction.translation_created',
        'history.whatnow.instruction.translation_published',
        'history.whatnow.instruction.translation_published',
        'history.whatnow.instruction.translation_published',
        'history.whatnow.instruction.translation_published',
        'history.whatnow.instruction.translation_published',
        'history.whatnow.instruction.translation_unpublished',
    ]);

    if (in_array($action, [
        'history.whatnow.instruction.translation_published',
        'history.whatnow.instruction.translation_unpublished',
        'history.whatnow.instruction.translation_created',
    ])) {
        $lang = $faker->randomElement(['en']);
    }

    return [
        'country_code'        => $faker->randomElement(['USA', 'CAN']),
        'language_code'        => $lang,
        'action'         => $action,
        'entity_id' => $faker->randomElement([rand(1, 100), null]),
        'content' => $faker->randomElement([
            'General',
            'Wildfire',
            'Biological Hazard',
            'Chemical Hazard',
            'Drought',
            'Earthquake',
            'Extreme Cold',
            'Extreme Heat',
            'Flood',
            'Hailstorm',
            'Hurricane',
            'Landslide'
        ]),
        'user_id' => rand(1, 30),
    ];
});
