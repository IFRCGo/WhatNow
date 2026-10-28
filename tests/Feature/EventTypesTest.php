<?php

namespace Tests\Feature;

use App\Http\Requests\EventTypeCreateRequest;
use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\EventType;
use Illuminate\Http\UploadedFile;
use App\Classes\RcnApi\RcnApiClient;
use App\Classes\RcnApi\Resources\WhatNowResource;
use Tests\TestCase;

class EventTypesTest extends TestCase
{
    private $resource;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.bucket_url' => 'https://assets.example.test']);
        $this->grantRolePermissions(Role::ROLE_GDPC_ADMIN, ['hz-type-create']);
        $this->resource = \Mockery::mock(WhatNowResource::class);
        $this->mock(RcnApiClient::class)->shouldReceive('whatnow')->andReturn($this->resource);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function get_event_types()
    {
        $events = \Database\Factories\EventTypesFactory::new()->count(2)->create();

        $response = $this->getJson('/api/event-types/');

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                [
                    'name' => 'Other',
                    'icon' => 'general@3x.png',
                    'code' => 'other',
                    'url' => 'https://assets.example.test/general@3x.png'

                ],
                [
                    'name' => $events[0]->name,
                    'icon' => $events[0]->icon,
                    'code' => $events[0]->code,
                    'url' => 'https://assets.example.test/'.$events[0]->icon

                ],
                [
                    'name' => $events[1]->name,
                    'icon' => $events[1]->icon,
                    'code' => $events[1]->code,
                    'url' => 'https://assets.example.test/'.$events[1]->icon
                ],
            ]
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_new_event_type_as_ifrc_support_user()
    {
        $user = \Database\Factories\UserFactory::new()->create();
        $user->attachRole(Role::where('name', 'IFRC Support')->first());
        $this->_create_new_event_type_as_valid_user($user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_new_event_type_as_gdpc_admin_user()
    {
        $user = \Database\Factories\UserFactory::new()->create();
        $user->attachRole(Role::where('name', 'IFRC Admin')->first());
        $this->_create_new_event_type_as_valid_user($user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_new_event_type_as_invalid_user_fails()
    {
        // test user with invalid roles
        $user = \Database\Factories\UserFactory::new()->create();
        $user->attachRole(Role::where('name', 'API User')->first());
        $user->attachRole(Role::where('name', 'NS Admin')->first());
        $user->attachRole(Role::where('name', 'NS Editor')->first());
        $this->_create_new_event_type_as_invalid_user($user);

        // test requers with no user
        $this->_create_new_event_type_as_invalid_user(null);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_a_new_event_type_validations()
    {
        $request = new EventTypeCreateRequest();
        $this->assertSame([
            'name' => ['required'],
            'icon' => ['required', 'mimes:png', 'max:30']
        ], $request->rules());
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $user
     */
    private function _create_new_event_type_as_valid_user(\Illuminate\Database\Eloquent\Model $user)
    {
        $this->actingAs($user);

        $this->assertEquals(1, EventType::all()->count());

        $this->resource->shouldReceive('uploadFile')->once()->withArgs(function ($path, $name) {
            return is_file($path) && $name === 'event-name.png';
        });

        $eventName = 'Event Name';

        $file = UploadedFile::fake()->image('icon.png');

        $response = $this->call('POST', '/api/event-types', [
            'name' => $eventName,
            'icon' => $file
        ]);

        // Assert expected response
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => ['name', 'icon', 'code', 'url']
        ]);

        $content = json_decode($response->getContent());
        $this->assertEquals($eventName, $content->data->name);
        $this->assertEquals('event-name.png', $content->data->icon);
        $this->assertEquals('event-name', $content->data->code);
        $this->assertEquals('https://assets.example.test/event-name.png', $content->data->url);

        // Assert data was saved properly
        $eventsOnDb = EventType::all();
        $this->assertEquals(2, $eventsOnDb->count());
        $event = $eventsOnDb->where('name', $eventName)->first();
        $this->assertEquals($eventName, $event->name);
        $this->assertEquals('event-name.png', $event->icon);
        $this->assertEquals('event-name', $event->code);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $user
     */
    private function _create_new_event_type_as_invalid_user(?\Illuminate\Database\Eloquent\Model $user = null)
    {
        if ($user !== null) {
            $this->actingAs($user);
        }

        $this->assertEquals(1, EventType::all()->count());

        $eventName = 'Event Name';

        $file = UploadedFile::fake()->image('icon.png');

        $response = $this->call('POST', '/api/event-types', [
            'name' => $eventName,
            'icon' => $file
        ]);

        // Assert expected response
        $response->assertStatus(403);

        $this->resource->shouldNotHaveReceived('uploadFile');

        // Assert data was saved properly
        $eventsOnDb = EventType::all();
        $this->assertEquals(1, $eventsOnDb->count());
    }

}
