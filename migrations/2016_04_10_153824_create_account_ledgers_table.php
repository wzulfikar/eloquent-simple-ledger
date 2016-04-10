<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountLedgersTable extends Migration {

	public function up()
	{
		Schema::create('account_ledgers', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('account_id')->unsigned();
			$table->integer('debit')->nullable();
			$table->integer('credit')->nullable();
			$table->text('desc');
			$table->integer('balance');
			$table->timestamp('created_at')->default(DB::raw('NOW()'));
		});
	}

	public function down()
	{
		Schema::drop('account_ledgers');
	}
}