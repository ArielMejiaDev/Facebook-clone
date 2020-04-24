const state = {
    user: null,
    userStatus: null,
}

const getters = {
    user: state => {
        return state.user
    },
    friendship: state => {
        return state.user.data.attributes.friendship
    },
    friendButtonText: (state, getters, rootState) => {

        // if is my profile not show buttons to add as a friend
        if (rootState.User.user.data.id === getters.user.data.id) {
            return ''
        }

        if (getters.friendship === null) {
            return 'Add friend'
        }

        // if is not confirmed or the friend (the reciepient of friend request) id does not match with the currently authenticated user
        if (getters.friendship.data.attributes.confirmed_at === null
            && getters.friendship.data.attributes.friend_id !== rootState.User.user.data.id) {
            return 'Pending'
        }

        // if is confirmed
        if (getters.friendship.data.attributes.confirmed_at !== null) {
            return ''
        }

        return 'accepted'
    },
    status: state => {
        return {
            user: state.userStatus,
            posts: state.postsStatus
        }
    }
}

const actions = {
    // get the profile user data
    fetchUser({commit, dispatch}, id ) {

        commit('setUserStatus', 'loading')

        axios.get(`/api/users/${id}`)
        .then(response => {
            commit('setUser', response.data)
            commit('setUserStatus', 'success')
        })
        .catch(errors => {
            console.log(errors.response.errors)
            commit('setUserStatus', 'error')
        })

    },
    // send friend request
    sendFriendRequest({commit, getters}, id) {
        if (getters.friendButtonText !== 'Add friend') {
            return
        }
        axios.post('/api/friend-request', { 'friend_id':id })
            .then(response => {
                commit('setUserFriendship', response.data)
            })
            .catch(errors => {
                console.log(errors.response.errors)
            })
    },
    // send friend request
    acceptFriendRequest({commit, state}, id) {
        axios.post('/api/friend-request-response', { 'user_id':id, 'status': 'accepted' })
            .then(response => {
                commit('setUserFriendship', response.data)
            })
            .catch(errors => {
                console.log(errors.response.errors)
            })
    },
    // send friend request
    ignoreFriendRequest({commit, state}, id) {
        axios.delete('/api/friend-request-response/delete', { data: { 'user_id':id } } )
            .then(response => {
                commit('setUserFriendship', null)
            })
            .catch(errors => {
                console.log(errors.response.errors)
            })
    },
}

const mutations = {
    setUser(state, user) {
        state.user = user
    },
    setUserFriendship(state, friendship) {
        state.user.data.attributes.friendship = friendship
    },
    setUserStatus(state, userStatus) {
        state.userStatus = userStatus
    },
}

export default {
    state, getters, actions, mutations
}
