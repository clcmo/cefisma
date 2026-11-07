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
    private const PERMALINK_STRUCTURE = '/%category%/%postname%/';
    private const PERMALINK_MIGRATION = 'category-postname-v1';

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
        add_action('admin_init', [self::class, 'criarPaginasExemplo']);
        add_action('after_switch_theme', [self::class, 'criarPaginasExemplo']);
        add_action('admin_init', [self::class, 'migrarPermalinks']);
        add_action('after_switch_theme', [self::class, 'configurarPermalinks']);

        foreach (self::MODELS as $model) {
            $model::register();
        }
        foreach (self::CONTROLLERS as $controller) {
            (new $controller())->register();
        }
    }

    /** Cria páginas demonstrativas ausentes e associa os templates do tema. */
    public static function criarPaginasExemplo(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $paginas = [
            [
                'slug'     => 'supernova',
                'titulo'   => 'Supernova',
                'template' => 'page-supernova.php',
            ],
            [
                'slug'     => 'quem-somos',
                'titulo'   => 'Quem somos',
                'template' => 'page-quem-somos.php',
            ],
            [
                'slug'     => 'comunicacao',
                'titulo'   => 'Comunicação',
                'template' => 'page-comunicacao.php',
            ],
            [
                'slug'     => 'participe',
                'titulo'   => 'Participe',
                'template' => 'page-participe.php',
            ],
        ];

        foreach ($paginas as $pagina) {
            $existente = get_page_by_path($pagina['slug'], OBJECT, 'page');
            if ($existente) {
                $templateAtual = get_page_template_slug($existente->ID);
                if ($templateAtual === '' || $templateAtual === 'default') {
                    update_post_meta($existente->ID, '_wp_page_template', $pagina['template']);
                }
                continue;
            }

            $id = wp_insert_post([
                'post_type'   => 'page',
                'post_status' => 'publish',
                'post_title'  => $pagina['titulo'],
                'post_name'   => $pagina['slug'],
            ], true);

            if (is_wp_error($id)) {
                error_log(sprintf('CEFISMA: não foi possível criar a página "%s": %s', $pagina['slug'], $id->get_error_message()));
                continue;
            }

            update_post_meta($id, '_wp_page_template', $pagina['template']);
        }
    }

    /** Define posts como /categoria/postname e páginas como /postname. */
    public static function configurarPermalinks(): void
    {
        update_option('permalink_structure', self::PERMALINK_STRUCTURE);
        update_option('cefisma_permalink_migration', self::PERMALINK_MIGRATION);
        flush_rewrite_rules();
    }

    /** Aplica uma vez a configuração quando o tema é atualizado sem reativação. */
    public static function migrarPermalinks(): void
    {
        if (get_option('cefisma_permalink_migration') === self::PERMALINK_MIGRATION) {
            return;
        }

        self::configurarPermalinks();
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
        $paginaSupernova = is_page_template('page-supernova.php');
        $supernova = is_post_type_archive(Edicao::TIPO) || is_singular(Edicao::TIPO);
        $woocommerce = function_exists('is_woocommerce') && (is_woocommerce() || is_cart() || is_checkout() || is_account_page());
        $publicacoes = $home || $supernova || is_home() || (!$woocommerce && (is_singular() || is_archive() || is_search()));

        if ($publicacoes) {
            wp_enqueue_style('cefisma-content', CEFISMA_URI . '/assets/css/content.css', ['cefisma'], CEFISMA_VERSION);
        }
        if ($home || $supernova || $paginaSupernova) {
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
