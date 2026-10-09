<?php /** @var array $item dados vindos de Models\Publicacao */ ?>
<li>
  <?php foreach ($item['tags'] as $tag) : ?>
    <span class="tag tag-<?php echo esc_attr($tag['slug']); ?>"><?php echo esc_html($tag['nome']); ?></span>
  <?php endforeach; ?>
  <time class="meta" datetime="<?php echo esc_attr($item['iso']); ?>"><?php echo esc_html($item['data']); ?></time>
  <h3><a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['titulo']); ?></a></h3>
  <?php if ($item['resumo'] !== '') : ?><p><?php echo esc_html($item['resumo']); ?></p><?php endif; ?>
</li>
