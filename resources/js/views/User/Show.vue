<template>
    <div class="flex flex-col items-center">
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

        </div>

        <p v-if="postsLoading">Loading ...</p>

        <Post v-else v-for="(post, index) in posts.data" :key="index" :post="post" />

        <p v-if="! postsLoading && posts.data.length < 1">No posts found. Get started...</p>

    </div>
</template>

<script>
import Post from '../../components/Post'
export default {
    name: 'UserShow',
    components: {
        Post,
    },
    data: () => {
        return {
            user: null,
            posts: null,
            userLoading: true,
            postsLoading: true,
        }
    },
    mounted() {
        axios.get(`/api/users/${this.$route.params.id}`)
            .then(response => this.user = response.data)
            .catch(errors => console.log(errors.response.errors))
            .finally(() => this.userLoading = false)

        axios.get(`/api/users/${this.$route.params.id}/posts`)
            .then(response => {
                console.log(response)
                this.posts = response.data
            })
            .catch(errors => console.log(errors.response.errors))
            .finally(() => this.postsLoading = false)
    }
}
</script>

<style>

</style>
