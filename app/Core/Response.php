<?php
namespace App\Core;

class Response
{
    public static function view(string $viewPath, array $data = [], string $layout = 'main'): void
    {
        extract($data);

        // Capture view content
        $viewFile = __DIR__ . '/../Views/' . $viewPath . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View file not found: {$viewPath}");
        }

        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // Render in layout if requested
        if ($layout) {
            $layoutFile = __DIR__ . '/../Views/layouts/' . $layout . '.php';
            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout file not found: {$layout}");
            }
            include $layoutFile;
        } else {
            echo $content;
        }
    }

    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}
