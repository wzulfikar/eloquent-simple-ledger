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
			$stats = LedgerHelper::accountStats($account);

			if(Request::ajax()){
				return $stats;
			}

			view()->addLocation(__DIR__ . '/views');

			return view('eloquent-simple-ledger.index', compact('account', 'stats'));
		});

		Route::post('', function($account){
			$transaction = LedgerHelper::record(Request::all(), $account);

			if(isset($transaction['error']))
				return $transaction;

			return LedgerHelper::accountStats($account);
		});

		Route::get('summary', function($account){
			return LedgerHelper::summary($account);
		});

		Route::get('transactions', function($account){
			return LedgerHelper::transactions($account);
		});		

		Route::get('accountStats', function($account){
			return LedgerHelper::accountStats($account);
		});
});