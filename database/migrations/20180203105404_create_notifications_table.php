<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationsTable extends Migration {
    
   public function up() {
        $this->schema->create('notifications', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('sender');
            $table->string('message');
            $table->smallInteger('read'); 
            $table->timestamps();
        });
        
        $this->schema->table('notifications', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
        });
    }
    
    public function down() {
        $this->schema->drop('notifications');
    }
    
}