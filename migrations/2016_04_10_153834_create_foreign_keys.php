<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateForeignKeys extends Migration {

	public function up()
	{
		Schema::table('account_ledgers', function(Blueprint $table) {
			$table->foreign('account_id')->references('id')->on('accounts')
						->onDelete('no action')
						->onUpdate('no action');
		});
	}

	public function down()
	{
		Schema::table('account_ledgers', function(Blueprint $table) {
			$table->dropForeign('account_ledgers_account_id_foreign');
		});
	}
}