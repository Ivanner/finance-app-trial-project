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
                <h2 class="flex-grow mb-2 uppercase font-bold text-black">Import Balance Entries</h2>
            </div>
            <div class="bg-white py-12">
                <div class="flex flex-col mb-4">
                    <label class="mb-2 uppercase font-bold text-grey-darkest">.csv file</label>
                    <input id="file" ref="file" class="border py-2 px-3 text-grey-darkest rounded-md" type="file"
                           @change="handleFileUpload"/>
                </div>
            </div>
            <div class="flex flex-row-reverse mb-4 px-4 py-2 bg-white">
                <button
                    class="px-8 py-4 flex items-center mr-4 bg-blue-700 rounded-md text-white text-xs font-bold uppercase tracking-tight"
                    @click="importFile">Import
                </button>
                <button class="px-8 py-4 rounded-md text-blue-700 bg-blue-400 hover:bg-blue-500 mx-6" @click="close">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        showing: {
            required: true,
            type: Boolean
        }
    },
    data() {
        return {
            file: ''
        }
    },
    methods: {
        importFile() {
            let that = this;
            // handle file changes
            const formData = new FormData();

            if (!this.file) return;

            formData.append('file', that.file);
            axios.post('transactions/import',
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            ).then(function (response) {
                if (response.data.message) {
                    alert(response.data.message);
                } else {
                    that.$emit('start-import', response.data);
                    that.close();
                }
            });
        },
        handleFileUpload(event) {
            this.file = event.target.files[0];
        },
        resetForm() {
            this.file = null;
        },
        close() {
            this.resetForm();
            this.$emit('close');
        },
    }
};
</script>
<style>

</style>
