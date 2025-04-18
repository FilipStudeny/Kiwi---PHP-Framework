<?php
// app/controllers/AuthController.php
namespace app\controllers;

use core\http\HttpMethod;
use core\http\Request;
use core\http\Response;
use core\http\RouterController;

class AuthController extends RouterController {
    public function registerController(): void {
        $this->route('/login', 'login', HttpMethod::POST);
        $this->route('/logout', 'logout', HttpMethod::POST, ['auth']);
        $this->route('/me', 'profile', HttpMethod::GET, ['auth']);
        $this->route('/me/:username', 'me', HttpMethod::GET);

    }

    public function login(Request $req, Response $res) {
        echo "Logging in...";
    }

    public function logout(Request $req, Response $res) {
        echo "Logging out...";
    }

    public function profile(Request $req, Response $res) {
        echo "Current user profile.";
    }

    public function me(Request $req, Response $res) {
        $username = $req->getParameter("username");
        echo "Current user profile......" . $username;
    }
}
