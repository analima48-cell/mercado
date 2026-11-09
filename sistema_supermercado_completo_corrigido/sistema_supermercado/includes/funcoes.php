<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function e($valor): string { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); }
function redirecionar(string $url): never { header('Location: '.$url); exit; }
function flash(string $tipo, string $mensagem): void { $_SESSION['flash'] = ['tipo'=>$tipo,'mensagem'=>$mensagem]; }
function mostrar_flash(): void {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash']; unset($_SESSION['flash']);
        echo '<div class="alert '.e($f['tipo']).'">'.e($f['mensagem']).'</div>';
    }
}
function dinheiro($valor): string { return 'R$ '.number_format((float)$valor, 2, ',', '.'); }
function validar_data(?string $data): bool {
    if (!$data) return true;
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d && $d->format('Y-m-d') === $data;
}
