<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateParticipantsTable extends Migration {
    
    public function up() {
        $this->schema->create('participants', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('conversation_id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->timestamp('last_read');
			$table->timestamps();
        });
        
        $this->schema->table('participants', function(Blueprint $table) {
        	$table->foreign('conversation_id')->references('id')->on('conversations');
			$table->foreign('user_id')->references('id')->on('users');
        });
    }
    
    public function down() {
        $this->schema->drop('participants');
    }
    
}