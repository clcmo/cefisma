<?php
/**
 * Página de entrada para o Boletim e o Folhetim Supernova.
 *
 * @var array $boletins
 * @var array $folhetins
 * @var string $url_boletim
 * @var string $url_folhetim
 */
?>
<section class="secao supernova-inicio">
  <div class="wrap">
    <header class="cabecalho-pagina">
      <span class="tag tag-supernova">Publicações estudantis</span>
      <h1>Supernova</h1>
      <p class="lede">Textos, artes, notícias e informações produzidos pela comunidade do Instituto de Física da USP.</p>
    </header>

    <div class="supernova-series">
      <article class="supernova-serie">
        <div class="supernova-serie-cabecalho">
          <span class="tag tag-supernova">Publicação mensal</span>
          <h2>Boletim Supernova</h2>
          <p>Um boletim da comunidade IFUSPiana com textos, coletânea de artes, repasses, problemas, avisos e indicações culturais.</p>
          <a class="btn btn-primario" href="<?php echo esc_url($url_boletim); ?>">Ver todas as edições do Boletim</a>
        </div>
        <?php if ($boletins) : ?>
          <ul class="supernova-edicoes" aria-label="Edições recentes do Boletim">
            <?php foreach ($boletins as $edicao) : ?>
              <li>
                <a href="<?php echo esc_url($edicao['url']); ?>">
                  <span class="supernova-capa"><?php cefisma_view('components/capa', ['edicao' => $edicao]); ?></span>
                  <span><strong><?php echo esc_html($edicao['titulo']); ?></strong><small><?php echo esc_html($edicao['data']); ?></small></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else : ?>
          <p class="suave">As edições do Boletim aparecerão aqui assim que forem publicadas.</p>
        <?php endif; ?>
      </article>

      <article class="supernova-serie supernova-serie-folhetim">
        <div class="supernova-serie-cabecalho">
          <span class="tag">Informes especiais</span>
          <h2>Folhetim Supernova</h2>
          <p>Publicação de formato conciso para centralizar informações e comunicados importantes à comunidade do IFUSP.</p>
          <a class="btn btn-linha" href="<?php echo esc_url($url_folhetim); ?>">Ver todas as edições do Folhetim</a>
        </div>
        <?php if ($folhetins) : ?>
          <ul class="supernova-edicoes" aria-label="Edições recentes do Folhetim">
            <?php foreach ($folhetins as $edicao) : ?>
              <li>
                <a href="<?php echo esc_url($edicao['url']); ?>">
                  <span class="supernova-capa"><?php cefisma_view('components/capa', ['edicao' => $edicao]); ?></span>
                  <span><strong><?php echo esc_html($edicao['titulo']); ?></strong><small><?php echo esc_html($edicao['data']); ?></small></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else : ?>
          <p class="suave">As edições do Folhetim aparecerão aqui assim que forem publicadas.</p>
        <?php endif; ?>
      </article>
    </div>
  </div>
</section>
