const state = {
    posts: null,
    postsStatus: null,
    postMessage: '',
}

const getters = {
    posts: state => {
        return state.posts
    },
    newsStatus: state => {
        return {
            postsStatus: state.postsStatus
        }
    },
    postMessage: state => {
        return state.postMessage
    },
}

const actions = {
    fetchNewsPosts({commit, state}) {
        commit('setPostsStatus', 'loading')
        axios.get('/api/posts')
        .then(response => {
            commit('setPosts', response.data)
            commit('setPostsStatus', 'success')
        })
        .catch(errors => {
            console.log(errors.response.errors)
            commit('setPostsStatus', 'success')
        })
    },
    postMessage({commit, state}) {
        commit('setPostsStatus', 'loading')
        axios.post('/api/posts', { body: state.postMessage})
        .then(response => {
            commit('pushPost', response.data)
            commit('setPostsStatus', 'success')
            commit('updateMessage', '')
        })
        .catch(errors => {
            console.log(errors.response.errors)
            // commit('setNewsPostsStatus', 'success')
        })
    },
    likePost({commit, state}, data) {
        axios.post(`/api/posts/${data.id}/like`)
            .then(response => {
                commit('pushLikes', { likes: response.data, index: data.index })

            })
    },
    commentPost({commit, state}, data) {
        axios.post(`/api/posts/${data.id}/comment`, {body: data.body})
            .then(response => {
                commit('pushComments', { comments: response.data, index: data.index })
            })
    },
     // fetch all post from the user that we are visiting
     fetchUserPosts({commit, state}, id) {
        commit('setPostsStatus', 'loading')
        axios.get(`/api/users/${id}/posts`)
        .then(response => {
            commit('setPosts', response.data)
            commit('setPostsStatus', 'success')
        })
        .catch(errors => {
            commit('setPostsStatus', 'error')
            console.log(errors.response.errors)
        })
    }
}

const mutations = {
    setPosts(state, posts) {
        state.posts = posts
    },
    setPostsStatus(state, status) {
        state.postsStatus = status
    },
    updateMessage(state, message) {
        state.postMessage = message
    },
    pushPost(state, post) {
        // push te post but at the top of the array
        state.posts.data.unshift(post)
    },
    pushLikes(state, data) {
        state.posts.data[data.index].data.attributes.likes = data.likes
    },
    pushComments(state, data) {
        state.posts.data[data.index].data.attributes.comments = data.comments
    },
}

export default {
    state, getters, actions, mutations
}
