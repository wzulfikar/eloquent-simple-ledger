<?php

Route::get('ledger/{account_id}', function($account_id){

	$account = Wzulfikar\EloquentSimpleLedger\Account::findOrFail($account_id);

	if(\Request::ajax()){
		return $account->ledger()->get();
	}

	view()->addLocation(__DIR__ . '/views');

	$last_record = $account->ledger()->latest()->first();
	return view('eloquent-simple-ledger.index', compact('account_id', 'last_record'));
});