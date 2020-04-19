<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostToTimelineTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function a_user_can_post_a_text_post()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create(), 'api');

        $response = $this->post('/api/posts', [
            'data' => [
                'type' => 'posts',
                'attributes' => [
                    'body' => 'Testing Body'
                ]
            ]
        ]);

        $post = Post::first();

        $this->assertEquals($user->id, $post->user_id);
        $this->assertEquals('Testing Body', $post->body);

        $response->assertStatus(201)->assertJson([
            'data' => [
                'type' => 'posts',
                'id' => $post->id,
                'attributes' => [
                    'body' => 'Testing Body',
                    'posted_by' => [
                        'data' => [
                            'type' => 'users',
                            'id' => $user->id,
                            'attributes' => [
                                'name' => $user->name
                            ],
                        ],
                    ],
                ],
            ],
            'links' => [
                'self' => url("/api/posts/{$post->id}"),
            ]
        ]);



    }

    /** @test */
    public function a_user_can_retrive_posts()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create(), 'api');

        $posts = factory(Post::class, 2)->create(['user_id' => $user->id]);

        $this->get('/api/posts')->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'posts',
                        'id' => $posts->last()->id,
                        'attributes' => [
                            'body' => $posts->last()->body,
                        ],
                    ],
                ],
                [
                    'data' => [
                        'type' => 'posts',
                        'id'   => $posts->first()->id,
                        'attributes' => [
                            'body' => $posts->first()->body
                        ]
                    ]
                ],
            ],
            'links' => url('/api/posts')
        ]);

    }


    /** @test */
    public function a_user_can_only_retrieve_their_posts()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $postOfAnotherUser = factory(Post::class)->create();
        $response = $this->get('/api/posts');
        $response->assertStatus(200)->assertExactJson([
            'data' => [],
            'links' => url('/api/posts')
        ]);

    }

}
