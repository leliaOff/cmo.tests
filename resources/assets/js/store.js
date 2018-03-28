import Vuex from 'vuex';

export const store = new Vuex.Store({
    
    state: {

        /* Данные таблицы */
        rows: [],

        /* Текущие данные */
        current: { },

        /* Лоадер */
        loader: false,

        /* Глобальные модальные окна */
        sureModal: {
            title: 'Вы уверены?',
            text: 'Вы уверены?',
            action: function() { }
        },

        /* В каком разрезе данные */
        incisions: {},

        /* Результаты теста */
        results: []

    }
});