<?php
/**
 * Template fino: o WordPress escolhe o arquivo, o Controller faz o resto.
 */
use Cefisma\Controllers\LojaController;

Cefisma\Core\Theme::dispatch(LojaController::class, 'loja');
