# Simple Ledger Mechanism via Laravel Eloquent

### Installation
`composer require wzulfikar\eloquent-simple-ledger`

### Usage 
	
	// find account from table `accounts`	
	$account = \Wzulfikar\EloquentSimpleLedger\Account::findOrFail($your_account_id);
	
	// assuming that current balance is 0
	// and now we want to debit 500
	// this will create new debit record in table `account_ledgers`
	$account->debit(500, 'initial deposit');
	
	// the balance now is 500
	// which is previous balance (0) + current transaction (500)
	echo $account->balance;
	
	// let's withdraw some money.
	// this will create new credit record in table `account_ledgers`
	$account->credit(50, 'withdrawal');
	
	// the balance now is 450
	// which is previous balance (500) + current transaction (-50)
	echo $account->balance;

### Get account balance

	$account = \Wzulfikar\EloquentSimpleLedger\Account::findOrFail($your_account_id);
	$account->balance;

### Get Previous Balance

	$account->prev_balance;

This method used to calculate the balance after each transaction.

### Record New Debit

	$account->debit($amount, $description);

### Record New Credit

	$account->credit($amount, $description);

### Get Ledger Records

	$account->ledger->all();

### Behind The Scene

When a transaction happens, eloquent will create new row in `account_ledgers` table. If the transaction is debit, the `debit` column won't be null but the `credit` column will be, and vice versa. Then, it will get value of `balance` column from previous row (where the `account_id` is same as the `account_id` of new row) and add amount of transaction to get the balance of current transaction.

After the transaction finished, eloquent will cache the last balance of `account_ledgers` table in `accounts` table.

### Table Definitions

Table `accounts` :

- (int) id  
  id of account
- (int) balance  
  current balance of account

Table `account_ledgers` :

- (int) id
- (int) account_id
- (int) debit  
  amount of debit, nullable.
- (int) credit  
  amount of credit, nullable.
- (text) desc  
	description of transaction, nullable.
- (int) balance   
	sum of previous balance and current transaction. this column ensures we have balance of a transaction of any time.

Table `account_ledgers` contains transactions from all accounts in table `accounts`. Both tables have `balance` column, but with different usage.

`balance` in `account_ledgers` records new balance after every transaction. 

*Example Debit* :  
if balance of last row is 50 and current row's debit is 10, then the balance of current row will be 50 + 10 = 60.

*Example Credit* :  
if balance of last row is 60 and current row's credit is 20, then the balance of current row will be 60 + -(20) = 40.

Since balance of every transaction is recorded in `account_ledgers`, generating report (eg. bank-statement) will be easier.

The `balance` column in table `accounts` used to get balance of particular account, which is actually value of `balance` of last row in table `account_ledgers` of that particular account. This is to avoid querying whole rows in `account_ledgers` just to get balance of an account.

All rows in table `account_ledgers`	 are meant to be read-only data. A row can only be created, update or modification should not be processed.

>debit, credit & balance are all integers by default.

### Integrating with User Model
Say, you want to get balance of currently logged in user, which might be something like this:

	auth()->user()->account->balance;

you need to connect your user model with account model.

*First, make sure the column `account_id` is available in table of your user model and it contains id of account that belongs to the user.*

*Secondly, add eloquent relationship in your user model:*

	
	// user model
	public function account(){
		return $this->hasOne(Wzulfikar\EloquentSimpleLedger\Account::class);	
	}


### Migration
Copy files in package's migrations folder into your laravel's migrations folder and run `php artisan migrate`. 

If you don't want to copy thus files into your app's migration folder, pass the path to package's migration files in artisan migrate command. Like this:
`php artisan migrate --path=vendor/wzulfikar/eloquent-simple-ledger/migrations`

### How it Looks
To see how it looks, 

- include package's `routes.php` into your app's `routes.php` :  
	`require_once base_path('path/to/wzulfikar/eloquent-simple-ledger/routes.php');`
- create dummy data for `account` and `account_ledgers` and then visit `/ledger/{account_id}`.

### Sample View : No Data
![image](view-without-data.png)

### Sample View : With Data
![image](view-with-data.png)

The sample view included some features:

 - export to excel, csv & pdf
 - reloading data using ajax
 - indicator for latest transaction
 - human friendly time using moment.js
 - responvie table, sortable columns & searchable -- yes, it uses datatables :)
 - debts indicator : balance will be red if it less than 0

### Additional Panels

- Transaction History

![image](panel-transaction-history.png)