<?php

use Illuminate\Database\Migrations\Migration;

class RawUriDefinition extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create two extra columns for the original collection URI and original resource name
		Schema::table('definitions', function($table){
			$table->string('original_collection_uri', 255);
			$table->string('original_resource_name', 255);
		});

		// By default make them the same as the collection uri and resource name
		\DB::statement('UPDATE definitions SET original_resource_name=resource_name');
		\DB::statement('UPDATE definitions SET original_collection_uri=collection_uri');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Drop the new columns
		Schema::table('definitions', function($table){
			$table->dropColumn('original_collection_uri', 'original_resource_name');
		});
	}

}