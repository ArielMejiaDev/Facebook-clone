<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserCanViewProfileTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_user_profiles()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create(), 'api');

        $posts = factory(Post::class, 2)->create();

        $response = $this->get("/api/users/{$user->id}");

        $response->assertStatus(200)->assertJson([
            'data' => [
                'type' => 'users',
                'id' => $user->id,
                'attributes' => [
                    'name' => $user->name,
                ]
            ],
                'links' => [
                    'self' => url("/users/{$user->id}")
                ]
        ]);
    }
    /** @test */
    public function a_user_can_fetch_posts_for_a_profile()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create(), 'api');

        $posts = factory(Post::class, 2)->create(['user_id' => $user->id]);

        $response = $this->get("/api/users/{$user->id}/posts");

        $response->assertStatus(200)->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'posts',
                        'id' => $posts->last()->id,
                        'attributes' => [
                            'body' => $posts->last()->body,
                            'posted_by' => [
                                'data' => [
                                    'type' => 'users',
                                    'id' => $user->id,
                                    'attributes' => [
                                        'name' => $user->name
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => url("posts/{$posts->last()->id}")
                    ]
                ],
                [
                    'data' => [
                        'type' => 'posts',
                        'id' => $posts->first()->id,
                        'attributes' => [
                            'body' => $posts->first()->body,
                        ]
                    ],
                    'links' => [
                        'self' => url("/posts/{$posts->first()->id}")
                    ]
                ],
            ]
        ]);
    }
}
