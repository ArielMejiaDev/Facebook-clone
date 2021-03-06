<?php

namespace Tests\Feature;

use App\Friend;
use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostToTimelineTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function a_user_can_post_a_text_post()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create(), 'api');

        $response = $this->post('/api/posts', [
            // 'data' => [
            //     'type' => 'posts',
            //     'attributes' => [
            //         'body' => 'Testing Body'
            //     ]
            // ]
            'body' => 'Testing Body'
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
                'self' => url("/posts/{$post->id}"),
            ]
        ]);



    }

    /** @test */
    public function a_user_can_retrive_posts()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create(), 'api');

        $anotherUser = factory(User::class)->create();

        Friend::create([
            'user_id'      => $user->id,
            'friend_id'    => $anotherUser->id,
            'confirmed_at' => now(),
            'status'       => 'accepted',
        ]);

        $posts = factory(Post::class, 2)->create(['user_id' => $anotherUser->id]);

        $this->get('/api/posts')->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'posts',
                        'id' => $posts->last()->id,
                        'attributes' => [
                            'body' => $posts->last()->body,
                            'image' => url($posts->last()->image),
                            'posted_at' => $posts->last()->created_at->diffForHumans(),
                        ],
                    ],
                ],
                [
                    'data' => [
                        'type' => 'posts',
                        'id'   => $posts->first()->id,
                        'attributes' => [
                            'body' => $posts->first()->body,
                            'image' => url($posts->first()->image),
                            'posted_at' => $posts->first()->created_at->diffForHumans(),
                        ]
                    ]
                ],
            ],
            'links' => url('/posts')
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
            'links' => url('/posts')
        ]);

    }

    /** @test */
    public function a_user_can_post_a_text_post_with_an_image()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create(), 'api');

        $file = UploadedFile::fake()->image('user-post.jpg');

        $response = $this->post('/api/posts', [
            'body' => 'Testing Body',
            'image' => $file,
            'width' => 100,
            'height' => 100,
        ]);

        Storage::disk('public')->assertExists("post-images/{$file->hashName()}");

        // $post = Post::first();

        $response->assertStatus(201)->assertJson([
            'data' => [
                'attributes' => [
                    'body' => 'Testing Body',
                    'image' => url("post-images/{$file->hashName()}"),
                ],
            ],
        ]);



    }

}
