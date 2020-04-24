<?php

namespace Tests\Feature;

use App\Friend;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FriendsTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function a_user_can_send_a_friend_request()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create(), 'api');

        $anotherUser = factory(User::class)->create();

        $response = $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $friendRelation = Friend::first();

        $this->assertNotNull($friendRelation);

        $this->assertEquals($anotherUser->id, $friendRelation->friend_id);

        $this->assertEquals($user->id, $friendRelation->user_id);

        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRelation->id,
                'attributes' => [
                    // 'user_id' => $user->id,
                    // 'friend_id' => $anotherUser->id,
                    'confirmed_at' => null,
                ]
            ],
            'links' => [
                'self' => url("/users/{$anotherUser->id}")
            ]
        ]);
    }

    /** @test */
    public function a_user_can_send_a_friend_request_only_once()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create(), 'api');

        $anotherUser = factory(User::class)->create();

        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $friendRelations = Friend::all();

        $this->assertCount(1, $friendRelations);
    }

    /** @test */
    public function only_valid_users_can_be_friend_requested()
    {

        $this->actingAs($user = factory(User::class)->create(), 'api');

        $response = $this->post('/api/friend-request', [
            'friend_id' => 123,
        ])->assertStatus(404);

        $this->assertNull(Friend::first());

        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title'=> 'User not found',
                'detail' => 'Unable to locale the user with the given information'
            ]
        ]);
    }

    /** @test */
    public function friend_request_can_be_accepted()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create(), 'api');

        $anotherUser = factory(User::class)->create();

        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $response = $this->actingAs($anotherUser, 'api')
            ->post('/api/friend-request-response', [
                'user_id' => $user->id,
                'status' => 'accepted'
            ])->assertStatus(200);

        $friendRelation = Friend::first();

        $this->assertNotNull($friendRelation->confirmed_at);

        $this->assertNotNull($friendRelation->status);

        $this->assertInstanceOf(Carbon::class, $friendRelation->confirmed_at);

        $this->assertEquals(now()->startOfSecond(), $friendRelation->confirmed_at);

        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRelation->id,
                'attributes' => [
                    // 'user_id'   => $user->id,
                    // 'friend_id' => $friendRelation->id,
                    'friend_id'    => $anotherUser->id,
                    'user_id'      => $user->id,
                    'confirmed_at' => $friendRelation->confirmed_at->diffForHumans(),
                ]
            ],
            'links' => [
                'self' => url("/users/{$anotherUser->id}")
            ]
        ]);
    }

    /** @test */
    public function only_valid_friend_requests_can_be_accepted()
    {
        // $this->withoutExceptionHandling();

        $anotherUser = factory(User::class)->create();

        $response = $this->actingAs($anotherUser, 'api')
            ->post('/api/friend-request-response', [
                'user_id' => 123,
                'status' => 'accepted'
            ])->assertStatus(404);

        $this->assertNull(Friend::first());

        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title'=> 'Friend request not found',
                'detail' => 'Unable to locale the friend request  with the given information'
            ]
        ]);
    }

    /** @test */
    public function only_the_recipient_can_accept_the_friend_request()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $anotherUser = factory(User::class)->create();

        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);


        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->post('/api/friend-request-response', [
                'user_id' => $user->id,
                'status' => 'accepted'
            ])->assertStatus(404);

        $friendRelation = Friend::first();

        $this->assertNull($friendRelation->confirmed_at);
        $this->assertNull($friendRelation->status);

        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title'=> 'Friend request not found',
                'detail' => 'Unable to locale the friend request  with the given information'
            ]
        ]);
    }

    /** @test */
    public function a_friend_id_is_required_for_friend_request()
    {
        // This test a custom error response without form request
        // ------------------------------------------------------------------------------------------------------
        // this is an aproach use only if custom error response is required
        // tests a custom error response format
        // this is created with a Laravel exception class
        // ans catched by a custom class invoked in the controller that validates with request() validator inline
        // then catched the ValidationException class of Laravel and throw the exception class
        // adding to controller just one invoke to (new CustomValidation)->validate(['friend_id' => 'required']);
        // $response = $this->actingAs($user = factory(User::class)->create(), 'api')
        //     ->post('/api/friend-request', [
        //         'friend_id' => '',
        // ])->assertJson([
        //     'errors' => [
        //         'meta' => [
        //             'friend_id' => [
        //                 'The friend id field is required.',
        //             ]
        //         ]
        //     ]
        // ]);

        // // dd( $response->getContent() );
        // $errorMessage = json_decode($response->getContent(), true);
        // $this->assertArrayHasKey('friend_id', $errorMessage['errors']['meta']);

        // Using default Laravel validations
        // ------------------------------------------------------

        $this->actingAs(factory(User::class)->create(), 'api')
            ->json('post', '/api/friend-request', [
                'friend_id' => '',
            ])->assertJson([
                'errors' => [
                    'friend_id' => [
                        'The friend id field is required.',
                    ]
                ]
            ]
        );

    }

    /** @test */
    public function a_user_id_and_status_are_required_for_friend_request_responses()
    {
        $response = $this->actingAs($user = factory(User::class)->create(), 'api')
            ->json('post', '/api/friend-request-response', [
                'friend_id' => '',
                'status'    => '',
            ]
        )->assertStatus(422)->assertJson([
                "errors" => [
                    "user_id" => [
                        "The user id field is required."
                    ],
                    "status" => [
                        "The status field is required."
                    ]
                ]
        ]);
    }

    /** @test */
    public function a_friendship_is_retrived_when_fetching_the_profile()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();
        $friendRequest = Friend::create([
            'user_id' => $user->id,
            'friend_id' => $anotherUser->id,
            'confirmed_at' => now()->subDay(),
            'status' => 'accepted',
        ]);

        $this->get('/api/users/'.$anotherUser->id)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'friendship' => [
                            'data' => [
                                'friend_request_id' => $friendRequest->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago',
                                ]
                            ]
                        ]
                    ]
                ],
            ]
        );
    }

    /** @test */
    public function an_inverse_friendship_is_retrived_when_fetching_the_profile()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $anotherUser = factory(User::class)->create();
        $friendRequest = Friend::create([
            'friend_id' => $user->id,
            'user_id' => $anotherUser->id,
            'confirmed_at' => now()->subDay(),
            'status' => 'accepted',
        ]);

        $this->get('/api/users/'.$anotherUser->id)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'friendship' => [
                            'data' => [
                                'friend_request_id' => $friendRequest->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago',
                                ]
                            ]
                        ]
                    ]
                ],
            ]
        );
    }

    /** @test */
    public function friend_request_can_be_ignored()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create(), 'api');

        $anotherUser = factory(User::class)->create();

        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $response = $this->actingAs($anotherUser, 'api')
            ->delete('/api/friend-request-response/delete', [
                'user_id' => $user->id,
            ])->assertStatus(204);

        $friendRelation = Friend::first();

        $this->assertNull($friendRelation);

        $response->assertNoContent();
    }

    /** @test */
    public function only_the_recipient_can_ignore_the_friend_request()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $anotherUser = factory(User::class)->create();

        $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
        ])->assertStatus(200);

        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->delete('/api/friend-request-response/delete', [
                'user_id' => $user->id,

            ])->assertStatus(404);

        $friendRelation = Friend::first();

        $this->assertNull($friendRelation->confirmed_at);
        $this->assertNull($friendRelation->status);

        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title'=> 'Friend request not found',
                'detail' => 'Unable to locale the friend request  with the given information'
            ]
        ]);
    }

    /** @test */
    public function a_user_id_is_required_for_ignoring_a_friend_request_responses()
    {
        $response = $this->actingAs($user = factory(User::class)->create(), 'api')
            ->json('delete', '/api/friend-request-response/delete', [
                'friend_id' => '',
            ]
        )->assertStatus(422)->assertJson([
                "errors" => [
                    "user_id" => [
                        "The user id field is required."
                    ]
                ]
        ]);
    }
}
