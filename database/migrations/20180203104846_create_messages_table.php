<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessagesTable extends Migration {
    
    public function up() {
        $this->schema->create('messages', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('conversation_id')->unsigned();
            $table->integer('user_id')->unsigned();
			$table->text('body');
			$table->timestamps();
		
        });
        $this->schema->table('messages', function(Blueprint $table) {
        	$table->foreign('conversation_id')->references('id')->on('conversations');
        });
    }
    
    public function down() {
        $this->schema->drop('messages');
    }
    
}