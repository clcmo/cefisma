<?php
/**
 * Importa/atualiza o catálogo no WordPress: wp eval-file scripts/import-supernova-edicoes.php
 */
if (!class_exists('WP_CLI')) {
    fwrite(STDERR, "Execute este importador com WP-CLI via wp eval-file.\n");
    exit(1);
}

$catalogPath = dirname(__DIR__) . '/data/supernova-edicoes.json';
$json = file_get_contents($catalogPath);
if ($json === false) {
    WP_CLI::error('Não foi possível ler data/supernova-edicoes.json.');
}

$edicoes = json_decode($json, true);
if (!is_array($edicoes)) {
    WP_CLI::error('O catálogo JSON não é válido.');
}

if (!class_exists(\Cefisma\Models\Edicao::class)) {
    $themeDir = get_template_directory();
    require_once $themeDir . '/app/Core/Autoloader.php';
    \Cefisma\Core\Autoloader::register('Cefisma\\', $themeDir . '/app/');
}

$criados = 0;
$atualizados = 0;
foreach ($edicoes as $edicao) {
    if (!isset($edicao['serie'], $edicao['numero'], $edicao['titulo'], $edicao['data'], $edicao['slug'], $edicao['origem_url'])) {
        WP_CLI::error('O catálogo contém uma edição com campos obrigatórios ausentes.');
    }

    $idExistente = get_posts([
        'post_type'      => \Cefisma\Models\Edicao::TIPO,
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => 'numero',
        'meta_value'     => absint($edicao['numero']),
        'meta_query'     => sanitize_key($edicao['serie']) === 'folhetim'
            ? [['key' => 'serie', 'value' => 'folhetim']]
            : [
                'relation' => 'OR',
                ['key' => 'serie', 'value' => 'boletim'],
                ['key' => 'serie', 'compare' => 'NOT EXISTS'],
            ],
    ]);

    $resultado = \Cefisma\Models\Edicao::salvarPorNumero($edicao);
    if (is_wp_error($resultado)) {
        WP_CLI::error($resultado->get_error_message());
    }

    if ($idExistente) {
        $atualizados++;
    } else {
        $criados++;
    }
}

flush_rewrite_rules();
WP_CLI::success(sprintf(
    'Catálogo Supernova importado: %d novas edições e %d atualizadas (%d no total).',
    $criados,
    $atualizados,
    count($edicoes)
));
