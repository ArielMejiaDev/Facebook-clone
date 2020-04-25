<template>
    <div>
        <img
            :src="imageObject.data.attributes.path"
            :alt="alt"
            ref="userImage"
            :class="classes">
    </div>
</template>

<script>
import Dropzone from 'dropzone'
import { mapGetters } from 'vuex'

export default {
    name: 'UploadableImage',
    data: () => {
        return {
            dropzone: null,
            uploadedImage: null,
        }
    },
    props: [
        'userImage',
        'imageWidth',
        'imageHeight',
        'location',
        'classes',
        'alt',
    ],
    computed:{
        ...mapGetters({
            authUser: 'authUser',
        }),
        settings() {
            return {
                paramName: 'image',
                url: '/api/user-images',
                acceptedFiles: 'image/*',
                params: {
                    'width': this.imageWidth,
                    'height': this.imageHeight,
                    'location': this.location,
                },
                headers: {
                    'X-CSRF-TOKEN': document.head.querySelector('meta[name=csrf-token]').content
                },
                success: (e, response) => {
                    // console.log(response)
                    this.uploadedImage = response
                    this.$store.dispatch('fetchAuthUser')
                    this.$store.dispatch('fetchUserPosts', this.$route.params.id)
                }
            }
        },
        imageObject() {
            // this computed get the response of new image uploaded to change the path dinamically
            // it load the uploaded image that is the new one but if this is not uploaded it returns
            // the original value null so it load the userImage prop that loads at the very beginning
            // when loads all the profile, if I change this to just use the dispatch an action to
            // fetch the current profile user with: this.$store.dispatch('fetchUser', this.$route.params.id)
            // because I work with this component it will re-render all the page and this is not the intended
            return this.uploadedImage || this.userImage
        }
    },
    methods: {
        isMyProfile() {
            return this.authUser.data.id.toString() === this.$route.params.id
        }
    },
    mounted() {
        if (this.isMyProfile()) {
            this.dropzone = new Dropzone(this.$refs.userImage, this.settings)
        }
    }
}
</script>

<style>

</style>
