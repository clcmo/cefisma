<?php
/**
 * Template Name: Quem somos
 * Template Post Type: page
 * Template fino: o Controller monta o conteúdo e o layout do tema.
 */
use Cefisma\Controllers\PaginaController;

Cefisma\Core\Theme::dispatch(PaginaController::class, 'quemSomos');
