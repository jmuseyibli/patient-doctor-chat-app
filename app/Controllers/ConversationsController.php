<?php namespace App\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Participant;
use App\Models\Conversation;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class ConversationsController extends Controller {
    
    public function generateConversationLinks(Request $request, Response $response, $args)  {
        $data = $request->getParsedBody();
        $users = User::where('fullname', 'LIKE', '%'. $data['query'] . '%')
        ->orWhere('username', 'LIKE', '%'. $data['query'] . '%')
        ->orWhere('email', 'LIKE', $data['query'] . '%')
        ->get();
        return $this->ci->view->render($response, 'ajax/conversation_links_generation.twig', [
            'users' => $users,
        ]);

    }
    
    public function createConversation(Request $request, Response $response, $args) {
        $sender = User::find($this->ci->auth->user()->id);
        $recipient = User::find($request->getParam('recipient'));
        if ($conversation = Conversation::where('subject', "{$sender->id}-{$recipient->id}")->orWhere('subject', "{$recipient->id}-{$sender->id}")->first()) {
            return $this->ci->view->render($response, 'ajax/create_conversation.twig', [
                'conversation' => $conversation,
            ]);
        } else {
            $conversation = Conversation::create([
                'subject' => "{$sender->id}-{$recipient->id}",
            ]);
            
            Participant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $sender->id,
            ]);
            Participant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $recipient->id,
            ]);
            return $this->ci->view->render($response, 'ajax/create_conversation.twig', [
                'conversation' => $conversation,
            ]);
        }
    }
    
    public function getMyConversations(Request $request, Response $response, $args)  {
        $user = User::find($this->ci->auth->user()->id);
        $conversations = Conversation::forUser($user->id)->orderBy('updated_at', 'desc')->get();
        return $this->ci->view->render($response, 'conversations/chat.twig', [
            'conversations' => $conversations,
        ]);
    }
    
    public function showConversation(Request $request, Response $response, $args) {
        if ($conversation = Conversation::find($request->getParam('id'))) {
            if ($conversation->isParticipant($this->ci->auth->user()->id)) {
                // $user = User::find($this->ci->auth->user()->id);
                // $conversations = Conversation::forUser($user->id)->orderBy('updated_at', 'desc')->get();
                $messages = $conversation->messages()->orderBy('updated_at')->get();
                return $this->ci->view->render($response, 'ajax/show.twig', [
                    'messages' => $messages,
                ]);
            } else {
                return "You do not have access";
            }
        } else {
            return "Conversation not found";
        }
    }
    
    public function sendMessage(Request $request, Response $response, $args)  {
        $conversation = Conversation::find($request->getParam('conversation_id'));
        $message = new Message();
        $message->body = $request->getParam('message');
        $message->user()->associate($this->ci->auth->user());
        $message->conversation()->associate($conversation);
        $message->save();
        return $response->withRedirect($this->ci->router->pathFor('conversations.show', [
            'id' => $conversation->id,
        ]));
    }
    
}