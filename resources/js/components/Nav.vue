<template>
    <div class="bg-white h-12 flex items-center px-4 border-b border-gray-400 shadow-sm">
        <div class="w-1/3">
            <div class="flex items-center">
                <router-link to="/">
                    <Logo />
                </router-link>
                <Searchbox />
            </div>
        </div>
        <div class="w-1/3 flex justify-center items-center h-full">
            <router-link to="/" class="mx-6 border-b-2 border-blue-500 h-full w-12 flex justify-center items-center">
                <HomeIcon />
            </router-link>
            <router-link :to="`/users/${authUser.data.id}`" class="mx-6 border-b-2 border-white h-full w-12 flex justify-center items-center">
                <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&w=1000&q=80" alt="Avatar" class="h-8 w-8 object-cover rounded-full">
            </router-link>
            <router-link to="/" class="mx-6 border-b-2 border-white h-full w-12 flex justify-center items-center">
                <MessengerIcon />
            </router-link>
        </div>
        <div class="w-1/3 flex justify-end items-center">
            <ConfigIcon />
        </div>
    </div>
</template>

<script>
import Logo from './Logo'
import Searchbox from './Searchbox'
import HomeIcon from './HomeIcon'
import MessengerIcon from './MessengerIcon'
import ConfigIcon from './ConfigIcon'
import {mapGetters} from 'vuex'
export default {
    name: 'Nav',
    components: {
        Logo,
        Searchbox,
        HomeIcon,
        MessengerIcon,
        ConfigIcon,
    },
    computed: {
        ...mapGetters({
            authUser: 'authUser'
        })
    }
}
</script>

<style>

</style>
