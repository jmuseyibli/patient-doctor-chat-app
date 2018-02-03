<?php namespace App\Models;

use App\Models\Conversation;
use App\Models\User;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Participant extends Eloquent {

	protected $fillable = ['conversation_id', 'user_id', 'last_read'];

	public function conversation() {
		return $this->belongsTo('App\Models\Conversation');
	}
	
	public function user() {
		return $this->belongsTo('App\Models\User');
	}
	
	public function scopeMe($query, $user) {
		return $query->where('user_id', '=', $user);
	}
	
	public function scopeNotMe($query, $user) {
		return $query->where('user_id', '!=', $user);
	}
}