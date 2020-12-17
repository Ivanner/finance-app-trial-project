<template>
    <!--  Transactions  -->
    <div class="grid mb-4 px-4 py-2 shadow-md bg-white rounded-md divide-y-2 divide-solid">
        <div class="flex items-center mb-4" @mouseleave="hover = false" @mouseover="hover = true">
            <div class="flex-grow">
                <div class="font-bold" v-text="cachedTransaction.label"></div>
                <div class="text-xs text-gray-500">{{ cachedTransaction.date | formatDate }}</div>
            </div>
            <div v-if="hover" class="flex px-12">
                <a v-if="!editMode" class="uppercase px-2 text-blue-600 underline" href="#"
                   @click.prevent="editMode = true">Edit</a>
                <a class="uppercase px-2 text-blue-600 underline" href="#" @click.prevent="removeTransaction(index)">Delete</a>
            </div>
            <div :class="{'text-green-500': isPositive }" class="text-lg font-bold w-32 text-right">
                {{ cachedTransaction.amount | formatAmount }}<span
                class="text-sm">.{{ cachedTransaction.amount | cents }}</span>
            </div>
        </div>
        <div v-if="editMode" class="grid grid-cols-3 gap-4 mb-4 px-4 py-12 bg-white">
            <div class="flex flex-col mb-4">
                <label class="mb-2 uppercase font-bold text-grey-darkest">Label</label>
                <input v-model="cachedTransaction.label" class="border py-2 px-3 text-grey-darkest rounded-md"
                       name="label" type="text">
            </div>
            <div class="flex flex-col mb-4">
                <label class="mb-2 uppercase font-bold text-grey-darkest">Date</label>
                <datetime v-model="cachedTransaction.date" class="border py-2 px-3 text-grey-darkest rounded-md"
                          format="YYYY-MM-DD H:i" width="300px"></datetime>
            </div>
            <div class="flex flex-col mb-4">
                <label class="mb-2 uppercase font-bold text-grey-darkest" for="price">Amount</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="sm:text-sm text-gray-500 w-6 text-center">
                        $
                      </span>
                    </div>
                    <input id="price" v-model="cachedTransaction.amount"
                           class="border pl-8 pr-4 py-2 rounded-md text-grey-darkest" name="price" placeholder="0.00"
                           type="number">
                </div>
            </div>
        </div>
        <div v-if="editMode" class="flex flex-row-reverse mb-4 px-4 py-2 bg-white">
            <button
                class="px-8 py-4 flex items-center mr-4 bg-blue-700 rounded-md text-white text-xs font-bold uppercase tracking-tight"
                @click="save(index)">Update Entry
            </button>
            <button class="px-8 py-4 rounded-md text-blue-700 bg-blue-400 hover:bg-blue-500 mx-6" @click="cancel">
                Cancel
            </button>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import datetime from 'vuejs-datetimepicker';

export default {
    components: {datetime},
    props: ['transaction', 'index'],
    filters: {
        formatDate: function (value) {
            return moment(value).format('D MMM, YYYY [at]  hh:mm A');
        }
    },
    data() {
        return {
            hover: false,
            editMode: false,
            cachedTransaction: {}
        }
    },
    computed: {
        isPositive() {
            return this.transaction.amount >= 0;
        }
    },
    methods: {
        removeTransaction(index) {
            this.$emit('delete-transaction', index);
        },
        save(index) {
            this.cachedTransaction.amount = parseFloat(this.cachedTransaction.amount);
            this.$emit('update-transaction', index, this.cachedTransaction);
            this.editMode = false;
        },
        cancel() {
            this.cachedTransaction = Object.assign({}, this.transaction);
            this.editMode = false;
        }
    },
    mounted() {
        this.cachedTransaction = Object.assign({}, this.transaction);
    }
}
</script>
