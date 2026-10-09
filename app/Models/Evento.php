<?php
declare(strict_types=1);

namespace Cefisma\Models;

/** Evento da agenda. Substitui os shortcodes [calendar id=…] do site atual. */
final class Evento
{
    public const TIPO = 'evento';

    public static function register(): void
    {
        add_action('init', [self::class, 'registrar']);
    }

    public static function campos(): array
    {
        return [
            'inicio' => ['Data (AAAA-MM-DD)', 'date'],
            'local'  => ['Local ou observação', 'text'],
            'link'   => ['Link (opcional)', 'url'],
        ];
    }

    public static function registrar(): void
    {
        register_post_type(self::TIPO, [
            'labels'       => ['name' => 'Agenda', 'singular_name' => 'Evento', 'add_new_item' => 'Adicionar evento', 'menu_name' => 'Agenda'],
            'public'       => false,
            'show_ui'      => true,
            'menu_icon'    => 'dashicons-calendar-alt',
            'supports'     => ['title'],
            'show_in_rest' => false,
        ]);
    }

    /** @return array[] próximos eventos, do mais próximo ao mais distante */
    public static function proximos(int $limite = 4): array
    {
        $consulta = new \WP_Query([
            'post_type'      => self::TIPO,
            'posts_per_page' => $limite,
            'no_found_rows'  => true,
            'meta_key'       => 'inicio',
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
            'meta_query'     => [['key' => 'inicio', 'value' => wp_date('Y-m-d'), 'compare' => '>=', 'type' => 'DATE']],
        ]);
        return array_map(static function (\WP_Post $p): array {
            $inicio = (string) get_post_meta($p->ID, 'inicio', true);
            $ts = strtotime($inicio . ' 12:00:00') ?: time();
            return [
                'id'     => $p->ID,
                'titulo' => get_the_title($p),
                'iso'    => $inicio,
                'dia'    => wp_date('j', $ts),
                'mes'    => wp_date('M', $ts),
                'local'  => (string) get_post_meta($p->ID, 'local', true),
                'link'   => (string) get_post_meta($p->ID, 'link', true),
            ];
        }, $consulta->posts);
    }
}
