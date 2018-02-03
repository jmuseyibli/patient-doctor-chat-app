<?php namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\User;
use Respect\Validation\Validator as vld;

class AuthController extends Controller {
    
    public function logout(Request $request, Response $response, $args) {
        $this->ci->auth->logout();
        return $response->withRedirect($this->ci->router->pathFor('auth.login'));
    }
    
    public function login(Request $request, Response $response, $args) {
        return $this->ci->view->render($response, 'auth/login.twig');
    }
    
    public function postLogin(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $auth = $this->ci->auth->attempt(
            $data['identification'],
            $data['password']
        );
        if (!$auth) {
            $this->ci->flash->addMessage('error', 'Could not sign you in with these creditentials.');
            return $response->withRedirect($this->ci->router->pathFor('auth.login'));
        }
        $this->ci->flash->addMessage('info', 'You have logged in.');
        return $response->withRedirect($this->ci->router->pathFor('home'));

    }
    
    public function signup(Request $request, Response $response, $args) {
        return $this->ci->view->render($response, 'auth/signup.twig');
    }
    
    public function postSignup(Request $request, Response $response, $args) {
        $validation = $this->ci->validator->validate($request, [
            'username' => vld::noWhitespace()->notEmpty(),
            'fullname' => vld::notEmpty()->alpha(),
            'email'=> vld::noWhitespace()->notEmpty()->email()->emailAvailable(),
            'password' => vld::noWhitespace()->notEmpty(),
        ]);
        
        if ($validation->failed()) {
            return $response->withRedirect($this->ci->router->pathFor('auth.signup'));
        }
        
        $data = $request->getParsedBody();
        $user = new User();
        $user->username = $data['username'];
        $user->fullname = $data['fullname'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->save();
        $this->ci->flash->addMessage('info', 'You have signed up.');
        return $response->withRedirect($this->ci->router->pathFor('home'));
    }
    
}