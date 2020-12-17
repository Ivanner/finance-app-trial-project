<template>
    <!--  Days  -->
    <div>
        <div class="flex items-center mb-4 pl-2 pr-4">
            <span
                class="flex-grow text-gray-500 font-bold text-sm uppercase tracking-tight">{{
                    day.date | formatHeaderDate
                }}</span>
            <span :class="className" class="text-lg font-bold">{{ day.amount | formatAmount }}<span
                class="text-sm">.{{ day.amount | cents }}</span></span>
        </div>

        <div>
            <transaction v-for="(transaction, index) in day.transactions" v-bind:key="transaction.id"
                         v-bind:index="index"
                         v-bind:transaction="transaction" v-on:delete-transaction="deleteTransaction"
                         v-on:update-transaction="updateTransaction">
            </transaction>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import Transaction from "./Transaction";

export default {
    components: {
        Transaction
    },
    props: ['day'],
    filters: {
        formatHeaderDate: function (value) {
            let today = moment(),
                parsedDate = moment(value);
            if (today.diff(value, 'days') === 0) {
                return 'Today';
            } else if (today.diff(value, 'days') === 1) {
                return 'Yesterday';
            } else {
                return parsedDate.format('ddd, D MMM').toUpperCase();
            }
        }
    },
    computed: {
        className() {
            return this.day.amount >= 0 ? 'text-green-500' : 'text-gray-500';
        }
    },
    methods: {
        deleteTransaction(index) {
            this.$emit('delete-transaction', this.day, index);
        },
        updateTransaction(transactionIndex, transaction) {
            this.$emit('update-transaction', this.day, transactionIndex, transaction);
        }
    }
}
</script>
