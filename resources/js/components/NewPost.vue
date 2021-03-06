<template>

    <div class="bg-white rounded shadow w-2/3 p-4">
        <div class="flex justify-between items-center">
            <div>
                <img
                    :src="authUser.data.attributes.profile_image.data.attributes.path"
                    alt="Avatar"
                    class="h-8 w-8 object-cover rounded-full">
            </div>
            <div class="flex-1 flex mx-2">
                <input v-model="postMessage" type="text" name="body" class="w-full pl-4 h-8 rounded text-sm bg-gray-200 focus:outline-none focus:shadow-outline" placeholder="Add a new post">
                <transition name="fade">
                    <button
                        class="ml-2 bg-gray-200 px-3 py-1 rounded-full focus:outline-none focus:shadow-outline"
                        v-if="postMessage"
                        @click="postHandler">
                        Post
                    </button>
                </transition>
            </div>
            <div>
                <button ref="postImage" class="bg-gray-200 rounded-full p-2 focus:outline-none focus:shadow-outline dz-clickable" >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="fill-current w-5 h-5 dz-clickable focus:outline-none"><path d="M21.8 4H2.2c-.2 0-.3.2-.3.3v15.3c0 .3.1.4.3.4h19.6c.2 0 .3-.1.3-.3V4.3c0-.1-.1-.3-.3-.3zm-1.6 13.4l-4.4-4.6c0-.1-.1-.1-.2 0l-3.1 2.7-3.9-4.8h-.1s-.1 0-.1.1L3.8 17V6h16.4v11.4zm-4.9-6.8c.9 0 1.6-.7 1.6-1.6 0-.9-.7-1.6-1.6-1.6-.9 0-1.6.7-1.6 1.6.1.9.8 1.6 1.6 1.6z"/></svg>
                </button>
            </div>
        </div>

        <div class="dropzone-previews">

            <div id="dz-template" class="hidden">
                <div class="dz-preview dz-file-preview mt-4">
                    <div class="dz-details">
                        <img data-dz-thumbnail class="w-32 h-32" >
                        <button data-dz-remove class="text-xs text-red-500 font-bold">Remove</button>
                    </div>
                    <div class="dz-progress">
                        <span class="dz-upload" data-dz-upload></span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</template>

<script>
import _ from 'lodash'
import { mapGetters } from 'vuex'
import Dropzone from 'dropzone'

export default {
    name: 'NewPost',
    data: () => {
        return {
            dropzone: null,
        }
    },

    mounted() {
        this.dropzone = new Dropzone(this.$refs.postImage, this.settings)
    },

    computed: {
        // this is like a v-model but binded directly to a module state of vuex by getters and setters directly
        postMessage: {
            get() {
                return this.$store.getters.postMessage
            },
            // without debounce
            // set(postMessage) {
            //     this.$store.commit('updateMessage', postMessage)
            // },
            set: _.debounce(function(postMessage) {
                this.$store.commit('updateMessage', postMessage)
            }, 300)
        },
        ...mapGetters({
            authUser: 'authUser',
        }),
        settings() {
            return {
                paramName: 'image',
                url: '/api/posts',
                acceptedFiles: 'image/*',
                clickable: '.dz-clickable',
                autoProcessQueue: false,
                previewsContainer: '.dropzone-previews',
                previewTemplate: document.querySelector('#dz-template').innerHTML,
                maxFiles: 1,
                params: {
                    width: 1000,
                    height: 1000,
                },
                headers: {
                    'X-CSRF-TOKEN': document.head.querySelector('meta[name=csrf-token]').content
                },
                sending: (file, xhr, formData) => {
                    formData.append('body', this.$store.getters.postMessage)
                },
                success: (event, response) => {
                    this.dropzone.removeAllFiles()
                    this.$store.commit('pushPost', response)
                },
                maxfilesexceeded: file => {
                    this.dropzone.removeAllFiles()
                    this.dropzone.addFile(file)
                }
            }
        }
    },
    methods: {
        postHandler() {

            if (this.dropzone.getAcceptedFiles().length) {
                this.dropzone.processQueue()
            }
            this.$store.dispatch('postMessage')

            this.$store.commit('updateMessage', '')
        }
    }
}
</script>

<style scoped>
    /* order of the animation class goes
    .fade-enter
    .fade-enter-active
    .fade-leave-active
    .fade-leave-to */

    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s;
        scale: 0;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0;
        scale: 1;
    }
</style>
