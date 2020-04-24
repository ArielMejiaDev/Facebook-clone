<?php

namespace Tests\Feature;

use App\Comment;
use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostCommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_comment_on_a_post()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create(['id' => 123]);
        $response = $this->post("/api/posts/{$post->id}/comment", [
            'body' => 'a great comment here',
        ])
            ->assertStatus(200);
        $comment = \App\Comment::first();
        $this->assertCount(1, \App\Comment::all());
        $this->assertEquals($user->id, $comment->user_id);
        $this->assertEquals($post->id, $comment->post_id);
        $this->assertEquals('a great comment here', $comment->body);
        $response->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'comments',
                        'id' => 1,
                        'attributes' => [
                            'commented_by' => [
                                'data' => [
                                    'id' => $user->id,
                                    'attributes' => [
                                        'name' => $user->name,
                                    ]
                                ]
                            ],
                            'body' => 'a great comment here',
                            'commented_at' => $comment->created_at->diffForHumans(),
                        ],
                    ],
                    'links' => [
                        'self' => url("/posts/{$post->id}")
                    ]
                ],
            ],
            'links' => [
                'self' => url('/posts')
            ]
        ]);
    }

    /** @test */
    public function a_body_is_required_to_leave_a_comment_on_a_post()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create(['id' => 123]);
        $response = $this->json('post', "/api/posts/{$post->id}/comment", [
            'body' => '',
        ])->assertStatus(422);

        $response->assertJson([
            "message" => "The given data was invalid.",
            "errors"=> [
                "body" => [
                "The body field is required."
                ]
            ]
        ]);
    }

    /** @test */
    public function posts_are_returned_with_comments()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create(['id' => 123, 'user_id' => $user->id]);
        $this->post("/api/posts/{$post->id}/comment", [
            'body' => 'a great comment here',
        ])->assertStatus(200);
        $response = $this->get('/api/posts');
        $comment = Comment::first();
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'data' => [ // posts collection
                            'type' => 'posts',
                            'attributes' => [
                                'comments' => [ // comments collection
                                    'data' => [
                                        [
                                            'data' => [ // single comment
                                                'type' => 'comments',
                                                'id' => 1,
                                                'attributes' => [
                                                    'commented_by' => [
                                                        'data' => [
                                                            'id' => $user->id,
                                                            'attributes' => [
                                                                'name' => $user->name,
                                                            ]
                                                        ]
                                                    ],
                                                    'body' => 'a great comment here',
                                                    'commented_at' => $comment->created_at->diffForHumans(),
                                                ],
                                            ],
                                            'links' => [
                                                'self' => url("/posts/{$post->id}")
                                            ]
                                        ]
                                    ],
                                    'comment_count' => 1,
                                ],
                            ]
                        ],
                    ]
                ]
            ]);
    }
}
