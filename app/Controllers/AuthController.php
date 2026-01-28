<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\RateLimiter;
use App\Models\User;
use App\Models\Workspace;
use App\Models\PasswordReset;
use App\Services\Mailer;

class AuthController extends Controller
{
    public function signup(): void
    {
        $this->view('auth/sign-up');
    }

    public function signupPost(): void
    {
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/sign-up');
        }
        if (!RateLimiter::hit('signup', 5, 300)) {
            Session::flash('error', 'Too many attempts.');
            $this->redirect('/sign-up');
        }
        $name = trim($this->request->input('name', ''));
        $email = trim($this->request->input('email', ''));
        $password = (string) $this->request->input('password', '');

        if (!$email || !$password) {
            Session::flash('error', 'Name, email, and password are required.');
            $this->redirect('/sign-up');
        }
        if (User::findByEmail($email)) {
            Session::flash('error', 'Email already registered.');
            $this->redirect('/sign-up');
        }
        $userId = User::create([
            'name' => $name ?: 'User',
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
        ]);
        $workspaceId = Workspace::create([
            'name' => $name ? $name . "'s Workspace" : 'Workspace',
            'created_by' => $userId,
        ]);
        Workspace::addMember($workspaceId, $userId, 'OWNER');
        Session::set('user_id', $userId);
        Session::set('workspace_id', $workspaceId);
        $this->redirect('/app');
    }

    public function signin(): void
    {
        $this->view('auth/sign-in');
    }

    public function signinPost(): void
    {
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/sign-in');
        }
        if (!RateLimiter::hit('signin', 10, 300)) {
            Session::flash('error', 'Too many attempts.');
            $this->redirect('/sign-in');
        }
        $email = trim($this->request->input('email', ''));
        $password = (string) $this->request->input('password', '');
        if (!\App\Core\Auth::attempt($email, $password)) {
            Session::flash('error', 'Invalid credentials.');
            $this->redirect('/sign-in');
        }
        $workspaces = Workspace::forUser((int) Session::get('user_id'));
        if (!Session::get('workspace_id') && $workspaces) {
            Session::set('workspace_id', $workspaces[0]['id']);
        }
        $this->redirect('/app');
    }

    public function signout(): void
    {
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/app');
        }
        \App\Core\Auth::logout();
        $this->redirect('/');
    }

    public function forgot(): void
    {
        $this->view('auth/forgot');
    }

    public function forgotPost(): void
    {
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/forgot-password');
        }
        if (!RateLimiter::hit('forgot', 5, 300)) {
            Session::flash('error', 'Too many attempts.');
            $this->redirect('/forgot-password');
        }
        $email = trim($this->request->input('email', ''));
        $user = User::findByEmail($email);
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600);
            PasswordReset::create($email, $token, $expires);
            $resetLink = url('/reset-password?token=' . $token);
            $mailer = new Mailer();
            $mailer->send($email, 'Reset your StudioKit password', 'Reset your password: <a href="' . $resetLink . '">' . $resetLink . '</a>');
        }
        Session::flash('message', 'If that email exists, a reset link was sent.');
        $this->redirect('/forgot-password');
    }

    public function reset(): void
    {
        $token = $this->request->query['token'] ?? '';
        $this->view('auth/reset', ['token' => $token]);
    }

    public function resetPost(): void
    {
        if (!Session::verifyCsrf($this->request->input('_csrf'))) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/reset-password?token=' . h($this->request->input('token', '')));
        }
        $token = (string) $this->request->input('token', '');
        $password = (string) $this->request->input('password', '');
        $reset = PasswordReset::findValid($token);
        if (!$reset) {
            Session::flash('error', 'Reset token expired or invalid.');
            $this->redirect('/forgot-password');
        }
        $user = User::findByEmail($reset['email']);
        if ($user) {
            $stmt = \App\Core\DB::pdo()->prepare('UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([password_hash($password, PASSWORD_BCRYPT), $user['id']]);
        }
        PasswordReset::deleteByToken($token);
        Session::flash('message', 'Password updated. Sign in.');
        $this->redirect('/sign-in');
    }
}
