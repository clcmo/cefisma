<?php
declare(strict_types=1);

namespace Cefisma\Controllers;

use Cefisma\Core\Controller;
use Cefisma\Models\Edicao;
use Cefisma\Models\Evento;
use Cefisma\Models\Participacao;
use Cefisma\Models\Produto;
use Cefisma\Models\Publicacao;

/** Página inicial: junta Supernova, agenda, publicações, loja e formulários. */
final class HomeController extends Controller
{
    public function index(): void
    {
        $edicoes = Edicao::todas(6);
        $mesAtual = wp_date('Y-m');
        $mesSelecionado = isset($_GET['mes']) && is_string($_GET['mes'])
            ? sanitize_text_field(wp_unslash($_GET['mes']))
            : '';
        if (preg_match('/^(\d{4})-(\d{2})$/', $mesSelecionado, $partes) && checkdate((int) $partes[2], 1, (int) $partes[1])) {
            $mesAtual = $mesSelecionado;
        }
        [$ano, $mes] = array_map('intval', explode('-', $mesAtual));

        $this->render('pages/home', [
            'edicao'       => $edicoes[0] ?? null,
            'edicoes'      => $edicoes,
            'calendario'   => Evento::calendario($ano, $mes),
            'eventos'      => Evento::proximos(3),
            'publicacoes'  => Publicacao::recentes(3),
            'produtos'     => Produto::destaques(4),
            'participe'    => Participacao::itens(),
            'url_loja'     => Produto::urlLoja(),
            'url_arquivo'  => (string) get_post_type_archive_link(Edicao::TIPO),
            'url_envio'    => Participacao::urlEnvioSupernova(),
            'url_posts'    => home_url('/boletim/'),
        ]);
    }
}
