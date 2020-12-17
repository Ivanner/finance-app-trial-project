<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/dashboard.js') }}" defer></script>
</head>
<body class="bg-gray-100 font-sans antialiased relative">
<div id="app">
    <div class="bg-white">
        <div class="container mx-auto px-8 py-4 flex flex-row">
            <a href="#" class="logo text-xl font-semibold flex-initial flex flex-row items-center tracking-wider">
                <img src="/images/logo.svg" class="mr-4"/>
                Your<span class="text-blue-600">Balance</span>
            </a>
            <div class="flex content-center flex-row flex-grow justify-end h-full">
                <a href="#" class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="block mx-auto my-auto" width="16" height="16"
                         viewBox="0 0 16 16">
                        <path class="notificationIcon" fill="#A0A5BA"
                              d="M10 14L6 14C6 15.1 6.9 16 8 16 9.1 16 10 15.1 10 14zM15 11L14.5 11C13.8 10.3 13 9.3 13 8L13 5C13 2.2 10.8 0 8 0 5.2 0 3 2.2 3 5L3 8C3 9.3 2.2 10.3 1.5 11L1 11C.4 11 0 11.4 0 12 0 12.6.4 13 1 13L15 13C15.6 13 16 12.6 16 12 16 11.4 15.6 11 15 11z"/>
                    </svg>
                </a>
                <a href="#" class="flex items-center font-bold text-sm text-gray-500">
                    <img src="/images/avatar.png" class="w-8 mx-4"/>
                    Molly Green
                </a>
            </div>
        </div>
    </div>

    <div class="mb-12 py-6 bg-gray-800">
        <div class="container mx-auto flex px-8">
            <div class="my-auto text-white flex flex-grow items-center">
                <h1 class="md:block hidden mr-4 text-2xl font-bold">
                    Your Balance
                </h1>

                <div class="flex flex-row" :class="{ 'cursor-not-allowed': importingFile }">
                    <a href="#"
                       class="flex items-center mr-4 px-3 py-2 bg-blue-700 rounded-md text-white text-xs font-bold uppercase tracking-tight"
                       :class="{ 'disabled pointer-events-none opacity-50': importingFile }"
                       @click.prevent="showAddTransactionModal = true" :disabled="importingFile">
                        Add Entry
                    </a>
                    <a href="#"
                       class="flex items-center mr-4 px-3 py-2 bg-blue-700 rounded-md text-white text-xs font-bold uppercase tracking-tight"
                       :class="{ 'disabled pointer-events-none opacity-50': importingFile }"
                       @click.prevent="showImportFileModal = true" :disabled="importingFile">
                        Import CSV
                    </a>
                </div>
            </div>
            <div class="my-auto text-right font-bold text-xs uppercase tracking-tight leading-7 text-gray-400">
                Total Balance
                <span class="block text-3xl font-normal" :class="className">
                    @{{ statement.totalBalance | formatAmountNoSign }}<span class="text-xl">.@{{ statement.totalBalance | cents }}</span>
                </span>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-8">
        <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-orange-400 text-center" v-if="importingFile">
            <span class="text-xl inline-block align-middle text-white">
                 <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                      viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
            </span>
            <span
                class="inline-block align-middle mr-8">We're importing @{{ totalImports }} balance entries. Sit tight.</span>
        </div>
        <day-transactions v-for="day in statement.days" v-bind:day="day" v-bind:key="day.date"
                          @update-transaction="onUpdateTransaction"
                          @delete-transaction="onDeleteTransaction"
                          @refresh-balance="refreshBalance">
        </day-transactions>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between" v-if="pagination.total > 0">
            <div>
                <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">@{{ pagination.from }}</span>
                    to
                    <span class="font-medium">@{{ pagination.to }}</span>
                    of
                    <span class="font-medium">@{{ pagination.total }}</span>
                    results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex -space-x-px" aria-label="Pagination">
                    <ul class="flex justify-items-center">
                        <li v-for="link in pagination.links" :key="link.label" v-if="link.url">
                            <a :class="[link.active? 'text-white  bg-blue-700':'text-blue-700  bg-white']"
                               class="flex items-center mr-4 px-3 py-2 rounded-md  text-xs font-bold uppercase tracking-tight"
                               href="#"
                               @click.stop="fetchData(link.url)"
                               v-html="link.label"
                            >
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

    </div>
    <add-transaction-modal :showing="showAddTransactionModal" @close="showAddTransactionModal = false"
                           @save-transaction="addTransaction"></add-transaction-modal>

    <import-file-modal :showing="showImportFileModal" @close="showImportFileModal = false"
                       @fetch="fetchData" @start-import="onFileImport"></import-file-modal>

</div>
</body>
</html>
