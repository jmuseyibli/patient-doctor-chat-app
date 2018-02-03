<?php

use App\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {
    
    public function up() {
        $this->schema->create('users', function(Blueprint $table) {
            $table->increments('id');
            $table->string('username');
            $table->string('fullname');
            $table->string('email');
            $table->string('password');
            $table->smallInteger('role');
            $table->timestamps();
        });
    }
    
    public function down() {
        $this->schema->drop('users');
    }
    
}