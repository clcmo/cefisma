<?php
/**
 * @var array $menu     itens padrão (usados se não houver menu atribuído)
 * @var array $carrinho [ativo, qtd, url]
 */
?>
<header class="topo">
  <div class="wrap">
    <a class="marca" href="<?php echo esc_url(home_url('/')); ?>" aria-label="cefisma, página inicial">
      <span class="marca-texto">ce<span class="f-funcao">f</span>isma</span>
    </a>

    <nav class="nav-principal" aria-label="Principal">
      <?php if (has_nav_menu('principal')): ?>
        <?php wp_nav_menu([
          'theme_location' => 'principal',
          'container' => false,
          'menu_id' => 'menu',
          'menu_class' => 'menu',
          'fallback_cb' => false,
          'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        ]); ?>
      <?php else: ?>
        <ul id="menu" class="menu">
          <?php foreach ($menu as $item): ?>
            <li<?php echo $item['classe'] !== '' ? ' class="' . esc_attr($item['classe']) . '"' : ''; ?>>
              <a href="<?php echo esc_url($item['url']); ?>" <?php echo $item['atual'] ? ' aria-current="page"' : ''; ?>><?php echo esc_html($item['rotulo']); ?></a>
              </li>
            <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </nav>

    <div class="topo-acoes">
      <?php if ($carrinho['ativo']): ?>
        <a class="btn-carrinho" href="<?php echo esc_url($carrinho['url']); ?>"
          aria-label="<?php echo esc_attr(sprintf('Carrinho, %d %s', $carrinho['qtd'], $carrinho['qtd'] === 1 ? 'item' : 'itens')); ?>">
          Carrinho <span class="qtd"><?php echo (int) $carrinho['qtd']; ?></span>
        </a>
      <?php endif; ?>
      <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="menu">Menu</button>
    </div>
  </div>
</header>