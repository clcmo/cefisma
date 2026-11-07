<?php
/**
 * Página inicial.
 *
 * @var array|null $edicao      edição mais recente
 * @var array      $edicoes
 * @var array      $eventos
 * @var array      $calendario
 * @var array      $publicacoes
 * @var array      $produtos
 * @var array      $participe
 * @var string     $url_loja
 * @var string     $url_arquivo
 * @var string     $url_envio
 * @var string     $url_posts
 */
// Posição dos pontos (anel, ângulo) para até cinco seções do sumário.
$posicoes = [[1, 215], [2, 320], [3, 40], [4, 150], [2, 100]];
?>
<section class="hero" aria-labelledby="h-hero">
  <div class="wrap">
    <div>
      <h1 id="h-hero">Centro Acadêmico do Instituto de Física da USP</h1>
      <p class="lede">Um só lugar para o histórico de lutas, a administração, a transparência e a conversa entre a diretoria do CEFISMA e as e os estudantes do IFUSP.</p>
      <div class="acoes">
        <?php if ($edicao) : ?>
          <a class="btn btn-primario" href="<?php echo esc_url($edicao['url']); ?>">Ler o <?php echo esc_html($edicao['titulo']); ?></a>
        <?php endif; ?>
        <a class="btn btn-linha" href="#participe">Ver como participar</a>
      </div>
    </div>

    <div class="ondas">
      <svg class="aneis" viewBox="0 0 520 520" aria-hidden="true">
        <circle class="anel" style="--i:0" cx="260" cy="260" r="80" stroke="#F7B731" stroke-width="3"/>
        <circle class="anel" style="--i:1" cx="260" cy="260" r="125" stroke="#C8124D" stroke-width="4" stroke-dasharray="2 11" stroke-linecap="round"/>
        <circle class="anel" style="--i:2" cx="260" cy="260" r="170" stroke="#2A44D8" stroke-width="2"/>
        <circle class="anel" style="--i:3" cx="260" cy="260" r="215" stroke="#C8124D" stroke-width="3" stroke-dasharray="44 16"/>
        <circle class="anel" style="--i:4" cx="260" cy="260" r="250" stroke="#1C1845" stroke-opacity=".28" stroke-width="2"/>
      </svg>
      <?php if ($edicao) : ?>
        <a class="capa-centro" href="<?php echo esc_url($edicao['url']); ?>" aria-label="Abrir o <?php echo esc_attr($edicao['titulo']); ?>">
          <?php cefisma_view('components/capa', ['edicao' => $edicao]); ?>
        </a>
        <?php foreach (array_slice($edicao['sumario'], 0, 5) as $i => $secao) : ?>
          <a class="ponto" href="<?php echo esc_url($secao['url'] !== '' ? $secao['url'] : $edicao['url']); ?>"
             data-anel="<?php echo (int) $posicoes[$i][0]; ?>" data-ang="<?php echo (int) $posicoes[$i][1]; ?>"><span><?php echo esc_html($secao['titulo']); ?></span></a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="secao" aria-labelledby="h-agenda">
  <div class="wrap calendario" id="agenda">
    <div class="calendario-cabecalho">
      <h2 id="h-calendario">Calendário de eventos</h2>
      <nav class="calendario-navegacao" aria-label="Navegar pelos meses do calendário">
        <a href="<?php echo esc_url(add_query_arg('mes', $calendario['mes_anterior'], home_url('/')) . '#agenda'); ?>" aria-label="Mês anterior">‹</a>
        <h3><?php echo esc_html($calendario['titulo']); ?></h3>
        <a href="<?php echo esc_url(add_query_arg('mes', $calendario['mes_seguinte'], home_url('/')) . '#agenda'); ?>" aria-label="Próximo mês">›</a>
      </nav>
    </div>
    <table class="calendario-tabela" aria-labelledby="h-calendario">
      <thead>
        <tr>
          <?php foreach ($calendario['dias_semana'] as $diaSemana) : ?>
            <th scope="col"><?php echo esc_html($diaSemana); ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($calendario['semanas'] as $semana) : ?>
          <tr>
            <?php foreach ($semana as $dia) : ?>
              <td<?php echo $dia && $dia['hoje'] ? ' class="hoje"' : ''; ?>>
                <?php if ($dia) : ?>
                  <time datetime="<?php echo esc_attr($dia['data']); ?>"<?php echo $dia['hoje'] ? ' aria-current="date"' : ''; ?>><?php echo esc_html((string) $dia['dia']); ?></time>
                  <?php if ($dia['eventos']) : ?>
                    <ul>
                      <?php foreach ($dia['eventos'] as $evento) : ?>
                        <li>
                          <?php if ($evento['link'] !== '') : ?>
                            <a href="<?php echo esc_url($evento['link']); ?>"><?php echo esc_html($evento['titulo']); ?></a>
                          <?php else : ?>
                            <?php echo esc_html($evento['titulo']); ?>
                          <?php endif; ?>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="wrap duas">
    <div>
      <h2 id="h-agenda">Próximos eventos</h2>
      <?php if ($eventos) : ?>
        <ul class="agenda">
          <?php foreach ($eventos as $e) : ?>
            <li>
              <time datetime="<?php echo esc_attr($e['iso']); ?>"><b><?php echo esc_html($e['dia']); ?></b><?php echo esc_html($e['mes']); ?></time>
              <div>
                <strong><?php if ($e['link'] !== '') : ?><a href="<?php echo esc_url($e['link']); ?>"><?php echo esc_html($e['titulo']); ?></a><?php else : echo esc_html($e['titulo']); endif; ?></strong>
                <?php if ($e['local'] !== '') : ?><span class="meta"><?php echo esc_html($e['local']); ?></span><?php endif; ?>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else : ?>
        <p class="suave">Nenhum evento marcado por enquanto.</p>
      <?php endif; ?>
    </div>
    <div>
      <h2>Últimas publicações</h2>
      <ul class="pubs">
        <?php foreach ($publicacoes as $item) : cefisma_view('components/publicacao', ['item' => $item]); endforeach; ?>
      </ul>
      <a class="btn btn-linha btn-pequeno" href="<?php echo esc_url($url_posts); ?>">Ver todas as publicações</a>
    </div>
  </div>
