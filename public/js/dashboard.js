document.addEventListener('DOMContentLoaded', function () {

    new Vue({
        el: '#app',
        data: {
            showAddTransactionModal: false,
            showImportFileModal: false,
            importingFile: false,
            file: '',
            totalImports: 0,
            statement: {
                totalBalance: 0,
                days: []
            },
            pagination: []
        },
        methods: {
            addTransaction(transaction) {
                const csrfToken = document.head.querySelector("[name~=csrf-token][content]").content;
                //Add
                fetch('transactions', {
                    method: 'post',
                    body: JSON.stringify(transaction),
                    headers: {
                        'content-type': 'application/json',
                        "X-CSRF-Token": csrfToken
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.message) {
                            alert(data.message);
                        } else {
                            transaction.id = data;
                            this.statement.totalBalance += parseFloat(transaction.amount);
                            // assign transaction to corresponding day
                            this.assignTransactionToDay(transaction);
                            // refresh global balance
                            this.refreshBalance();
                            alert('Transaction Added Successfully');
                        }
                    })
            },
            assignTransactionToDay(transaction) {
                let statement = this.statement,
                    transactionDate = moment(transaction.date).format('YYYY-MM-DD'),
                    dayIndex = statement.days.findIndex((element) => element.date === transactionDate) // find corresponding day;
                // if day does not exist, create it
                if (dayIndex === -1) {
                    // add day
                    statement.days.push({
                        date: transactionDate,
                        amount: 0,
                        transactions: []
                    });
                    // re-sort days
                    statement.days.sort((a, b) => moment(b.date) - moment(a.date));
                    dayIndex = statement.days.findIndex((element) => element.date === transactionDate)
                }
                // add transaction to day
                statement.days[dayIndex].transactions.push(transaction);
                statement.days[dayIndex].transactions.sort((a, b) => moment(b.date) - moment(a.date));
            },
            onDeleteDay(day) {
                let statement = this.statement,
                    index = statement.days.findIndex((element) => element.date === day.date);
                statement.days.splice(index, 1);
                this.refreshBalance();
            },
            onMoveTransaction(oldDate, transactionIndex) {
                let statement = this.statement,
                    dayIndex = statement.days.findIndex((element) => element.date === oldDate),
                    transaction = statement.days[dayIndex].transactions[transactionIndex];
                // remove from current day
                statement.days[dayIndex].transactions.splice(transactionIndex, 1);
                // if day doesn't have any more transactions, remove it
                if (statement.days[dayIndex].transactions.length === 0) {
                    this.onDeleteDay(statement.days[dayIndex]);
                }
                // assign transaction to new day
                this.assignTransactionToDay(transaction);

                // refresh global balance
                this.refreshBalance();
            },
            onFileImport(importProcess) {
                this.importingFile = true;
                this.totalImports = importProcess.totalRows;
                Echo.private('App.Models.ImportProcess.' + importProcess.id)
                    .listen('ImportProcessComplete', () => {
                        this.importingFile = false;
                        console.log('Import complete');
                        this.fetchData();
                    });
            },
            onDeleteTransaction(day, index) {
                if (confirm('Are you sure?')) {
                    const csrfToken = document.head.querySelector("[name~=csrf-token][content]").content;
                    let transaction = day.transactions[index];
                    fetch("transactions/" + transaction.id, {
                        method: 'delete',
                        headers: {
                            'Content-Type': 'application/json',
                            "X-CSRF-Token": csrfToken
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            // update client side object
                            day.transactions.splice(index, 1);
                            if (day.transactions.length === 0) {
                                this.onDeleteDay(day);
                            }
                            this.statement.totalBalance -= parseFloat(transaction.amount);
                            this.refreshBalance();
                        })
                        .catch(err => console.log(err));
                }
            },
            onUpdateTransaction(day, transactionIndex, transaction) {
                const csrfToken = document.head.querySelector("[name~=csrf-token][content]").content;
                //Update
                fetch("transactions/" + day.transactions[transactionIndex].id, {
                    method: 'put',
                    body: JSON.stringify(transaction),
                    headers: {
                        'content-type': 'application/json',
                        "X-CSRF-Token": csrfToken
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.message) {
                            alert(data.message);
                        } else {
                            // update client side object
                            this.statement.totalBalance -= parseFloat(day.transactions[transactionIndex].amount);
                            day.transactions[transactionIndex] = transaction;
                            this.statement.totalBalance += parseFloat(transaction.amount);
                            if (day.date !== moment(transaction.date).format('YYYY-MM-DD')) {
                                this.onMoveTransaction(day.date, transactionIndex);
                            } else {
                                this.refreshBalance();
                            }
                            alert('Transaction Updated Successfully');
                        }
                        this.refreshBalance();
                    })
            },
            refreshBalance() {
                let statement = this.statement;
                statement.days.forEach(day => {
                    day.amount = 0;
                    day.transactions.forEach(transaction => {
                        day.amount += transaction.amount;
                    })
                })
            },
            makePagination(meta, links) {
                let pagination = {
                    links: links,
                    current_page: meta.current_page,
                    last_page: meta.last_page,
                    next_page_url: meta.next_page_url,
                    prev_page_url: meta.prev_page_url,
                    from: meta.from,
                    to: meta.to,
                    total: meta.total,
                    per_page: meta.per_page,
                }
                this.pagination = pagination;
            },
            fetchData(pageUrl) {
                pageUrl = pageUrl || 'transactions';
                fetch(pageUrl)
                    .then(res => res.json())
                    .then(res => {
                        this.statement.days = []; // reset
                        this.statement.totalBalance = parseFloat(res.totalBalance);
                        this.transactions = res.transactions.data;
                        this.makePagination(res.transactions, res.transactions.links);
                        // load transactions in object, group, and calculate totals
                        this.transactions.forEach(transaction => {
                            this.assignTransactionToDay(transaction);
                        });
                        this.refreshBalance();
                    })
                    .catch(err => console.log(err));
            }
        },
        computed: {
            className() {
                return this.statement.totalBalance >= 0 ? 'text-green-500' : 'text-gray-500';
            },
        },
        created() {
            // fetch the data when the view is created and the data is
            // already being observed
            this.fetchData();
        }
    });
});
