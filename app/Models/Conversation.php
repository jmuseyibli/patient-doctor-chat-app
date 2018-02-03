<?php

namespace App\Models;

use App\Models\User;
use App\Models\Message;
use App\Models\Participant;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Conversation extends Eloquent {

    protected $fillable = ['subject'];
    
    // is necessary?
    public function isParticipant($userId) {
    	foreach($this->participants as $participant) {
			if ($participant['user_id'] == $userId) return true;
    	}
    	return false;
    }
    
    public function getLastMessage() {
    	return $this->messages()->orderBy('created_at', 'desc')->first();
    }
    
    public function getTitle() {
    	$users = explode('-', $this->subject);
    	if (isset($_SESSION['user'])) {
	    	$user = User::find($_SESSION['user']);
	    	$index = array_search($user->id, $users);
	    	unset($users[$index]);
	    	$target = implode("-", $users);
	    	$title = User::find($target)->fullname;
    	    return $title;
    	}
    	return "401 Unauthorized";
    	
    }

    public function messages() {
		return $this->hasMany('App\Models\Message');
	}
	
	public function participants() {
		return $this->hasMany('App\Models\Participant');
	}
	
	public function scopeForUser($query, $user_id) {
		return $query->join('participants', 'conversations.id', '=', 'participants.conversation_id')
		->where('participants.user_id', $user_id)
		->select('conversations.*');
	}
	
// 	public function scopeWithNewMessages($query, $user) {
// 		return $query->join('participants', 'conversations.id', '=', 'participants.conversation_id')
// 		->where('participants.user_id', $user)
// 		->where('conversations.updated_at', '>', DB::raw('participants.last_read'))
// 		->select('conversations.*');
// 	}
	
// 	public function participantsString($user) {
// 		$participantNames = DB::table('users')
// 		->join('participants', 'users.id', '=', 'participants.user_id')
// 		->where('users.id', '!=', $user)
// 		->where('participants.conversation_id', $this->id)
// 		->select(DB::raw("concat(users.first_name, ' ', users.last_name) as name"))
// 		->lists('users.name');
// 		return implode(', ', $participantNames);
// 	}

	public function addParticipants(array $participants) {
		$participant_ids = [];
		if (is_array($participants)) {
			if (is_numeric($participants[0])) {
				$participant_ids = $participants;
			} else {
				$participant_ids = User::whereIn('email', $participants)->lists('id');
			}
		} else {
			if (is_numeric($participants)) {
				$participant_ids = [$participants];
			} else {
				$participant_ids = User::where('email', $participants)->lists('id');
			}
		} 
		if(count($participant_ids)) {
			foreach ($participant_ids as $user_id) {
				Participant::create([
					'user_id' => $user_id,
					'conversation_id' => $this->id,
				]);
			}
		}
	}
}