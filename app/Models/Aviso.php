<?php
declare(strict_types=1);

namespace Cefisma\Models;

/** Aviso de topo (greve, eleição, prazos). Some sozinho após a data "ate". */
final class Aviso
{
    public const TIPO = 'aviso';

    public static function register(): void
    {
        add_action('init', [self::class, 'registrar']);
    }

    public static function campos(): array
    {
        return [
            'link'       => ['Link (opcional)', 'url'],
            'link_texto' => ['Texto do link', 'text'],
            'ate'        => ['Exibir até (AAAA-MM-DD, opcional)', 'date'],
        ];
    }

    public static function registrar(): void
    {
        register_post_type(self::TIPO, [
            'labels'       => ['name' => 'Avisos', 'singular_name' => 'Aviso', 'add_new_item' => 'Adicionar aviso', 'menu_name' => 'Avisos'],
            'public'       => false,
            'show_ui'      => true,
            'menu_icon'    => 'dashicons-megaphone',
            'supports'     => ['title'],
            'show_in_rest' => false,
        ]);
    }

    /** @return array[] avisos vigentes (sem data final, ou com data final futura) */
    public static function ativos(int $limite = 2): array
    {
        $hoje = wp_date('Y-m-d');
        $consulta = new \WP_Query([
            'post_type'      => self::TIPO,
            'posts_per_page' => $limite,
            'no_found_rows'  => true,
            'meta_query'     => [
                'relation' => 'OR',
                ['key' => 'ate', 'compare' => 'NOT EXISTS'],
                ['key' => 'ate', 'value' => '', 'compare' => '='],
                ['key' => 'ate', 'value' => $hoje, 'compare' => '>=', 'type' => 'DATE'],
            ],
        ]);
        return array_map(static fn(\WP_Post $p) => [
            'id'          => $p->ID,
            'texto'       => get_the_title($p),
            'link'        => (string) get_post_meta($p->ID, 'link', true),
            'link_texto'  => (string) get_post_meta($p->ID, 'link_texto', true) ?: 'Saiba mais',
        ], $consulta->posts);
    }
}
