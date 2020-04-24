<template>

    <div class="flex flex-col items-center py-4" v-if="posts">
        <NewPost />
        <p v-if="newsStatus.newsPostsStatus === 'loading'">Loading posts ...</p>
        <Post v-else v-for="(post, index) in posts.data" :key="index" :post="post" />
    </div>

</template>

<script>
import NewPost from '../components/NewPost'
import Post from '../components/Post'
import { mapGetters } from 'vuex'

export default {
    name: 'NewsFeed',
    components: {
        NewPost,
        Post,
    },
    mounted() {
        this.$store.dispatch('fetchNewsPosts')
    },
    computed: {
        ...mapGetters({
            posts: 'posts',
            newsStatus: 'newsStatus'
        })
    }
}
</script>
