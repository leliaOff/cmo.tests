<template>
    <div>
        <div class="loader" v-if="$store.state.loader">
            <div class="progress-container">
                <div class="progress">
                    <div class="progress-bar">
                        <div class="progress-shadow"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name:   'loader'
    }
</script>