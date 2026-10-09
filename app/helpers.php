<?php
declare(strict_types=1);

if (!function_exists('cefisma_view')) {
    /** Atalho para incluir um partial de dentro de uma View. */
    function cefisma_view(string $view, array $dados = []): void
    {
        Cefisma\Core\View::partial($view, $dados);
    }
}
