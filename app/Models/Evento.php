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
        return array_map([self::class, 'formatar'], $consulta->posts);
    }

    /** @return array Calendário mensal, com semanas iniciadas na segunda-feira. */
    public static function calendario(int $ano, int $mes): array
    {
        $inicioMes = new \DateTimeImmutable(
            sprintf('%04d-%02d-01', $ano, $mes),
            wp_timezone()
        );
        $fimMes = $inicioMes->modify('last day of this month');
        $consulta = new \WP_Query([
            'post_type'      => self::TIPO,
            'posts_per_page' => -1,
            'no_found_rows'  => true,
            'meta_key'       => 'inicio',
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
            'meta_query'     => [[
                'key'     => 'inicio',
                'value'   => [$inicioMes->format('Y-m-d'), $fimMes->format('Y-m-d')],
                'compare' => 'BETWEEN',
                'type'    => 'DATE',
            ]],
        ]);

        $eventosPorDia = [];
        foreach ($consulta->posts as $post) {
            $evento = self::formatar($post);
            $eventosPorDia[$evento['iso']][] = $evento;
        }

        $celulas = array_fill(0, (int) $inicioMes->format('N') - 1, null);
        for ($dia = 1; $dia <= (int) $fimMes->format('j'); $dia++) {
            $data = $inicioMes->setDate($ano, $mes, $dia)->format('Y-m-d');
            $celulas[] = [
                'dia'     => $dia,
                'data'    => $data,
                'hoje'    => $data === wp_date('Y-m-d'),
                'eventos' => $eventosPorDia[$data] ?? [],
            ];
        }
        $celulas = array_pad($celulas, (int) (ceil(count($celulas) / 7) * 7), null);

        return [
            'titulo'      => wp_date('F Y', $inicioMes->getTimestamp()),
            'mes_anterior'=> $inicioMes->modify('-1 month')->format('Y-m'),
            'mes_seguinte'=> $inicioMes->modify('+1 month')->format('Y-m'),
            'dias_semana' => ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
            'semanas'     => array_chunk($celulas, 7),
        ];
    }

    private static function formatar(\WP_Post $post): array
    {
        $inicio = (string) get_post_meta($post->ID, 'inicio', true);
        $data = \DateTimeImmutable::createFromFormat('!Y-m-d', $inicio, wp_timezone());
        $ts = $data ? $data->getTimestamp() : time();

        return [
            'id'     => $post->ID,
            'titulo' => get_the_title($post),
            'iso'    => $inicio,
            'dia'    => wp_date('j', $ts),
            'mes'    => wp_date('M', $ts),
            'local'  => (string) get_post_meta($post->ID, 'local', true),
            'link'   => (string) get_post_meta($post->ID, 'link', true),
        ];
    }
}
