<?php

namespace Tests\Unit\Renderer;

use App\Classes\RcnApi\RcnApiClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Psr\Log\NullLogger;
use Tests\TestCase;

class RegionsTest extends TestCase
{

    /** @var \App\Models\Access\User\User */
    protected $user;
    private MockHandler $responses;
    private array $requests = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->responses = new MockHandler();
        $stack = HandlerStack::create($this->responses);
        $stack->push(Middleware::history($this->requests));
        $this->app->instance(RcnApiClient::class, new RcnApiClient(
            new Client(['base_uri' => 'https://api.example.test/v1/', 'handler' => $stack]),
            new NullLogger()
        ));

        $this->user = \Database\Factories\UserFactory::new()->create();
        $this->user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
    }

    public function testRegionsForOrganisation()
    {
        $regions = [['id' => 7, 'countryCode' => 'USA', 'title' => 'South']];
        $this->responses->append(new Response(200, [], json_encode($regions)));
        $this->actingAs($this->user)->getJson('api/subnationals/USA')
            ->assertOk()->assertExactJson($regions);
        $this->assertSame('/v1/subnationals/USA', $this->requests[0]['request']->getUri()->getPath());
    }

    public function testGetLanguageSpecificRegionsForOrganisation()
    {
        $regions = [['id' => 7, 'title' => 'Sur']];
        $this->responses->append(new Response(200, [], json_encode($regions)));
        $this->actingAs($this->user)->getJson('api/subnationals/USA/es')
            ->assertOk()->assertExactJson($regions);
        $this->assertSame('/v1/subnationals/USA/es', $this->requests[0]['request']->getUri()->getPath());
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

        $this->responses->append(new Response(201, [], json_encode(['id' => 7] + $data)));
        $response = $this->actingAs($this->user)->postJson('api/subnationals', $data)
            ->assertStatus(201);
        $regionCreated = json_decode($response->getContent());
        $this->assertSame(7, $regionCreated->id);
        $this->assertSame('POST', $this->requests[0]['request']->getMethod());
        $this->assertSame($data, json_decode((string) $this->requests[0]['request']->getBody(), true));

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

        $this->responses->append(new Response(202, [], json_encode(['id' => 7] + $data)));
        $response = $this->actingAs($this->user)->putJson('api/subnationals/subnationals/' . $regionCreated->id, $data)
            ->assertStatus(202);

        $regionUpdated = json_decode($response->getContent());
        $this->assertSame(7, $regionUpdated->id);
        $this->assertSame('PUT', $this->requests[1]['request']->getMethod());
        $this->assertSame($data, json_decode((string) $this->requests[1]['request']->getBody(), true));
        $this->responses->append(new Response(202));

        $this->actingAs($this->user)->deleteJson('api/subnationals/subnationals/' . $regionCreated->id)
            ->assertStatus(202);
        $this->assertSame('DELETE', $this->requests[2]['request']->getMethod());
        $this->assertSame('/v1/subnationals/subnationals/7', $this->requests[2]['request']->getUri()->getPath());
    }
}
