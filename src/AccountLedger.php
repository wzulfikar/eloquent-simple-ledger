<?php 

namespace Wzulfikar\EloquentSimpleLedger;

use Illuminate\Database\Eloquent\Model;

class AccountLedger extends Model{
	
	public $timestamps = false;

	public function account(){
		return $this->belongsTo(Account::class);
	}

	public function scopeOfAccount($query, $account_id)
	{
	  return $query->where('account_id', $account_id);
	}

	public function getTransactionAmountAttribute(){
		return $this->debit ?: -($this->credit);
	}

	public function delete()
	{
	  die("Ledger can't be deleted to keep the integrity of its historical data");
	}

	public function save(array $options = []){
		
		$this->balance = $this->account->balance + $this->transaction_amount;

		$saved = parent::save($options);

		if(!$saved) return $saved;

		// update balance in table accounts
		$this->account->balance = $this->balance;
		$accountUpdated = $this->account->save();

		return $accountUpdated;
	}

}