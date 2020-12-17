<template>
    <div v-if="showing" class="fixed w-full h-full top-0 left-0 flex items-center justify-center">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50" @click.self="close"></div>
        <div class="relative w-full max-w-2xl bg-white shadow-lg rounded-lg p-8 divide-y divide-solid">
            <div class="flex items-center mb-4">
                <button
                    aria-label="close"
                    class="absolute top-0 right-0 text-xl text-gray-500 my-2 mx-4"
                    @click.prevent="close"
                >
                    ×
                </button>
                <h2 class="flex-grow mb-2 uppercase font-bold text-black">Add Balance Entry</h2>
            </div>
            <div class="grid grid-cols-3 gap-4 mb-4 px-4 py-12 bg-white">
                <div class="flex flex-col mb-4">
                    <label class="mb-2 uppercase font-bold text-grey-darkest">Label</label>
                    <input v-model="transaction.label" class="border py-2 px-3 text-grey-darkest rounded-md"
                           name="label" type="text">
                </div>
                <div class="flex flex-col mb-4">
                    <label class="mb-2 uppercase font-bold text-grey-darkest">Date</label>
                    <datetime v-model="transaction.date" class="border py-2 px-3 text-grey-darkest rounded-md"
                              format="YYYY-MM-DD H:i"></datetime>
                </div>
                <div class="flex flex-col mb-4">
                    <label class="mb-2 uppercase font-bold text-grey-darkest" for="price">Amount</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="sm:text-sm text-gray-500 w-6 text-center">
                        $
                      </span>
                        </div>
                        <input id="price" v-model="transaction.amount"
                               class="border pl-8 pr-1 py-2 rounded-md text-grey-darkest w-40" name="price"
                               placeholder="0.00" type="number">
                    </div>
                </div>

            </div>
            <div class="flex flex-row-reverse mb-4 px-4 py-2 bg-white">
                <button
                    class="px-8 py-4 flex items-center mr-4 bg-blue-700 rounded-md text-white text-xs font-bold uppercase tracking-tight"
                    @click="save">Save Entry
                </button>
                <button class="px-8 py-4 rounded-md text-blue-700 bg-blue-400 hover:bg-blue-500 mx-6" @click="close">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import datetime from 'vuejs-datetimepicker';

export default {
    components: {datetime},
    props: {
        showing: {
            required: true,
            type: Boolean
        }
    },
    data() {
        return {
            transaction: {
                label: "",
                amount: 0,
                date: moment().format('YYYY-MM-DD H:mm')
            }
        }
    },
    methods: {
        resetTransaction() {
            this.transaction = {
                label: "",
                amount: 0,
                date: moment().format('YYYY-MM-DD H:mm')
            };
        },
        close() {
            this.resetTransaction();
            this.$emit('close');
        },
        save() {
            this.transaction.amount = parseFloat(this.transaction.amount);
            this.$emit('save-transaction', this.transaction);
            this.close();
        },
    }
};
</script>
<style>

</style>
