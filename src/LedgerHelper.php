<?php

namespace Wzulfikar\EloquentSimpleLedger;

use Illuminate\Support\Facades\DB;

class LedgerHelper {

	public static function record(array $data, Account $account)
	{
		$amount = $data['amount'] * 100; // convert user's amount to cent
		$desc   = $data['desc'];
		$action = $data['action']; // debit or credit

		if(!$amount || !$desc)
			return ['error'=>true, 'msg'=>"Both amount & description can't be empty."];

		return $account->$action($amount, $desc);
	}

	public static function accountStats(Account $account){
		$static = (new static);
		return ['ledger' => $account->ledgers()->get(), 'summary' => $static->summary($account), 'transactions'=>$static->transactions($account)];
	}

	public static function transactions(Account $account)
	{
		$transactions = $account->ledgers()->select(
			DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") date, coalesce(sum(debit)/100, 0) debit, coalesce(sum(credit)/100, 0) credit, coalesce(sum(debit)/100, 0) - coalesce(sum(credit)/100, 0) balance')
		)		
		->groupBy('date')
		->get()
		->toArray();
		
		// adjust balance
		foreach ($transactions as $key => $transaction) {
			if($key == 0)
				continue;
			$transactions[$key]['balance'] += $transactions[$key-1]['balance'];
		}

	  return $transactions;
	}

	public static function summary(Account $account){
		// create 3 months range : this month and 2 months before
		$months = [];
		foreach (range(0, 2) as $val) {
			$months[] = date('Y-m', strtotime('-' . $val . ' months'));
		}

		sort($months);
		$range  = [ $months[ 0 ] . date('-01 00:00:00'), $months[ count($months) - 1 ] . date('-d H:i:s')];
		
		$ledger = $account->ledgers()
											->whereBetween('created_at', $range)
											->get(['created_at', 'debit', 'credit', 'balance'])
											->toArray();

    if(!count($ledger))
    	return [];

		// $months = array_flip($months);
		foreach ($months as $monthsKey => $month) {
			
			$month_data = ['debit'=>0, 'credit'=>0, 'balance'=>0, 'month'=>date('M Y', strtotime($month . '-01'))];

			foreach ($ledger as $ledgerKey => $transaction) {
				if(!starts_with($transaction['created_at'], $month))
					continue;
 
				$month_data['debit']  += $transaction['debit']/100; 
				$month_data['credit'] += $transaction['credit']/100;
				$month_data['balance'] = $transaction['balance']/100;
			}

			$months[$monthsKey] = $month_data;
		}

		return $months;
	}
}