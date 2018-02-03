<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConversationsTable extends Migration {
    
    public function up() {
        $this->schema->create('conversations', function(Blueprint $table) {
            $table->increments('id');
            $table->string('subject');
            $table->timestamps();
        });
    }
    
    public function down() {
        $this->schema->drop('conversations');
    }
    
}