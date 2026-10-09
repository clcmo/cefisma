<?php
/**
 * Layout base. Variáveis: $conteudo (HTML da página) + dados globais do Controller.
 *
 * @var string $conteudo
 * @var array  $avisos
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="pular" href="#conteudo">Pular para o conteúdo</a>

<?php cefisma_view('partials/aviso', ['avisos' => $avisos]); ?>
<?php cefisma_view('partials/topo', ['menu' => $menu, 'carrinho' => $carrinho]); ?>

<main id="conteudo" tabindex="-1">
<?php echo $conteudo; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML das Views, já escapado ?>
</main>

<?php cefisma_view('partials/rodape', [
    'canais'        => $canais,
    'transparencia' => $transparencia,
    'contato'       => $contato,
    'whatsapp'      => $whatsapp,
]); ?>
<?php wp_footer(); ?>
</body>
</html>
