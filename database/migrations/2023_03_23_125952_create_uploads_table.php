<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUploadsTable.
 */
class CreateUploadsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('uploads', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('uploadable_id');
			$table->string('uploadable_type');
			$table->string('name')->nullable();
			$table->tinyInteger('is_image')->default(0);
			$table->string('path')->nullable();
			$table->string('base_url')->nullable();
			$table->string('size')->nullable();
			$table->softDeletes();
            $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('uploads');
	}
}
