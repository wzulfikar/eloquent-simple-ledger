<?php

use \Wzulfikar\EloquentSimpleLedger\Account;
use \Wzulfikar\EloquentSimpleLedger\LedgerHelper;

Route::group([
	'as'=>'Ledger::',
	'prefix'=>'ledger/{account}',
	], function($account) use ($router){ // implicit binding for account

		// bind account to \Wzulfikar\EloquentSimpleLedger\Account
		$router->model('account', Account::class);

		Route::get('', function($account){
			if(Request::ajax()){
				return LedgerHelper::accountData($account);
			}

			view()->addLocation(__DIR__ . '/views');

			return view('eloquent-simple-ledger.index', compact('account'));
		});

		Route::post('', function($account){
			$transaction = LedgerHelper::record(Request::all(), $account);

			if(isset($transaction['error']))
				return $transaction;

			return LedgerHelper::accountData($account);
		});

		Route::get('summary', function($account){
			return LedgerHelper::summary($account);
		});
});