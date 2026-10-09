<?php
declare(strict_types=1);

namespace Cefisma\Controllers\Admin;

use Cefisma\Core\Campos;
use Cefisma\Core\Controller;
use Cefisma\Core\View;
use Cefisma\Models\Aviso;
use Cefisma\Models\Edicao;
use Cefisma\Models\Evento;

/**
 * Caixa "Detalhes" no editor de Edição, Aviso e Evento.
 * Os campos vêm de Model::campos(): para criar um campo novo, edite só o Model.
 */
final class MetaBoxController extends Controller
{
    private const NONCE = 'cefisma_campos';

    /** @return array<string, array> tipo de conteúdo => campos */
    private function mapa(): array
    {
        return [
            Edicao::TIPO => Edicao::campos(),
            Aviso::TIPO  => Aviso::campos(),
            Evento::TIPO => Evento::campos(),
        ];
    }

    public function register(): void
    {
        add_action('add_meta_boxes', [$this, 'adicionar']);
        add_action('save_post', [$this, 'salvar'], 10, 2);
    }

    public function adicionar(): void
    {
        foreach (array_keys($this->mapa()) as $tipo) {
            add_meta_box('cefisma-campos', 'Detalhes', [$this, 'exibir'], $tipo, 'normal', 'high');
        }
    }

    public function exibir(\WP_Post $post): void
    {
        $campos = [];
        foreach ($this->mapa()[$post->post_type] ?? [] as $chave => [$rotulo, $tipo]) {
            $campos[] = [
                'chave'  => $chave,
                'rotulo' => $rotulo,
                'tipo'   => $tipo,
                'valor'  => Campos::paraTexto($tipo, get_post_meta($post->ID, $chave, true)),
            ];
        }
        View::render('admin/campos', ['campos' => $campos, 'nonce' => self::NONCE], null);
    }

    public function salvar(int $postId, \WP_Post $post): void
    {
        if (!isset($this->mapa()[$post->post_type])) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        $nonce = isset($_POST[self::NONCE]) ? sanitize_text_field(wp_unslash($_POST[self::NONCE])) : '';
        if (!wp_verify_nonce($nonce, self::NONCE) || !current_user_can('edit_post', $postId)) {
            return;
        }
        foreach ($this->mapa()[$post->post_type] as $chave => [, $tipo]) {
            $bruto = isset($_POST['cefisma'][$chave]) ? wp_unslash($_POST['cefisma'][$chave]) : '';
            update_post_meta($postId, $chave, Campos::sanitizar($tipo, $bruto));
        }
    }
}
