<template>

    <div class="w-2/3 bg-white rounded shadow mt-6 overflow-hidden">

        <div class="flex flex-col p-4">

            <div class="flex items-center">
                <div class="w-8">
                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&w=1000&q=80" alt="Avatar" class="h-8 w-8 object-cover rounded-full">
                </div>
                <div class="ml-6">
                    <div class="text-sm font-bold">Username</div>
                    <div class="text-xs text-gray-600">12 Mins</div>
                </div>
            </div>

            <div class="mt-4">
                <p>Not having fun at all.</p>
            </div>

        </div>

        <div class="w-full">
            <img
                src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjExMDk0fQ&w=1000&q=80"
                alt="Post image" class="w-full">

            <div class="p-4 text-sm text-gray-700 flex justify-between items-center">
                <div class="flex items-center">
                    <LikeIcon />
                    <p class="ml-2">Jane Smith and 124 others</p>
                </div>

                <div>
                    <p>123 comments</p>
                </div>
            </div>

            <div class="flex justify-between items-center m-4 border-1 border-gray-400">
                <button class="w-full rounded-lg hover:bg-gray-200 text-gray-600 focus:outline-none flex justify-center py-2">
                    <LikeIcon />
                    <p class="ml-2">Like</p>
                </button>
                <button class="w-full rounded-lg hover:bg-gray-200 text-gray-600 focus:outline-none flex justify-center py-2">
                    <CommentIcon />
                    <p class="ml-2">Comment</p>
                </button>
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
}
</script>
