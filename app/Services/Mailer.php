<?php
namespace App\Services;

use App\Core\Config;

class Mailer
{
    public function send(string $to, string $subject, string $html, string $text = ''): bool
    {
        $smtp = Config::get('smtp', []);
        $host = $smtp['host'] ?? '';
        $port = (int) ($smtp['port'] ?? 587);
        $user = $smtp['user'] ?? '';
        $pass = $smtp['pass'] ?? '';
        $encryption = $smtp['encryption'] ?? 'tls';
        $fromName = $smtp['from_name'] ?? 'StudioKit';
        $fromEmail = $smtp['from_email'] ?? $user;

        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $fromName . ' <' . $fromEmail . '>',
        ];

        if (!$host) {
            return mail($to, $subject, $html, implode("\r\n", $headers));
        }

        $socket = fsockopen(($encryption === 'ssl' ? 'ssl://' : '') . $host, $port, $errno, $errstr, 10);
        if (!$socket) {
            return false;
        }
        $this->read($socket);
        $this->write($socket, 'EHLO ' . ($smtp['helo'] ?? 'localhost'));
        $this->read($socket);

        if ($encryption === 'tls') {
            $this->write($socket, 'STARTTLS');
            $this->read($socket);
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $this->write($socket, 'EHLO ' . ($smtp['helo'] ?? 'localhost'));
            $this->read($socket);
        }

        if ($user && $pass) {
            $this->write($socket, 'AUTH LOGIN');
            $this->read($socket);
            $this->write($socket, base64_encode($user));
            $this->read($socket);
            $this->write($socket, base64_encode($pass));
            $this->read($socket);
        }

        $this->write($socket, 'MAIL FROM: <' . $fromEmail . '>');
        $this->read($socket);
        $this->write($socket, 'RCPT TO: <' . $to . '>');
        $this->read($socket);
        $this->write($socket, 'DATA');
        $this->read($socket);

        $message = 'Subject: ' . $subject . "\r\n";
        $message .= implode("\r\n", $headers) . "\r\n\r\n";
        $message .= $html . "\r\n.\r\n";
        $this->write($socket, $message, false);
        $this->read($socket);
        $this->write($socket, 'QUIT');
        fclose($socket);
        return true;
    }

    private function write($socket, string $command, bool $appendNewline = true): void
    {
        fwrite($socket, $command . ($appendNewline ? "\r\n" : ''));
    }

    private function read($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $response;
    }
}
