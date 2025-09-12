<?php
declare(strict_types=1);

use App\Controllers\QuestionController;
use App\Controllers\AdminController;
use Pecee\SimpleRouter\SimpleRouter as Router;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
if (is_file(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

// Load Railway environment variables if available
if (getenv('RAILWAY_ENVIRONMENT')) {
    // Railway automatically provides these environment variables
    // No additional loading needed
}

// Headers de segurança
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
// CSP temporariamente desabilitado para debug
// header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'; style-src \'self\' \'unsafe-inline\'; img-src \'self\' data:; font-src \'self\';');

// Rota para setup do banco
Router::get('/setup-database', function() {
    include __DIR__ . '/../setup-database.php';
    exit;
});

// Rotas públicas
Router::get('/', [QuestionController::class, 'root']);
Router::get('/q/{n}', [QuestionController::class, 'show']);
Router::post('/q/{n}', [QuestionController::class, 'submit']);
Router::get('/end', [QuestionController::class, 'end']);
Router::get('/restart', [QuestionController::class, 'restart']);

// Rotas administrativas (API)
Router::post('/admin/login', [AdminController::class, 'login']);
Router::post('/admin/logout', [AdminController::class, 'logout']);
Router::get('/admin/questions', [AdminController::class, 'getQuestions']);
Router::post('/admin/questions', [AdminController::class, 'createQuestion']);
Router::post('/admin/questions/update', [AdminController::class, 'updateQuestion']);
Router::post('/admin/questions/delete', [AdminController::class, 'deleteQuestion']);
Router::post('/admin/questions/toggle', [AdminController::class, 'toggleQuestion']);
Router::get('/admin/csrf-token', [AdminController::class, 'getCsrfToken']);

try {
    Router::start();
} catch (Exception $e) {
    http_response_code(500);
    echo "Erro interno: " . $e->getMessage();
}
