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

        $this->render('pages/home', [
            'edicao'       => $edicoes[0] ?? null,
            'edicoes'      => $edicoes,
            'eventos'      => Evento::proximos(3),
            'publicacoes'  => Publicacao::recentes(3),
            'produtos'     => Produto::blusas(4),
            'participe'    => Participacao::itens(),
            'url_loja'     => Produto::urlLoja(),
            'url_arquivo'  => (string) get_post_type_archive_link(Edicao::TIPO),
            'url_envio'    => Participacao::urlEnvioSupernova(),
            'url_posts'    => home_url('/boletim/'),
        ]);
    }
}
