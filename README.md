# Tema CEFISMA (MVC)

Tema WordPress do Centro Acadêmico do Instituto de Física da USP, com o Boletim Supernova e a loja (WooCommerce) integrados.

## Arquitetura

```
functions.php          só carrega o autoloader e chama Theme::boot()
*.php (raiz)           templates finos: escolhem o Controller e a ação
app/
  Core/                Autoloader, Theme (boot + dispatch), View, Controller, Campos
  Models/              dados: Edicao, Aviso, Evento, Publicacao, Produto, Navegacao, Participacao
  Controllers/         orquestração: Home, Supernova, Pagina, Loja, Navegacao
    Api/               EdicaoApiController (REST para o pipeline do boletim)
    Admin/             MetaBoxController (campos no editor)
  Views/               HTML (layouts, partials, components, pages, supernova, loja, admin)
assets/                css/main.css e js/main.js
```

Fluxo de uma requisição: o WordPress escolhe o template (`front-page.php`) → ele chama `Theme::dispatch(HomeController::class, 'index')` → o Controller pede dados aos **Models** → entrega arrays prontos à **View**, que só imprime (com escape). Views nunca consultam o banco.

| Template | Controller::ação |
|---|---|
| `front-page.php` | `HomeController::index` |
| `archive-edicao.php` | `SupernovaController::arquivo` |
| `single-edicao.php` | `SupernovaController::edicao` |
| `page.php`, `single.php` | `PaginaController::conteudo` |
| `index.php`, `archive.php`, `search.php` | `PaginaController::listar` |
| `404.php` | `PaginaController::naoEncontrado` |
| `woocommerce.php` | `LojaController::loja` |

## Instalação

1. Compacte a pasta `cefisma/` em `.zip` (ou use o arquivo entregue) e envie em Aparência > Temas > Adicionar.
2. Ative o tema e vá em Configurações > Links permanentes > Salvar (atualiza as rotas das edições).
3. Em Aparência > Menus, crie o menu e atribua ao local "Menu principal" (sem menu, o tema usa os itens padrão de `Models/Navegacao.php`).
4. Instale e ative o WooCommerce. Crie a categoria de produtos com slug `supernova` para o bloco "Apoie o Supernova".

**Atenção:** o arquivo das edições usa o endereço `/supernova/boletim/`, o mesmo da página atual "Supernova > Boletim". Renomeie o slug da página antiga ou exclua-a, senão haverá conflito de rotas.

## Integração com o Boletim Supernova

O tipo de conteúdo **Edição** guarda número, PDF, capa, resumo e sumário. O pipeline de build do boletim em LaTeX pode publicar tudo pela API (idempotente: o mesmo número atualiza a mesma edição).

1. Em Usuários > Perfil, crie uma *Senha de aplicativo*.
2. Publique a edição (exemplo com `curl`; no GitHub Actions, guarde `WP_USER` e `WP_APP_PASSWORD` como *secrets*):

```bash
curl -X POST "https://cefisma.com.br/wp-json/cefisma/v1/edicoes" \
  -u "$WP_USER:$WP_APP_PASSWORD" \
  -H "Content-Type: application/json" \
  -d '{
    "numero": 6,
    "titulo": "Supernova #6",
    "data": "2026-10-03",
    "resumo": "Na seção Me conta, a equipe indica obras que apreciou.",
    "pdf_url": "https://cefisma.com.br/wp-content/uploads/2026/10/supernova-6.pdf",
    "capa_url": "https://cefisma.com.br/wp-content/uploads/2026/10/supernova-6-capa.png",
    "sumario": [
      {"titulo": "Editorial", "url": ""},
      {"titulo": "Me conta", "url": "https://cefisma.com.br/boletim/me-conta-supernova-6/"}
    ]
  }'
```

`GET /wp-json/cefisma/v1/edicoes/ultima` devolve a edição mais recente (público).

Sem `capa_url` nem imagem destacada, o tema gera uma capa em SVG com o número da edição.

## Integração com a loja

- **Supernova → loja:** páginas do arquivo e de cada edição mostram "Apoie o Supernova" com os produtos da categoria `supernova`.
- **Curadoria Mediacast:** o arquivo do Supernova mostra até três posts publicados na categoria `mediacast`; use essa categoria para selecionar as recomendações.
- **Blusas na home:** a página inicial mostra até quatro produtos publicados e em estoque da categoria `blusas`.
- **Checkout:** campo "Quero receber o Supernova por e-mail". A escolha fica no pedido (`_cefisma_newsletter`) e dispara o hook `cefisma_newsletter_optin`. Exemplo para conectar a um plugin de newsletter:

```php
add_action('cefisma_newsletter_optin', function (string $email, WC_Order $pedido) {
    // inscreva $email na lista do Supernova (MailPoet, Brevo, etc.)
}, 10, 2);
```

- **Pós-compra:** a página "Pedido recebido" mostra um bloco "Leia o Supernova" com a edição mais recente.

## Filtros úteis

| Filtro | Para quê |
|---|---|
| `cefisma_whatsapp_url` | URL do WhatsApp (vazio oculta o botão) |
| `cefisma_contato` | e-mail e texto do rodapé |
| `cefisma_participe` | lista de formulários da seção "Participe" |

## Conteúdo no admin

- **Avisos:** faixa de topo, com link e data final opcionais.
- **Agenda:** eventos com data; substitui os shortcodes `[calendar id=…]`.
- **Supernova (Edições):** campos de número, PDF, capa e sumário (uma seção por linha: `Título | URL`).

## Como estender

- Novo campo em um tipo de conteúdo: edite `campos()` no Model; o admin e a API passam a aceitá-lo.
- Nova página com dados próprios: crie `Controllers/NomeController.php`, uma View em `Views/` e um template fino na raiz.

## Notas

- As fontes vêm do Google Fonts. Para evitar o envio de dados a terceiros (LGPD), baixe-as para `assets/fonts/` e troque o `wp_enqueue_style` em `Core/Theme.php`.
- A contagem do carrinho no topo é a do carregamento da página; para atualizar sem recarregar, use os *cart fragments* do WooCommerce.
- Os textos de exemplo (Participe, canais, transparência) usam os endereços atuais do site e podem ser ajustados nos Models.
