<template>
    <div class="flex flex-col items-center" v-if="status.user === 'success' && user">
        <div class="relative w-full mb-8">

            <div class="w-100 h-64 overflow-hidden z-10">
                <img
                    src="https://s1.best-wallpaper.net/wallpaper/m/1808/Art-picture-mountains-trees-sun-deer_m.jpg"
                    alt="Profile picture"
                    class="w-full object-cover">
            </div>

            <div class="absolute bottom-0 left-0 -mb-8 ml-12 z-20 flex items-center">

                <div class="w-32">
                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&w=1000&q=80"
                        alt="Profile picture"
                        class="object-cover h-32 w-32 border-4 border-gray-200 rounded-full shadow-lg">
                </div>

                <p v-if="user" class="text-2xl text-gray-100 ml-4">{{ user.data.attributes.name }}</p>

            </div>

            <div class="absolute bottom-0 right-0 mb-4 mr-12 z-20 flex items-center">
                <button v-if="friendButtonText && friendButtonText !== 'accepted'" class="py-1 px-3 bg-gray-400 rounded" @click="$store.dispatch('sendFriendRequest', $route.params.id)">
                    {{ friendButtonText }}
                </button>
                <button v-if="friendButtonText && friendButtonText === 'accepted'" class="py-1 px-3 bg-blue-500 rounded mr-2 text-white" @click="$store.dispatch('acceptFriendRequest', $route.params.id)">
                    Accepted
                </button>
                <button v-if="friendButtonText && friendButtonText === 'accepted'" class="py-1 px-3 bg-gray-400 rounded" @click="$store.dispatch('ignoreFriendRequest', $route.params.id)">
                    Ignore
                </button>
            </div>

        </div>

        <p v-if="status.posts === 'loading'">Loading ...</p>

        <p v-else-if="posts.length < 1">No posts found. Get started...</p>

        <Post v-else v-for="(post, index) in posts.data" :key="index" :post="post" />

    </div>
</template>

<script>
import Post from '../../components/Post'
import {mapGetters} from 'vuex'
export default {
    name: 'UserShow',
    components: {
        Post,
    },
    mounted() {

        this.$store.dispatch('fetchUser', this.$route.params.id)

        this.$store.dispatch('fetchUserPosts', this.$route.params.id)

    },

    computed: {
        ...mapGetters({
            user: 'user',
            posts: 'posts',
            status: 'status',
            friendButtonText: 'friendButtonText'
        })
    }
}
</script>

<style>

</style>
