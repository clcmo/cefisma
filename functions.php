<?php
/**
 * Ponto de entrada do tema. Só carrega o autoloader e inicia o Theme.
 * Nenhuma regra de negócio mora aqui: veja app/.
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('CEFISMA_VERSION', '0.1.1');
define('CEFISMA_DIR', get_template_directory());
define('CEFISMA_URI', get_template_directory_uri());

require_once CEFISMA_DIR . '/app/Core/Autoloader.php';
Cefisma\Core\Autoloader::register('Cefisma\\', CEFISMA_DIR . '/app/');

require_once CEFISMA_DIR . '/app/helpers.php';

Cefisma\Core\Theme::boot();
