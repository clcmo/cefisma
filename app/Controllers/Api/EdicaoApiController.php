<?php
declare(strict_types=1);

namespace Cefisma\Controllers\Api;

use Cefisma\Core\Controller;
use Cefisma\Models\Edicao;

/**
 * API usada pelo pipeline das edições Supernova (LaTeX -> GitHub Actions -> site).
 *
 *   POST /wp-json/cefisma/v1/edicoes          cria/atualiza a edição pelo número (autenticado)
 *   GET  /wp-json/cefisma/v1/edicoes/ultima   devolve a edição mais recente (público)
 *
 * Autenticação: Application Passwords do WordPress (Usuários > Perfil).
 */
final class EdicaoApiController extends Controller
{
    private const NS = 'cefisma/v1';

    public function register(): void
    {
        add_action('rest_api_init', [$this, 'rotas']);
    }

    public function rotas(): void
    {
        register_rest_route(self::NS, '/edicoes', [
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'salvar'],
            'permission_callback' => static fn() => current_user_can('publish_posts'),
            'args'                => [
                'numero'   => ['type' => 'integer', 'required' => true, 'minimum' => 1],
                'serie'    => ['type' => 'string', 'enum' => ['boletim', 'folhetim']],
                'titulo'   => ['type' => 'string'],
                'resumo'   => ['type' => 'string'],
                'data'     => ['type' => 'string'],
                'data_publicacao' => ['type' => 'string'],
                'slug'     => ['type' => 'string'],
                'pdf_url'  => ['type' => 'string', 'format' => 'uri'],
                'capa_url' => ['type' => 'string', 'format' => 'uri'],
                'origem_url' => ['type' => 'string', 'format' => 'uri'],
                'sumario'  => [
                    'type'  => 'array',
                    'items' => ['type' => 'object', 'properties' => [
                        'titulo' => ['type' => 'string'],
                        'url'    => ['type' => 'string'],
                    ]],
                ],
            ],
        ]);

        register_rest_route(self::NS, '/edicoes/ultima', [
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [$this, 'ultima'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function salvar(\WP_REST_Request $req)
    {
        $id = Edicao::salvarPorNumero($req->get_json_params() ?: $req->get_params());
        if (is_wp_error($id)) {
            return $id;
        }
        $post = get_post($id);
        return new \WP_REST_Response(Edicao::desdePost($post), 200);
    }

    public function ultima(): \WP_REST_Response
    {
        $edicao = Edicao::ultima();
        return new \WP_REST_Response($edicao ?? ['mensagem' => 'Nenhuma edição publicada.'], $edicao ? 200 : 404);
    }
}
