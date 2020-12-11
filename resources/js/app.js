require('./bootstrap');

window.moment = require('moment');
window.Vue = require('vue');

Vue.component('day-transactions', require('./components/DayTransactions.vue').default);
Vue.component('add-transaction-modal', require('./components/AddTransactionModal.vue').default);
Vue.component('import-file-modal', require('./components/ImportFileModal.vue').default);
Vue.filter('formatAmount', function (value) {
    if (value >= 0) {
        return '+ $' + Math.abs(parseInt(value)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    } else {
        return '- $' + Math.abs(parseInt(value)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
});
Vue.filter('formatAmountNoSign', function (value) {
    if (value >= 0) {
        return '$' + Math.abs(parseInt(value)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    } else {
        return '- $' + Math.abs(parseInt(value)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
});
Vue.filter('cents', function (value) {
    return parseFloat(value).toFixed(2).toString().split(".")[1];
})
