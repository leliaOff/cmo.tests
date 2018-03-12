<template>
    <div class="container manager">
        <manager-menu></manager-menu>
    </div>
</template>

<script>
    export default {

        mounted() {
            
            if(localStorage['manager_mode'] == undefined) localStorage['manager_mode'] = '/manager/tests';
            this.$router.push(localStorage['manager_mode']);

        },

    }
</script>