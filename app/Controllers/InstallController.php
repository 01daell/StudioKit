<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\DB;
use App\Core\Session;
use App\Models\User;
use App\Models\Workspace;

class InstallController extends Controller
{
    public function index(): void
    {
        $step = (int) (Session::get('install_step', 1));
        $this->view('install/step' . $step, [
            'requirements' => $this->requirements(),
            'data' => Session::get('install_data', []),
            'errors' => Session::flash('install_errors') ?? [],
        ]);
    }

    public function handle(): void
    {
        $step = (int) (Session::get('install_step', 1));
        $data = Session::get('install_data', []);
        $errors = [];

        if ($step === 1) {
            if ($this->request->input('accept_terms') !== 'on') {
                $errors[] = 'You must accept the terms to continue.';
            }
            if (!$errors) {
                $step = 2;
            }
        } elseif ($step === 2) {
            $requirements = $this->requirements();
            $failed = array_filter($requirements, fn($req) => $req['status'] === false);
            if ($failed) {
                $errors[] = 'Please resolve the failed requirements before continuing.';
            } else {
                $step = 3;
            }
        } elseif ($step === 3) {
            $db = [
                'host' => trim($this->request->input('db_host', 'localhost')),
                'name' => trim($this->request->input('db_name', '')),
                'user' => trim($this->request->input('db_user', '')),
                'pass' => (string) $this->request->input('db_pass', ''),
                'port' => trim($this->request->input('db_port', '3306')),
            ];
            $test = DB::testConnection($db);
            if (!$test['ok']) {
                $errors[] = 'Database connection failed: ' . $test['error'];
            } else {
                $data['database'] = $db;
                $step = 4;
            }
        } elseif ($step === 4) {
            $data['app'] = [
                'url' => trim($this->request->input('app_url', $this->detectUrl())),
                'name' => trim($this->request->input('app_name', 'StudioKit')),
            ];
            $data['admin'] = [
                'name' => trim($this->request->input('admin_name', '')),
                'email' => trim($this->request->input('admin_email', '')),
                'password' => (string) $this->request->input('admin_password', ''),
            ];
            $data['workspace'] = [
                'name' => trim($this->request->input('workspace_name', 'StudioKit Workspace')),
            ];
            if (!$data['admin']['email'] || !$data['admin']['password']) {
                $errors[] = 'Admin email and password are required.';
            } else {
                $step = 5;
            }
        } elseif ($step === 5) {
            $data['smtp'] = [
                'host' => trim($this->request->input('smtp_host', '')),
                'port' => trim($this->request->input('smtp_port', '587')),
                'user' => trim($this->request->input('smtp_user', '')),
                'pass' => (string) $this->request->input('smtp_pass', ''),
                'encryption' => trim($this->request->input('smtp_encryption', 'tls')),
                'from_name' => trim($this->request->input('smtp_from_name', 'StudioKit')),
                'from_email' => trim($this->request->input('smtp_from_email', '')),
            ];
            $step = 6;
        } elseif ($step === 6) {
            $data['stripe'] = [
                'publishable_key' => trim($this->request->input('stripe_publishable_key', '')),
                'secret_key' => trim($this->request->input('stripe_secret_key', '')),
                'webhook_secret' => trim($this->request->input('stripe_webhook_secret', '')),
                'success_url' => trim($this->request->input('stripe_success_url', $this->detectUrl() . '/app/billing')),
                'cancel_url' => trim($this->request->input('stripe_cancel_url', $this->detectUrl() . '/app/billing')),
                'price_ids' => [
                    'starter' => trim($this->request->input('stripe_price_starter', '')),
                    'pro' => trim($this->request->input('stripe_price_pro', '')),
                    'agency' => trim($this->request->input('stripe_price_agency', '')),
                ],
            ];
            $step = 7;
        } elseif ($step === 7) {
            $data = Session::get('install_data', []);
            $config = $this->buildConfig($data);
            $this->writeConfig($config);
            $this->createSchema($data['database']);
            $userId = User::create([
                'name' => $data['admin']['name'] ?: 'Admin',
                'email' => $data['admin']['email'],
                'password_hash' => password_hash($data['admin']['password'], PASSWORD_BCRYPT),
            ]);
            $workspaceId = Workspace::create([
                'name' => $data['workspace']['name'],
                'created_by' => $userId,
            ]);
            Workspace::addMember($workspaceId, $userId, 'OWNER');
            $lockPath = __DIR__ . '/../../storage/installed.lock';
            file_put_contents($lockPath, 'installed ' . date('c'));
            Session::forget('install_step');
            Session::forget('install_data');
            $this->redirect('/install/complete');
            return;
        }

        if ($errors) {
            Session::flash('install_errors', $errors);
        } else {
            Session::set('install_step', $step);
            Session::set('install_data', $data);
        }
        $this->redirect('/install');
    }

    public function complete(): void
    {
        $this->view('install/complete');
    }

    private function requirements(): array
    {
        $extensions = ['pdo_mysql', 'openssl', 'mbstring', 'gd', 'zip'];
        $reqs = [
            ['label' => 'PHP >= 8.1', 'status' => version_compare(PHP_VERSION, '8.1.0', '>=')],
        ];
        foreach ($extensions as $ext) {
            $reqs[] = ['label' => 'Extension: ' . $ext, 'status' => extension_loaded($ext)];
        }
        $paths = [
            __DIR__ . '/../../storage',
            __DIR__ . '/../../storage/uploads',
            __DIR__ . '/../../storage/cache',
            __DIR__ . '/../../storage/logs',
            __DIR__ . '/../../config',
        ];
        foreach ($paths as $path) {
            $reqs[] = ['label' => 'Writable: ' . str_replace(__DIR__ . '/../..', '', $path), 'status' => is_writable($path)];
        }
        return $reqs;
    }

    private function detectUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $path = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
        return $scheme . '://' . $host . $path;
    }

    private function writeConfig(array $config): void
    {
        $content = "<?php\nreturn " . var_export($config, true) . ";\n";
        $path = __DIR__ . '/../../config/config.php';
        file_put_contents($path, $content);
    }

    private function createSchema(array $db): void
    {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $db['host'], $db['port'], $db['name']);
        $pdo = new \PDO($dsn, $db['user'], $db['pass'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);
        $schema = file_get_contents(__DIR__ . '/../../config/schema.sql');
        $pdo->exec($schema);
    }

    private function buildConfig(array $data): array
    {
        return [
            'app' => [
                'name' => $data['app']['name'],
                'url' => $data['app']['url'],
                'key' => bin2hex(random_bytes(32)),
            ],
            'database' => $data['database'],
            'smtp' => $data['smtp'],
            'stripe' => $data['stripe'],
            'storage' => [
                'path' => __DIR__ . '/../../storage',
            ],
        ];
    }
}
