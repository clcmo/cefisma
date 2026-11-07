# Tema CEFISMA (MVC)

Tema WordPress do Centro Acadêmico do Instituto de Física da USP, com o Boletim Supernova e a loja (WooCommerce) integrados.

O tema requer PHP 7.4 ou superior e WordPress 6.4 ou superior.

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
assets/                css por módulos e js/main.js
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

## Estilos

`assets/css/main.css` contém a base global do tema. Os módulos `home.css`, `content.css`, `supernova.css`, `edition.css`, `store.css`, `woocommerce.css` e `editorial.css` são carregados conforme a rota WordPress, evitando enviar estilos específicos para páginas que não os utilizam. A versão do tema identifica os assets e renova o cache quando eles mudam.

## Prévia do tema

O workflow `.github/workflows/pages-preview.yml` publica versões estáticas e demonstrativas da página inicial, de Quem somos, Supernova, Comunicação e Participe em `https://clcmo.github.io/cefisma/` a cada push para `main`. Para ativar a publicação, configure **Settings > Pages > Build and deployment > GitHub Actions** no repositório. O WordPress continua sendo necessário para renderizar as páginas dinâmicas; a prévia usa conteúdo ilustrativo e não contém dados reais da loja ou da agenda.

## Instalação

1. Compacte a pasta `cefisma/` em `.zip` (ou use o arquivo entregue) e envie em Aparência > Temas > Adicionar.
2. Ative o tema. Ele configura os posts como `/%category%/%postname%/`, mantém páginas em `/%postname%/` e atualiza as regras de URL.
3. Em Aparência > Menus, crie o menu e atribua ao local "Menu principal" (sem menu, o tema usa os itens padrão de `Models/Navegacao.php`). Os itens chamados **Quem somos**, **Comunicação** e **Participe** apontam às páginas próprias; o tema também corrige os destinos desses itens no menu do WordPress.
4. Instale e ative o WooCommerce. Crie a categoria de produtos com slug `supernova` para o bloco "Apoie o Supernova".
5. Ao ativar o tema ou abrir o painel como administrador, as páginas **Quem somos**, **Supernova**, **Comunicação** e **Participe** são criadas automaticamente caso não existam e recebem seus modelos. A página Supernova apresenta separadamente o Boletim e o Folhetim, com as três edições mais recentes de cada série. Páginas existentes não são sobrescritas; se já tiverem um modelo personalizado, ele é preservado.
6. A página **Comunicação** lista automaticamente as publicações da categoria `informes`, com paginação.

Na página **Comunicação**, publique posts normalmente e atribua a categoria com slug `informes` para que apareçam na lista. Outros posts não entram nesse arquivo.

**Atenção:** o arquivo das edições usa o endereço `/supernova/boletim/`. Se já existir uma página com esse endereço, altere o slug dela para evitar conflito de rotas.

## Edições históricas do Supernova

O arquivo `data/supernova-edicoes.json` cataloga as 18 edições do Boletim e as 3 do Folhetim listadas nos arquivos públicos do CEFISMA. Cada edição publicada no novo site mantém o link para a página original; assim, PDFs e textos continuam sendo servidos pela fonte oficial.

Depois de instalar/atualizar o tema e ativá-lo no WordPress, importe ou atualize os registros com WP-CLI, na raiz da instalação WordPress:

```bash
wp eval-file wp-content/themes/cefisma/scripts/import-supernova-edicoes.php
```

O importador é idempotente por série e número, publica as edições e atualiza as rotas permanentes. As datas disponíveis nos arquivos são mensais ou semanais, sem dia exato; o catálogo preserva esse rótulo e usa o primeiro dia do mês como data interna do registro. As novas páginas ficam em `/supernova/edicoes/`; os endereços de origem continuam disponíveis para consulta. Confirme o caminho `wp-content/themes/cefisma` se o tema estiver instalado com outro nome.

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
- **Agenda:** em Agenda > Adicionar evento, informe título, data, local e link opcional. Eventos publicados aparecem na grade mensal da página inicial; use as setas para navegar entre os meses.
- **Contato e redes sociais:** o rodapé exibe o site, e-mail e os perfis de Instagram, X, TikTok, Telegram e Facebook.
- **Supernova (Edições):** campos de número, PDF, capa e sumário (uma seção por linha: `Título | URL`).

## Como estender

- Novo campo em um tipo de conteúdo: edite `campos()` no Model; o admin e a API passam a aceitá-lo.
- Nova página com dados próprios: crie `Controllers/NomeController.php`, uma View em `Views/` e um template fino na raiz.

## Notas

- As fontes vêm do Google Fonts. Para evitar o envio de dados a terceiros (LGPD), baixe-as para `assets/fonts/` e troque o `wp_enqueue_style` em `Core/Theme.php`.
- A contagem do carrinho no topo é a do carregamento da página; para atualizar sem recarregar, use os *cart fragments* do WooCommerce.
- Os textos de exemplo (Participe, canais, transparência) usam os endereços atuais do site e podem ser ajustados nos Models.
