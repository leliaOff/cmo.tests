<template>
    <div>
        <select v-model="current">
            <option v-for="element in elements" :value="element">{{ element.title }}</option>
        </select>
    </div>
</template>

<script>

    export default {

        props: ['value'],

        data() {
            return {
                elements    : [],
                current     : 0,
                condition   : this.value
            }
        },

        watch: {
            current: function() {
                this.$emit('onChange', this.current, this.condition);
            }
        },

        methods: {

            getElements() {
                
                axios.post(window.baseurl + 'frontGetTest', {
                    
                    id: this.$route.params.id, user: 0

                }).then(response => {
                    
                    if(response.data.status == 'success') {
                        this.elements = Object.assign({}, response.data.elements);
                    }

                }).catch(error => {
                    // ...
                });

            },
            
        },

        mounted() {
            this.getElements();
        }
    }
</script>