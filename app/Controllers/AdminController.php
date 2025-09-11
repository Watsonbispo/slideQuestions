<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\QuestionService;

final class AdminController
{
    private AuthService $auth;
    private QuestionService $questions;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->questions = new QuestionService();
    }

    public function login(): void
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $username = htmlspecialchars(trim($input['username'] ?? ''), ENT_QUOTES, 'UTF-8');
        $password = $input['password'] ?? '';
        
        if ($this->auth->login($username, $password)) {
            echo json_encode(['success' => true, 'message' => 'Login realizado com sucesso']);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Usuário ou senha inválidos']);
        }
    }

    public function getQuestions(): void
    {
        header('Content-Type: application/json');
        
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Não autorizado']);
            return;
        }

        $questions = $this->questions->getAllQuestions();
        echo json_encode(['success' => true, 'questions' => $questions]);
    }

    public function updateQuestion(): void
    {
        header('Content-Type: application/json');
        
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Não autorizado']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);
        $title = trim($input['title'] ?? '');
        $options = $input['options'] ?? [];
        
        if (empty($title) || $id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            return;
        }

        $result = $this->questions->updateQuestion($id, $title, $options);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Pergunta atualizada com sucesso']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar pergunta']);
        }
    }

    public function createQuestion(): void
    {
        header('Content-Type: application/json');
        
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Não autorizado']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $title = htmlspecialchars(trim($input['title'] ?? ''), ENT_QUOTES, 'UTF-8');
        $options = $input['options'] ?? [];
        
        if (empty($title) || strlen($title) < 10) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Título deve ter pelo menos 10 caracteres']);
            return;
        }

        $result = $this->questions->createQuestion($title, $options);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Pergunta criada com sucesso']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao criar pergunta']);
        }
    }

    public function deleteQuestion(): void
    {
        header('Content-Type: application/json');
        
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Não autorizado']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);
        
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            return;
        }

        $result = $this->questions->deleteQuestion($id);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Pergunta excluída com sucesso']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao excluir pergunta']);
        }
    }

    public function toggleQuestion(): void
    {
        header('Content-Type: application/json');
        
        if (!$this->auth->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Não autorizado']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);
        
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            return;
        }

        $result = $this->questions->toggleQuestionStatus($id);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Status da pergunta alterado']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao alterar status']);
        }
    }

    public function logout(): void
    {
        header('Content-Type: application/json');
        $this->auth->logout();
        echo json_encode(['success' => true, 'message' => 'Logout realizado com sucesso']);
    }

    public function getCsrfToken(): void
    {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'csrf_token' => 'disabled']);
    }
}