</section>

<?php if ($edicoes) : ?>
<section class="secao banda-escura" aria-labelledby="h-super">
  <div class="wrap faixa-supernova">
    <div>
      <h2 id="h-super">Boletim Supernova</h2>
      <p>O boletim dos estudantes do IFUSP: notícias, ciência, arte e resenhas feitas por quem estuda aqui.</p>
      <div class="acoes">
        <a class="btn btn-sol" href="<?php echo esc_url($url_arquivo); ?>">Ver todas as edições</a>
        <a class="btn btn-linha" style="border-color:#fff;color:#fff" href="<?php echo esc_url($url_envio); ?>">Enviar texto ou arte</a>
      </div>
    </div>
    <ul class="capas" aria-label="Edições do Supernova">
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
<?php endif; ?>

<?php if ($produtos) : ?>
<section class="secao banda-lavanda vitrine-blusas" aria-labelledby="h-loja">
  <div class="wrap">
    <div class="cab-secao">
      <div class="vitrine-blusas-copy">
        <h2 id="h-loja">Camisetas do CEFISMA</h2>
        <p>Vista as cores do Centro Acadêmico e leve essa história com você.</p>
        <a class="btn btn-primario btn-pequeno" href="<?php echo esc_url($url_loja); ?>">Ver a loja completa</a>
      </div>
      <img class="vitrine-blusas-estampa" src="<?php echo esc_url(get_theme_file_uri('assets/images/estampa-cefisma.svg')); ?>" alt="" aria-hidden="true">
    </div>
    <ul class="grade-produtos">
      <?php foreach ($produtos as $produto) : cefisma_view('components/produto', ['produto' => $produto]); endforeach; ?>
    </ul>
  </div>
</section>
<?php endif; ?>

<section class="secao" id="participe" aria-labelledby="h-part">
  <div class="wrap">
    <h2 id="h-part">Participe</h2>
    <ul class="linhas">
      <?php foreach ($participe as $p) : ?>
        <li>
          <div><h3><?php echo esc_html($p['titulo']); ?></h3><p><?php echo esc_html($p['texto']); ?></p></div>
          <a class="btn btn-linha btn-pequeno" href="<?php echo esc_url($p['url']); ?>"><?php echo esc_html($p['botao']); ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
