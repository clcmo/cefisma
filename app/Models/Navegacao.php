<?php
declare(strict_types=1);

namespace Cefisma\Models;

/** Itens de navegação padrão (usados quando nenhum menu foi atribuído em Aparência > Menus). */
final class Navegacao
{
    public static function principal(): array
    {
        $requisicao = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '/'; // só comparado, nunca impresso
        $atual = trailingslashit((string) wp_parse_url((string) $requisicao, PHP_URL_PATH));
        $itens = [
            ['rotulo' => 'Início',      'url' => home_url('/'),                  'classe' => ''],
            ['rotulo' => 'Quem somos',  'url' => home_url('/quem-somos/'),       'classe' => ''],
            ['rotulo' => 'Supernova',   'url' => (string) get_post_type_archive_link(Edicao::TIPO) ?: home_url('/supernova/'), 'classe' => ''],
            ['rotulo' => 'Comunicação', 'url' => home_url('/category/rd/'),      'classe' => ''],
            ['rotulo' => 'Participe',   'url' => home_url('/formularios/'),      'classe' => ''],
            ['rotulo' => 'Loja',        'url' => Produto::urlLoja(),             'classe' => 'menu-loja'],
        ];
        foreach ($itens as &$i) {
            $caminho = trailingslashit((string) wp_parse_url($i['url'], PHP_URL_PATH));
            $i['atual'] = $caminho === '/' ? $atual === '/' : str_starts_with($atual, $caminho);
        }
        return $itens;
    }

    public static function canais(): array
    {
        return [
            ['rotulo' => 'Instagram', 'icone' => 'instagram', 'url' => 'https://www.instagram.com/cefisma/'],
            ['rotulo' => 'Telegram',  'icone' => 'telegram',  'url' => 'https://t.me/cefisma'],
            ['rotulo' => 'TikTok',    'icone' => 'tiktok',    'url' => 'https://www.tiktok.com/@cefisma'],
            ['rotulo' => 'X',         'icone' => 'x',         'url' => 'https://x.com/CEFISMA'],
            ['rotulo' => 'Facebook',  'icone' => 'facebook', 'url' => 'https://www.facebook.com/cefismausp/'],
        ];
    }

    public static function transparencia(): array
    {
        return [
            ['rotulo' => 'Transparência', 'url' => home_url('/transparencia/')],
            ['rotulo' => 'Relatórios',    'url' => home_url('/category/boletim/relatorios/')],
            ['rotulo' => 'Arquivos',      'url' => home_url('/arquivos/')],
            ['rotulo' => 'Eleição',       'url' => home_url('/eleicao/')],
        ];
    }

    public static function contato(): array
    {
        return apply_filters('cefisma_contato', [
            'email' => 'cefisma@cefisma.org.br',
            'sobre' => 'Centro Acadêmico do Instituto de Física da USP.',
        ]);
    }

    /** URL do WhatsApp (ex.: https://wa.me/55...). Vazio = botão oculto. Defina com o filtro cefisma_whatsapp_url. */
    public static function whatsapp(): string
    {
        return (string) apply_filters('cefisma_whatsapp_url', '');
    }
}
