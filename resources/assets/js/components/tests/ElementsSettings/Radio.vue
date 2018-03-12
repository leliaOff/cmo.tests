<template>
    <div>
        <easy-radio :name="'radio_' + id" :model="oldResult" :data="setting.rows" v-on:update="resultUpdate"></easy-radio>
        
        <!--
        <div class="radio-container">
            <div class="radio" v-for="(row, i) in setting.rows">
                <label>
                    <input type="radio" :name="'radio_' + id" :value="i" v-model="result" @click="resultUpdate" />
                    {{ row.value }}>
                    
                </label>
            </div>
        </div>
        -->
              
    </div>
</template>

<script>
    export default {

        props: ['setting', 'oldResult', 'id'],

        methods: {

            resultUpdate(index) {
                this.$emit('update', parseInt(index, 10));
            }
            
        },
    }
</script>