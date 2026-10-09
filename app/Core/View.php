<?php
declare(strict_types=1);

namespace Cefisma\Core;

/**
 * Renderizador de Views. Uma View recebe apenas arrays/valores prontos
 * e não consulta o banco: toda busca de dados fica nos Models.
 */
final class View
{
    /** Renderiza $view dentro do layout (ou sozinha, se $layout for null). */
    public static function render(string $view, array $dados = [], ?string $layout = 'layouts/base'): void
    {
        $conteudo = self::capturar($view, $dados);
        if ($layout === null) {
            echo $conteudo; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML já escapado nas Views
            return;
        }
        self::incluir($layout, $dados + ['conteudo' => $conteudo]);
    }

    public static function capturar(string $view, array $dados = []): string
    {
        ob_start();
        self::incluir($view, $dados);
        return (string) ob_get_clean();
    }

    public static function partial(string $view, array $dados = []): void
    {
        self::incluir($view, $dados);
    }

    private static function incluir(string $view, array $dados): void
    {
        $arquivo = CEFISMA_DIR . '/app/Views/' . $view . '.php';
        if (!is_readable($arquivo)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                trigger_error('View não encontrada: ' . esc_html($view), E_USER_WARNING);
            }
            return;
        }
        // Escopo isolado: a View só enxerga as variáveis em $dados.
        (static function (string $__arquivo, array $__dados): void {
            extract($__dados, EXTR_SKIP);
            include $__arquivo;
        })($arquivo, $dados);
    }
}
