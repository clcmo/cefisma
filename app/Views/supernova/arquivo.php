<?php
/**
 * Arquivo do Boletim Supernova.
 *
 * @var array|null $destaque
 * @var array      $edicoes
 * @var array      $mediacast
 * @var array      $apoie
 * @var string     $url_loja
 * @var string     $url_envio
 */
?>
<div class="wrap cabecalho-pagina">
  <h1>Supernova</h1>
  <p class="lede">Boletim e folhetim dos estudantes do Instituto de Física da USP.</p>
  <div class="abas">
    <span class="chip" aria-current="page">Boletim</span>
    <a class="chip" href="<?php echo esc_url(home_url('/supernova/folhetim/')); ?>">Folhetim</a>
  </div>
</div>

<?php if ($destaque) : ?>
<section class="secao" aria-label="Edição mais recente" style="padding-top:1.5rem">
  <div class="wrap">
    <?php cefisma_view('components/edicao-destaque', ['edicao' => $destaque, 'titulo_tag' => 'h2', 'link_titulo' => true]); ?>
  </div>
</section>

<section class="secao banda-escura" aria-labelledby="h-arquivo">
  <div class="wrap">
    <h2 id="h-arquivo">Todas as edições</h2>
    <ul class="capas">
      <?php foreach ($edicoes as $e) : ?>
        <li class="capa">
          <a href="<?php echo esc_url($e['url']); ?>">
            <?php cefisma_view('components/capa', ['edicao' => $e]); ?>
            <span><?php echo esc_html($e['titulo']); ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
<?php else : ?>
<section class="secao"><div class="wrap"><p>As edições aparecerão aqui assim que forem publicadas.</p></div></section>
<?php endif; ?>

<?php if ($mediacast) : ?>
<section class="secao banda-lavanda" aria-labelledby="h-mediacast">
  <div class="wrap">
    <div class="cab-secao">
      <h2 id="h-mediacast">Curadoria Mediacast</h2>
    </div>
    <ul class="pubs">
      <?php foreach ($mediacast as $item) : cefisma_view('components/publicacao', ['item' => $item]); endforeach; ?>
    </ul>
  </div>
</section>
<?php endif; ?>

<?php cefisma_view('supernova/envio', ['url_envio' => $url_envio]); ?>
<?php cefisma_view('loja/apoie', ['apoie' => $apoie, 'url_loja' => $url_loja]); ?>
