<?php

namespace Tests\Feature;

use App\Http\Requests\EventTypeCreateRequest;
use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\EventType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventTypesTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function get_event_types()
    {
        $events = factory(EventType::class, 2)->create();

        $response = $this->getJson('/api/event-types/');

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                [
                    'name' => 'Other',
                    'icon' => 'general@3x.png',
                    'code' => 'other',
                    'url' => asset('storage/general@3x.png')

                ],
                [
                    'name' => $events[0]->name,
                    'icon' => $events[0]->icon,
                    'code' => $events[0]->code,
                    'url' => asset('storage/'.$events[0]->icon)

                ],
                [
                    'name' => $events[1]->name,
                    'icon' => $events[1]->icon,
                    'code' => $events[1]->code,
                    'url' => asset('storage/'.$events[1]->icon)
                ],
            ]
        ]);
    }

    /**
     * @test
     */
    public function create_new_event_type_as_3sc_admin_user()
    {
        $user = factory(User::class)->create();
        $user->attachRole(Role::where('name', '3SC Admin')->first());
        $this->_create_new_event_type_as_valid_user($user);
    }

    /**
     * @test
     */
    public function create_new_event_type_as_gdpc_admin_user()
    {
        $user = factory(User::class)->create();
        $user->attachRole(Role::where('name', 'GDPC Admin')->first());
        $this->_create_new_event_type_as_valid_user($user);
    }

    /**
     * @test
     */
    public function create_new_event_type_as_invalid_user_fails()
    {
        // test user with invalid roles
        $user = factory(User::class)->create();
        $user->attachRole(Role::where('name', 'API User')->first());
        $user->attachRole(Role::where('name', 'NS Admin')->first());
        $user->attachRole(Role::where('name', 'NS Editor')->first());
        $this->_create_new_event_type_as_invalid_user($user);

        // test requers with no user
        $this->_create_new_event_type_as_invalid_user(null);
    }

    /** @test */
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
        Storage::fake('public');
        Storage::disk('public')->assertMissing('event-name.png');

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
        $this->assertEquals(asset('storage/event-name.png'), $content->data->url);

        // Assert the file was stored...
        Storage::disk('public')->assertExists('event-name.png');

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
    private function _create_new_event_type_as_invalid_user(\Illuminate\Database\Eloquent\Model $user = null)
    {
        if ($user !== null) {
            $this->actingAs($user);
        }

        $this->assertEquals(1, EventType::all()->count());
        Storage::fake('public');
        Storage::disk('public')->assertMissing('event-name.png');

        $eventName = 'Event Name';

        $file = UploadedFile::fake()->image('icon.png');

        $response = $this->call('POST', '/api/event-types', [
            'name' => $eventName,
            'icon' => $file
        ]);

        // Assert expected response
        $response->assertStatus(403);

        // Assert the file was stored...
        Storage::disk('public')->assertMissing('event-name.png');

        // Assert data was saved properly
        $eventsOnDb = EventType::all();
        $this->assertEquals(1, $eventsOnDb->count());
    }


}
