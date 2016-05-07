<?php

namespace Wzulfikar\EloquentSimpleLedger;

use Illuminate\Database\Eloquent\Model;

class Account extends Model{

	public function ledgers(){
		return $this->hasMany(AccountLedger::class);
	}

	public function debit($amount, $desc, $created_by = null){
		$ledger        = $this->createLedgerRecord();
		$ledger->debit = $amount;
		$ledger->desc  = $desc;
		
		if($created_by){
			$ledger->created_by = $created_by;
		}
		
		return $ledger->save();
	}

	public function credit($amount, $desc, $created_by = null){
		$ledger         = $this->createLedgerRecord();
		$ledger->credit = $amount;
		$ledger->desc   = $desc;
		
		if($created_by){
			$ledger->created_by = $created_by;
		}

		return $ledger->save();
	}
	
	public function getPrevBalanceAttribute()
	{
	  if($ledger = $this->ledger()->latest()->take(2)->get())
	  	return $ledger[1]->balance;

	  return null;
	}

	private function createLedgerRecord(){
		$ledger = new AccountLedger;
		$ledger->account_id = $this->id;

		return $ledger;
	}
}