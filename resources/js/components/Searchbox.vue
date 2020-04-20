<template>
    <div class="ml-2 relative">

        <div class="absolute text-gray-700 hover:text-blue-700">

            <svg viewBox="0 0 24 24" class="fill-current w-5 h-5 mt-2 ml-2">
                <path fill-rule="evenodd" d="M20.2 18.1l-1.4 1.3-5.5-5.2 1.4-1.3 5.5 5.2zM7.5 12c-2.7 0-4.9-2.1-4.9-4.6s2.2-4.6 4.9-4.6 4.9 2.1 4.9 4.6S10.2 12 7.5 12zM7.5.8C3.7.8.7 3.7.7 7.3s3.1 6.5 6.8 6.5c3.8 0 6.8-2.9 6.8-6.5S11.3.8 7.5.8z" clip-rule="evenodd"/>
            </svg>

        </div>

        <input type="text" class="bg-gray-200 rounded-full focus:outline-none focus:shadow-outline pl-8 h-8 text-sm" placeholder="Search Facebook">

    </div>
</template>

<script>
export default {
    name: 'Searchbox',
}
</script>
