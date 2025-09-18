<?php
declare(strict_types=1);

namespace App\Services;

use App\Support\Database;
use PDO;

final class QuestionService
{
    public function getQuestions(): array
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('
                SELECT q.id, q.title, q.sort_order, q.is_active
                FROM questions q 
                WHERE q.is_active = 1 
                ORDER BY q.sort_order ASC
            ');
            $stmt->execute();
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result = [];
            foreach ($questions as $question) {
                $options = $this->getQuestionOptions($question['id']);
                $result[] = [
                    'id' => (int)$question['id'],
                    'title' => $question['title'],
                    'options' => $options,
                ];
            }

            return $result;
        } catch (\Throwable $e) {
            error_log('QuestionService error: ' . $e->getMessage());
            return $this->getFallbackQuestions();
        }
    }

    public function getAllQuestions(): array
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('
                SELECT q.id, q.title, q.sort_order, q.is_active
                FROM questions q 
                ORDER BY q.sort_order ASC
            ');
            $stmt->execute();
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result = [];
            foreach ($questions as $question) {
                $options = $this->getQuestionOptions($question['id']);
                $result[] = [
                    'id' => (int)$question['id'],
                    'title' => $question['title'],
                    'sort_order' => (int)$question['sort_order'],
                    'is_active' => (bool)$question['is_active'],
                    'options' => $options,
                ];
            }

            return $result;
        } catch (\Throwable $e) {
            error_log('QuestionService error: ' . $e->getMessage());
            return [];
        }
    }

    public function getQuestionById(int $id): ?array
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('
                SELECT q.id, q.title, q.sort_order, q.is_active
                FROM questions q 
                WHERE q.id = :id
            ');
            $stmt->execute([':id' => $id]);
            $question = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$question) {
                return null;
            }

            $options = $this->getQuestionOptions($question['id']);
            
            return [
                'id' => (int)$question['id'],
                'title' => $question['title'],
                'sort_order' => (int)$question['sort_order'],
                'is_active' => (bool)$question['is_active'],
                'options' => $options,
            ];
        } catch (\Throwable $e) {
            error_log('QuestionService error: ' . $e->getMessage());
            return null;
        }
    }

    public function createQuestion(string $title, array $options): bool
    {
        try {
            $pdo = Database::getConnection();
            $pdo->beginTransaction();

            // Inserir pergunta
            $stmt = $pdo->prepare('
                INSERT INTO questions (title, sort_order) 
                VALUES (:title, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM questions q))
            ');
            $stmt->execute([':title' => $title]);
            $questionId = (int)$pdo->lastInsertId();

            // Inserir opções
            $this->insertQuestionOptions($pdo, $questionId, $options);

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            error_log('QuestionService create error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateQuestion(int $id, string $title, array $options): bool
    {
        try {
            $pdo = Database::getConnection();
            $pdo->beginTransaction();

            // Atualizar pergunta
            $stmt = $pdo->prepare('UPDATE questions SET title = :title WHERE id = :id');
            $stmt->execute([':title' => $title, ':id' => $id]);

            // Remover opções antigas
            $stmt = $pdo->prepare('DELETE FROM question_options WHERE question_id = :id');
            $stmt->execute([':id' => $id]);

            // Inserir novas opções
            $this->insertQuestionOptions($pdo, $id, $options);

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            error_log('QuestionService update error: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteQuestion(int $id): bool
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('DELETE FROM questions WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (\Throwable $e) {
            error_log('QuestionService delete error: ' . $e->getMessage());
            return false;
        }
    }

    public function toggleQuestionStatus(int $id): bool
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('UPDATE questions SET is_active = NOT is_active WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (\Throwable $e) {
            error_log('QuestionService toggle error: ' . $e->getMessage());
            return false;
        }
    }

    private function getQuestionOptions(int $questionId): array
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('
                SELECT option_id, text 
                FROM question_options 
                WHERE question_id = :question_id 
                ORDER BY option_id ASC
            ');
            $stmt->execute([':question_id' => $questionId]);
            $options = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result = [];
            foreach ($options as $option) {
                $result[] = [
                    'id' => $option['option_id'],
                    'text' => $option['text'],
                ];
            }

            return $result;
        } catch (\Throwable $e) {
            error_log('QuestionService options error: ' . $e->getMessage());
            return [];
        }
    }

    private function insertQuestionOptions(PDO $pdo, int $questionId, array $options): void
    {
        $stmt = $pdo->prepare('
            INSERT INTO question_options (question_id, option_id, text) 
            VALUES (:question_id, :option_id, :text)
        ');

        foreach ($options as $optionId => $text) {
            if (!empty(trim($text))) {
                $stmt->execute([
                    ':question_id' => $questionId,
                    ':option_id' => $optionId,
                    ':text' => trim($text),
                ]);
            }
        }
    }

    private function getFallbackQuestions(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Sobre sua saúde, há alguma condição relevante que devamos considerar?',
                'options' => [
                    ['id' => 'a', 'text' => 'Hipertensão controlada'],
                    ['id' => 'b', 'text' => 'Diabetes controlado'],
                    ['id' => 'c', 'text' => 'Lesões ou limitações físicas'],
                    ['id' => 'd', 'text' => 'Nenhuma das opções acima'],
                ],
            ],
            [
                'id' => 2,
                'title' => 'Como é sua rotina diária em termos de tempo disponível?',
                'options' => [
                    ['id' => 'a', 'text' => 'Menos de 30 minutos por dia'],
                    ['id' => 'b', 'text' => 'Entre 30 e 60 minutos por dia'],
                    ['id' => 'c', 'text' => 'Mais de 60 minutos por dia'],
                ],
            ],
        ];
    }
}
