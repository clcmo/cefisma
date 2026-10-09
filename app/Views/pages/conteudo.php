<?php /** @var array $item título, data, tags, conteudo, imagem, eh_post */ ?>
<article class="secao">
  <div class="wrap">
    <header class="conteudo">
      <?php foreach ($item['tags'] as $tag) : ?>
        <span class="tag tag-<?php echo esc_attr($tag['slug']); ?>"><?php echo esc_html($tag['nome']); ?></span>
      <?php endforeach; ?>
      <?php if ($item['eh_post']) : ?>
        <time class="meta" datetime="<?php echo esc_attr($item['iso']); ?>"><?php echo esc_html($item['data']); ?></time>
      <?php endif; ?>
      <h1><?php echo esc_html($item['titulo']); ?></h1>
    </header>
    <div class="conteudo">
      <?php echo wp_kses_post($item['imagem']); ?>
      <?php echo $item['conteudo']; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content já filtrado ?>
    </div>
  </div>
</article>
