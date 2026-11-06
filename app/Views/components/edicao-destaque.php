<?php
/**
 * Capa grande + dados + sumário de uma edição.
 *
 * @var array  $edicao
 * @var string $titulo_tag  h1 (página da edição) ou h2 (arquivo)
 * @var bool   $link_titulo se verdadeiro, o botão principal leva à página da edição
 */
$tag = in_array($titulo_tag ?? 'h2', ['h1', 'h2'], true) ? $titulo_tag : 'h2';
?>
<div class="edicao-destaque">
  <div class="grande"><?php cefisma_view('components/capa', ['edicao' => $edicao]); ?></div>
  <div>
    <<?php echo $tag; // allowlist acima ?>><?php echo esc_html($edicao['titulo']); ?></<?php echo $tag; ?>>
    <p class="meta"><time datetime="<?php echo esc_attr($edicao['iso']); ?>"><?php echo esc_html($edicao['data']); ?></time></p>
    <?php if ($edicao['resumo'] !== '') : ?><p><?php echo esc_html($edicao['resumo']); ?></p><?php endif; ?>
    <div class="acoes">
      <?php if (!empty($link_titulo)) : ?>
        <a class="btn btn-primario" href="<?php echo esc_url($edicao['url']); ?>">Abrir a edição</a>
      <?php endif; ?>
      <?php if ($edicao['pdf'] !== '') : ?>
        <a class="btn <?php echo !empty($link_titulo) ? 'btn-linha' : 'btn-primario'; ?>" href="<?php echo esc_url($edicao['pdf']); ?>">Ler o PDF</a>
      <?php endif; ?>
      <?php if (!empty($edicao['origem'])) : ?>
        <a class="btn <?php echo !empty($link_titulo) || $edicao['pdf'] !== '' ? 'btn-linha' : 'btn-primario'; ?>" href="<?php echo esc_url($edicao['origem']); ?>">Ler edição original</a>
      <?php endif; ?>
    </div>
    <?php if ($edicao['sumario']) : ?>
      <h3 class="sumario-titulo">Nesta edição</h3>
      <ul class="sumario">
        <?php foreach ($edicao['sumario'] as $secao) : ?>
          <li>
            <?php if ($secao['url'] !== '') : ?>
              <a href="<?php echo esc_url($secao['url']); ?>"><?php echo esc_html($secao['titulo']); ?></a>
            <?php else : ?>
              <span><?php echo esc_html($secao['titulo']); ?></span>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>
