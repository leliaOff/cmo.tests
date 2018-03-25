<template>
    <div>

        <h2>Тест привязан к справочникам: </h2>

        <div class="row" v-for="(item, key, i) in items" :key="item.id">
            <div class="col-sm-1 clearfix"><label class="input-title">{{ ( i + 1 ) }}</label></div>
            <div class="col-sm-8 clearfix"><label class="input-title">{{ item.directory.title }}</label></div>
            <div class="col-sm-3 clearfix align-right">
                <button type="button" class="btn btn-link" @click="deleteLink(item.id)">Отвязать</button>
            </div>
        </div>
        <div class="row" v-if="isCreate == true">
            <div class="col-sm-7 clearfix">
                <select v-model="newItem">
                    <option v-for="directory in directories" v-if="items[directory.alias] == undefined" :key="directory.id" :value="directory.id">{{ directory.title }}</option>
                </select>
            </div>
            <div class="col-sm-3 clearfix align-right">
                <button type="button" class="btn btn-link" @click="add">Привязать</button>
            </div>
            <div class="col-sm-2 clearfix align-right">
                <button type="button" class="btn btn-link" @click="cansel">Отменить</button>
            </div>
        </div>
        <div class="row" v-if="isCanAddNewLink">
            <div class="col-sm-12 clearfix align-right">
                <button type="button" class="btn btn-success" @click="addForm">Новая привязка</button>
            </div>
        </div>

        <h2>Ссылки на тест:</h2>
        
        <div class="row">
            <div class="col-sm-12 clearfix"><div class="alert alert-info">Просто кликните на ссылку, чтобы скопировать её в буфер обмена</div></div>
            <div class="col-sm-12 clearfix"><input v-model="search" class="form-control" placeholder="введите название элемента справочника что бы найти его в списке"/></div>
            <div class="col-sm-12 clearfix"><br/></div>
        </div>
        <div class="row" v-for="(link, i) in filteredLinks" :key="i">
            <div class="col-sm-8 clearfix"><label class="input-title">{{ link.title }}</label></div>
            <div class="col-sm-4 clearfix align-right">
                <input readonly :value="link.link" @click="copyLink($event.target)" class="form-control" />
            </div>
        </div>

    </div>
</template>

<script>

    export default {

        props: ['testId'],

        data() {

            return {
                items           : {},
                directories     : {},
                isCreate        : false,
                newItem         : 0,
                links           : [],
                search          : ''
            }

        },

        computed: {

            isCanAddNewLink() {
                return (Object.keys(this.items).length != Object.keys(this.directories).length);
            },

            filteredLinks() {
                return this.links.filter(item => {
                    if(item.title.toLowerCase().indexOf(this.search.toString().toLowerCase()) != -1) return item;
                    for(let key in item.data) {
                        let value = item.data[key];
                        if(typeof(value) == 'number') value = value.toString();
                        if(typeof(value) != 'string') continue;
                        if(value.toLowerCase().indexOf(this.search.toString().toLowerCase()) != -1) return item;
                    }
                });
            },

        },

        methods: {
            
            /* Список привязанных справочников */
            list() {
                
                this.$store.state.loader = true;
                axios.get(window.baseurl + 'testsLinks/' + this.testId).then(response => {
                    this.items = response.data;
                    this.$store.state.loader = false;
                    this.getTestsLinks();
                }).catch(error => {
                    this.$store.state.loader = false;
                    console.log(error);
                });

            },

            /* Добавить форму для нового справочника */
            addForm() {
                this.isCreate = true;
            },

            /* Добавить ссылку */
            add() {

                this.isCreate = false;
                if(this.newItem == 0) return;

                this.$store.state.loader = true;
                axios.get(window.baseurl + 'testsLinkInsert/' + this.testId + '/' + this.newItem).then(response => {
                    this.newItem = 0;
                    this.list();
                }).catch(error => {
                    this.$store.state.loader = false;
                    console.log(error);
                });

            },

            /* Отменить новую привязку */
            cansel() {
                this.isCreate = false;
                this.newItem = 0;
            },

            /* Удалить привязанный справочник */
            deleteLink(id) {
                this.$store.state.loader = true;
                axios.get(window.baseurl + 'testsLinkDelete/' + id).then(response => {
                    this.list();
                }).catch(error => {
                    this.$store.state.loader = false;
                    console.log(error);
                });
            },

            /* Список всех справочников */
            getDirectories() {

                this.$store.state.loader = true;
                axios.post(window.baseurl + 'directoriesList').then(response => {
                    this.directories = response.data.result;
                    this.$store.state.loader = false;
                }).catch(error => {
                    this.$store.state.loader = false;
                    console.log(error);
                });

            },

            /* Возвращаем ссылки в зависимости от привязанных справочников */
            getTestsLinks() {

                this.$store.state.loader = true;
                axios.get(window.baseurl + 'links/' + this.testId).then(response => {
                    this.links = response.data;
                    this.$store.state.loader = false;
                }).catch(error => {
                    this.$store.state.loader = false;
                    console.log(error);
                });

            },

            /* Поместить ссылку в буфер */
            copyLink(element) {
                element.select();
                document.execCommand('copy');
            }

        },

        mounted() {
            this.list();
            this.getDirectories();
        },
    }
</script>