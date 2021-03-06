# Finance App Trial Project

This project proposal has been put together to help developers who are applying for new positions but don't have any sample code to provide during the hiring process.

We've put together a basic Laravel starter app, which includes authentication scaffolding. To get started, run the following commands:

```
composer install
php artisan migrate
npm install
npm run dev
```

After signing up, you'll be redirected to the user's dashboard. This is a static HTML representation of the [dashboard mockup](#dashboard) built using Tailwind CSS.

## Requirements

The trial project should be built using Laravel and Vue.js and should demonstrate your abilities with these frameworks.

### Dashboard

A list of the user's balance entries should be shown by default. Entries should be grouped by date. Although pagination is missing from the mockups, feel free to add basic pagination if you get time.

![](mockups/yourbalance-1-default@2x.png)

### Add Entry

A user should be able to add single balance entries. Adding a new entry should update the balance list and the total balance.

![](mockups/yourbalance-2-add-item-modal@2x.png)

### Edit & Delete Entry

Hovering over an entry should show the edit and delete links.

![](mockups/yourbalance-3-rollover-actions@2x.png)

Clicking 'Delete' should remove the entry from the list and update the total balance. Clicking 'Edit' should reveal the edit form.

![](mockups/yourbalance-4-edit-item@2x.png)

Clicking 'Update Entry' should update the balance list and update the total balance.

### Import Entries

A [CSV file](data/5000-balance-entries.csv) of entries can be imported. The import should happen in the background. The 'Add Entry' and 'Import CSV' buttons should be disabled while the import is working, however, existing entries can be edited or deleted.

![](mockups/yourbalance-6-import-csv-file-selected@2x.png)

Imported entries should not appear in the balance list, until the entire import has completed. When the import is running, a notice should be shown with the count of entries currently being imported.

![](mockups/yourbalance-7-csv-uploading@2x.png)

## Delivery

1. Please track your time to give us an idea of how long it took you to complete the project.
1. It's not required, but you get massive bonus points if you record a screencast with commentary, showing us how you're thinking through the problems you face and how you tackle the project from start to finish.
1. Fork this repo
1. If the repo is private, grant @A5hleyRich and @bradt access
1. Create a new branch and make all your commits to that branch
1. When it's ready for review, push the branch to GitHub
1. Open a pull request to merge your branch into the `master` branch and mention @A5hleyRich in the PR comment
1. Reply to our email to let us know you've mentioned Ash in the PR
