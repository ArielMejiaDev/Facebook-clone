const state = {
    user: null,
    userStatus: null,
}

const getters = {
    authUser: state => {
        return state.user
    }
}

const actions = {
    fetchAuthUser({commit, state}) {
        axios.get('/api/auth-user')
            .then(response => commit('setAuthUser', response.data))
            .catch(errors => console.log(errors.response.data.errors))
    }
}

const mutations = {
    setAuthUser(state, user) {
        state.user = user
    }
}

export default {
    state, getters, actions, mutations
}
