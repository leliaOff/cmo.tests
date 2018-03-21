<template>
    <div>
        <select v-model="current">
            <option v-if="element.id != elementId" v-for="element in elements" :value="element.id">{{ element.title }}</option>
        </select>
    </div>
</template>

<script>

    export default {

        /* Выбранный элемент (объект) и индекс Условия */
        props: ['value', 'index', 'elementId'],

        data() {
            return {
                elements    : [],
                current     : this.value.id,
            }
        },

        watch: {
            current: function(current) {
                let element = this.getCurrentElement(current);
                this.$emit('onChange', element, this.index);
            },
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

            getCurrentElement(id) {
                let result = false;
                $.each(this.elements, (i, value) => {
                    if(value.id === id) return result = value;
                });
                return result;
            }
            
        },

        mounted() {
            this.getElements();
        }
    }
</script>