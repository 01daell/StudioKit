<?php
namespace App\Core;

use App\Core\Auth;

class Controller
{
    protected Request $request;
    protected Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $contentView = __DIR__ . '/../Views/' . $view . '.php';
        require __DIR__ . '/../Views/layouts/app.php';
    }

    protected function redirect(string $path): void
    {
        $this->response->redirect($path);
    }

    protected function requireAuth(): array
    {
        $user = Auth::user();
        if (!$user) {
            $this->redirect('/sign-in');
        }
        return $user;
    }
}
