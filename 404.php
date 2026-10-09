<?php
/**
 * Template fino: o WordPress escolhe o arquivo, o Controller faz o resto.
 */
use Cefisma\Controllers\PaginaController;

Cefisma\Core\Theme::dispatch(PaginaController::class, 'naoEncontrado');
