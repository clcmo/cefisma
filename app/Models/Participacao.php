<?php
declare(strict_types=1);

namespace Cefisma\Models;

/**
 * Formas de participar (formulários e editais). Valores padrão apontam para as
 * páginas atuais do site; use o filtro "cefisma_participe" para alterar.
 */
final class Participacao
{
    public static function itens(): array
    {
        $itens = [
            [
                'titulo' => 'Formulário de reclamações das disciplinas',
                'texto'  => 'Conte o que está funcionando e o que não está nas disciplinas do IFUSP.',
                'botao'  => 'Responder',
                'url'    => home_url('/boletim/cefisma/formularios/participe-do-formulario-de-reclamacoes-e-sugestoes-das-disciplinas-do-ifusp/'),
            ],
            [
                'titulo' => 'Texto ou arte para o Supernova',
                'texto'  => 'Envie sua contribuição para a próxima edição.',
                'botao'  => 'Enviar',
                'url'    => home_url('/boletim/cefisma/formularios/formularios-de-envio-de-textos-e-artes-para-o-boletim-supernova/'),
            ],
            [
                'titulo' => 'Representante de turma',
                'texto'  => 'Leia o edital e inscreva-se para representar sua turma.',
                'botao'  => 'Ler o edital',
                'url'    => home_url('/boletim/edital-para-representante-de-turma/'),
            ],
        ];
        return apply_filters('cefisma_participe', $itens);
    }

    /** Link do formulário de envio ao Supernova (primeiro item que contém "supernova"). */
    public static function urlEnvioSupernova(): string
    {
        foreach (self::itens() as $i) {
            if (stripos($i['url'], 'supernova') !== false) {
                return $i['url'];
            }
        }
        return home_url('/formularios/');
    }
}
