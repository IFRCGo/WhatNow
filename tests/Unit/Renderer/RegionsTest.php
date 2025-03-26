<?php

namespace Tests\Unit\Renderer;

use App\Classes\Renderer\Entities\EventType;
use App\Models\Access\User\User;
use App\Models\Access\User\UserProfile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/** @group skipPipeline */
class RegionsTest extends TestCase
{
    use DatabaseTransactions;

    /** @var \App\Models\Access\User\User */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user->userProfile()->save(factory(UserProfile::class)->make());
    }

    public function testRegionsForOrganisation()
    {
       $this->actingAs($this->user)->getJson('api/subnationals/USA')
            ->assertStatus(200);
    }

    public function testGetLaguageSpecificRegionsForOrganisation()
    {
        $this->actingAs($this->user)->getJson('api/subnationals/USA/es')
            ->assertStatus(200);
    }

    public function testRegionsCrudForOrganisation()
    {
        $data = [
            "countryCode" => 'USA',
            'title' => 'Test Region 1',
            'slug' => 'test-subnationals-1',
            'translations' => [
                "en" => [
                    'title' => 'Testing 3',
                    'description' => 'testing 789'
                ],
                "es" => [
                    'title' => 'Prueba 3',
                    'description' => 'prueba 789'
                ]
            ]
        ];

        $response = $this->actingAs($this->user)->postJson('api/subnationals', $data)
            ->assertStatus(201);
        $regionCreated = json_decode($response->getContent());

        $data = [
            "countryCode" => 'USA',
            'title' => 'Test Region 1',
            'slug' => 'test-subnationals-1',
            'translations' => [
                "en" => [
                    'title' => 'Testing 4',
                    'description' => 'testing 987'
                ],
                "es" => [
                    'title' => 'Prueba 4',
                    'description' => 'prueba 987'
                ],
                "fr" => [
                    'title' => 'Test 4',
                    'description' => 'test 987'
                ]
            ]
        ];

        $response = $this->actingAs($this->user)->putJson('api/subnationals/subnationals/' . $regionCreated->id, $data)
            ->assertStatus(202);

        $regionUpdated = json_decode($response->getContent());

        $this->actingAs($this->user)->deleteJson('api/subnationals/subnationals/' . $regionCreated->id)
            ->assertStatus(202);
    }
}
