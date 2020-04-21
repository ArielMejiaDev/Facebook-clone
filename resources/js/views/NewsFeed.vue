<template>

    <div class="flex flex-col items-center py-4">
        <NewPost />
        <Post v-for="(post, index) in posts.data" :key="index" :post="post" />
    </div>

</template>

<script>
import NewPost from '../components/NewPost'
import Post from '../components/Post'
export default {
    name: 'NewsFeed',
    components: {
        NewPost,
        Post,
    },
    data: () => {
        return {
            posts: null,
        }
    },
    mounted() {
        axios.get('/api/posts')
            .then(response => {
                this.posts = response.data
            })
            .catch(errors => console.log(errors.response.errors))
    }
}
</script>
