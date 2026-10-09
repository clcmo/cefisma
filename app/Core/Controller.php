<?php
declare(strict_types=1);

namespace Cefisma\Core;

use Cefisma\Models\Aviso;
use Cefisma\Models\Navegacao;
use Cefisma\Models\Produto;

/** Base dos Controllers: junta os dados globais do layout e delega à View. */
abstract class Controller
{
    /** Registra hooks do WordPress (opcional). Chamado por Theme::boot(). */
    public function register(): void
    {
    }

    /** Dados que todo layout precisa (avisos, menu, carrinho, canais). */
    protected function globais(): array
    {
        return [
            'avisos'   => Aviso::ativos(),
            'menu'     => Navegacao::principal(),
            'canais'   => Navegacao::canais(),
            'carrinho' => Produto::resumoCarrinho(),
            'transparencia' => Navegacao::transparencia(),
            'contato'  => Navegacao::contato(),
            'whatsapp' => Navegacao::whatsapp(),
        ];
    }

    protected function render(string $view, array $dados = []): void
    {
        View::render($view, $dados + $this->globais());
    }
}
