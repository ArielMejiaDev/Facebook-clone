## Install vuex

```bash
npm install vuex --save-dev
```

## Creates the config files

On "resources/js" directory add a store directory 

On "resources/js/store" add an index.js file and fill the file with the next content:

```js
import Vue from 'vue'
import Vuex from 'vuex'
import ExampleModule from './modules/example'

 Vue.use(Vuex)

 export default new Vuex.Store({
    modules: {
        ExampleModule,
    }
 })
```

Now we need to create the "ExampleModule file" in the directory that is set in the import

In "resources/js/store" add a new directory named "modules" and add a file there named example.js

```js 
const state = {
}

const getters = {
}

const actions = {
}

const mutations = {
}

export default {
    state, getters, actions, mutations
}

```

Now in your "resources/js/app.js" file import and use vuex

```js 
import Vue from 'vue'
import router from './router'
import store from './store'
import App from './components/App'

require('./bootstrap')

const app = new Vue({
    el: '#app',
    components: {
        App,
    },
    router,
    store
})
```

* Note: this example use vue router, so the "App" is a component that load first and add the <router-view></router-view> tags to load all views, just like the App.vue file works in VueCLI projects.

Now you can create any module add your "state", create any call to an API in "actions", set your states values in "mutations" and add "getters" to get the state values as you want just add modules in modules directory and import them to the "store/index.js" file.

If you want to get globally your getters add the "actions" in the App component as a mounted hook, note: App component or any component that works as the one that add the <router-view> tags.

Then to use in any component, just use a getter as mapGetter and a computed propery works perfect to load it.
