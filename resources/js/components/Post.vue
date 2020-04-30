<template>

    <div class="w-2/3 bg-white rounded shadow mt-6 overflow-hidden">

        <div class="flex flex-col p-4">

            <div class="flex items-center">
                <div class="w-8">
                    <img :src="post.data.attributes.posted_by.data.attributes.profile_image.data.attributes.path" alt="Avatar" class="h-8 w-8 object-cover rounded-full">
                </div>
                <div class="ml-6">
                    <div class="text-sm font-bold">{{ post.data.attributes.posted_by.data.attributes.name }}</div>
                    <div class="text-xs text-gray-600">{{ post.data.attributes.posted_at }}</div>
                </div>
            </div>

            <div class="mt-4">
                <p>{{ post.data.attributes.body }}</p>
            </div>

        </div>

            <div v-if="post.data.attributes.image.length" class="w-full">
                <img :src="post.data.attributes.image" alt="Post image" class="w-full">
            </div>

            <div class="p-4 text-sm text-gray-700 flex justify-between items-center">
                <div class="flex items-center">
                    <LikeIcon />
                    <p class="ml-2">{{ post.data.attributes.likes.like_count }} likes</p>
                </div>

                <div>
                    <p>{{ post.data.attributes.comments.comment_count }} comments</p>
                </div>
            </div>

            <div class="flex justify-between items-center m-4 border-1 border-gray-400">
                <button class="w-full rounded-lg focus:outline-none flex justify-center py-2"
                    :class="[post.data.attributes.likes.user_likes_post ? 'bg-blue-600 text-white' : '']"
                    @click="$store.dispatch('likePost', {id: post.data.id, index: $vnode.key})">
                    <LikeIcon />
                    <p class="ml-2">Like</p>
                </button>
                <button class="w-full rounded-lg text-gray-600 focus:outline-none flex justify-center py-2"
                    @click="comments = ! comments">
                    <CommentIcon />
                    <p class="ml-2">Comment</p>
                </button>
            </div>

            <div v-if="comments" class="border-t  p-4 pt-2">
                <div class="flex">
                    <input type="text" v-model="commentBody"
                        placeholder="Write your comment"
                        class="w-full pl-4 h-8 bg-gray-200 rounded focus:outline-none focus:shadow-outline">
                    <button v-if="commentBody"
                        class="bg-gray-200 ml-2 px-2 py-1 rounded-full focus:outline-none focus:shadow-outline"
                        @click="$store.dispatch('commentPost', { body: commentBody, id: post.data.id, index: $vnode.key }); commentBody = ''">
                        Add
                    </button>
                </div>

                <div
                    v-for="(comment, index) in post.data.attributes.comments.data"
                    :key="index"
                    class="flex my-4 items-center">
                    <div class="w-8">
                        <img
                            :src="comment.data.attributes.commented_by.data.attributes.profile_image.data.attributes.path" alt="Avatar" class="h-8 w-8 object-cover rounded-full">
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="bg-gray-200 rounded p-2 text-sm">
                            <a class="font-bold text-blue-700 mr-1" :href="`/users/${comment.data.attributes.commented_by.data.id}`">
                            {{ comment.data.attributes.commented_by.data.attributes.name }}
                            </a>
                            <p class="inline">
                                {{ comment.data.attributes.body }}
                            </p>
                        </div>
                        <div class="text-gray-800 text-xs ml-2 mt-1">
                            <p>{{ comment.data.attributes.commented_at }}</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</template>

<script>
import LikeIcon from './LikeIcon'
import CommentIcon from './CommentIcon'
export default {
    name: 'Post',
    components: {
        LikeIcon,
        CommentIcon,
    },
    props: [
        'post',
    ],
    data: () => {
        return {
            comments: false,
            commentBody: '',
        }
    }
}
</script>
