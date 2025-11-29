<?php
namespace App\Infra\Http;

class Request
{
    public function method(): string { return $_SERVER['REQUEST_METHOD'] ?? 'GET'; }
    public function query(string $key, $default = null) { return $_GET[$key] ?? $default; }
    public function input(string $key, $default = null) { return $_POST[$key] ?? $default; }
}
