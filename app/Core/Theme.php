<?php
declare(strict_types=1);

namespace Cefisma\Core;

use Cefisma\Controllers\Admin\MetaBoxController;
use Cefisma\Controllers\Api\EdicaoApiController;
use Cefisma\Controllers\LojaController;
use Cefisma\Controllers\NavegacaoController;
use Cefisma\Models\Aviso;
use Cefisma\Models\Edicao;
use Cefisma\Models\Evento;

/** Inicialização do tema: registra Models, Controllers, suportes e assets. */
final class Theme
{
    /** Models que registram tipos de conteúdo (CPTs). */
    private const MODELS = [Edicao::class, Aviso::class, Evento::class];

    /** Controllers que escutam hooks do WordPress. */
    private const CONTROLLERS = [
        NavegacaoController::class,
        LojaController::class,
        EdicaoApiController::class,
        MetaBoxController::class,
    ];

    public static function boot(): void
    {
        add_action('after_setup_theme', [self::class, 'suportes']);
        add_action('wp_enqueue_scripts', [self::class, 'assets']);

        foreach (self::MODELS as $model) {
            $model::register();
        }
        foreach (self::CONTROLLERS as $controller) {
            (new $controller())->register();
        }
    }

    /**
     * Chamado pelos arquivos de template (front-page.php etc.).
     * O WordPress escolhe o template; este método escolhe o Controller.
     */
    public static function dispatch(string $controller, string $acao, array $args = []): void
    {
        $instancia = new $controller();
        if (!method_exists($instancia, $acao)) {
            wp_die(esc_html("Ação inexistente: {$controller}::{$acao}"));
        }
        $instancia->{$acao}(...$args);
    }

    public static function suportes(): void
    {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption', 'style', 'script']);
        add_theme_support('woocommerce');
        add_image_size('capa', 600, 800, true);

        register_nav_menus([
            'principal' => 'Menu principal',
        ]);
    }

    public static function assets(): void
    {
        wp_enqueue_style(
            'cefisma-fontes',
            'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;600;700;800&family=Source+Serif+4:ital,wght@0,400;0,600;1,400&display=swap',
            [],
            null
        );
        wp_enqueue_style('cefisma', CEFISMA_URI . '/assets/css/main.css', ['cefisma-fontes'], CEFISMA_VERSION);

        $home = is_front_page();
        $supernova = is_post_type_archive(Edicao::TIPO) || is_singular(Edicao::TIPO);
        $woocommerce = function_exists('is_woocommerce') && (is_woocommerce() || is_cart() || is_checkout() || is_account_page());
        $publicacoes = $home || $supernova || is_home() || (!$woocommerce && (is_singular() || is_archive() || is_search()));

        if ($publicacoes) {
            wp_enqueue_style('cefisma-content', CEFISMA_URI . '/assets/css/content.css', ['cefisma'], CEFISMA_VERSION);
        }
        if ($home || $supernova) {
            wp_enqueue_style('cefisma-supernova', CEFISMA_URI . '/assets/css/supernova.css', ['cefisma'], CEFISMA_VERSION);
        }
        if ($supernova) {
            wp_enqueue_style('cefisma-edition', CEFISMA_URI . '/assets/css/edition.css', ['cefisma-supernova'], CEFISMA_VERSION);
        }
        if ($home) {
            wp_enqueue_style('cefisma-home', CEFISMA_URI . '/assets/css/home.css', ['cefisma-content', 'cefisma-supernova'], CEFISMA_VERSION);
        }

        $loja = $home || $supernova || $woocommerce;
        if ($loja) {
            wp_enqueue_style('cefisma-store', CEFISMA_URI . '/assets/css/store.css', ['cefisma'], CEFISMA_VERSION);
        }
        if ($woocommerce) {
            wp_enqueue_style('cefisma-woocommerce', CEFISMA_URI . '/assets/css/woocommerce.css', ['cefisma-store'], CEFISMA_VERSION);
        }

        if (!$home && (!$woocommerce && (is_singular() || is_home() || is_archive() || is_search()) || is_404())) {
            wp_enqueue_style('cefisma-editorial', CEFISMA_URI . '/assets/css/editorial.css', ['cefisma'], CEFISMA_VERSION);
        }

        wp_enqueue_script('cefisma', CEFISMA_URI . '/assets/js/main.js', [], CEFISMA_VERSION, ['in_footer' => true, 'strategy' => 'defer']);
    }
}
