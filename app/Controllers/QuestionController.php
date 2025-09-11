<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\QuestionService;
use App\Services\SettingsService;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class QuestionController
{
    private Environment $twig;
    private QuestionService $questions;
    private SettingsService $settings;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../Views');
        $this->twig = new Environment($loader, [
            'cache' => false,
            'auto_reload' => true,
        ]);
        $this->questions = new QuestionService();
        $this->settings = new SettingsService();
    }

    public function root(): void
    {
        $this->redirect('/q/1');
    }

    public function show(int $n): void
    {
        $items = $this->questions->getQuestions();
        if ($n < 1 || $n > count($items)) {
            $this->redirect('/q/1');
            return;
        }

        $question = $items[$n - 1];

        $prevLink = $n > 1 ? '/q/' . ($n - 1) : null;
        $nextPost = '/q/' . $n;

        echo $this->twig->render('question.twig', [
            'question' => $question,
            'number' => $n,
            'total' => count($items),
            'prevLink' => $prevLink,
            'nextPost' => $nextPost,
            'backgroundUrl' => $this->settings->getBackgroundUrl(),
        ]);
    }

    public function submit(int $n): void
    {
        $items = $this->questions->getQuestions();
        if ($n < 1 || $n > count($items)) {
            $this->redirect('/q/1');
            return;
        }

        if ($n < count($items)) {
            $this->redirect('/q/' . ($n + 1));
            return;
        }

        $this->redirect('/end');
    }

    public function end(): void
    {
        $purchaseUrl = 'https://example.com/checkout/ABC123';
        echo $this->twig->render('end.twig', [
            'purchaseUrl' => $purchaseUrl,
            'backgroundUrl' => $this->settings->getBackgroundUrl(),
        ]);
    }

    public function restart(): void
    {
        $this->redirect('/q/1');
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url, true, 302);
        exit;
    }
}
