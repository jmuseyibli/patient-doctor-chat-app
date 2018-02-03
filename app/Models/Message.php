<?php namespace App\Models;

use App\Models\Conversation;
use App\Models\Participant;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Message extends Eloquent {

	protected $touches = ['conversation'];
	protected $fillable = ['conversation_id', 'user_id', 'body'];

	public function conversation() {
		return $this->belongsTo('App\Models\Conversation');
	}

	public function user() {
		return $this->belongsTo('App\Models\User');
	}

	public function participants() {
		return $this->hasMany('App\Models\Participant', 'conversation_id', 'conversation_id');
	}

	public function recipients() {
		return $this->participants()->where('user_id', '!=', $this->user_id)->get();
	}
}