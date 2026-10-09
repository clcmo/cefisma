<?php
declare(strict_types=1);

namespace Cefisma\Core;

/**
 * Tipos de campo dos Models (meta fields): sanitização e conversão
 * de/para o texto exibido no admin. Usado pelo MetaBoxController e pela API.
 */
final class Campos
{
    /** @param mixed $valor */
    public static function sanitizar(string $tipo, $valor)
    {
        switch ($tipo) {
            case 'number':
                return absint($valor);
            case 'url':
                return esc_url_raw((string) $valor);
            case 'date':
                $v = sanitize_text_field((string) $valor);
                return preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? $v : '';
            case 'linhas': // lista de {titulo, url}
                return self::linhas($valor);
            default:
                return sanitize_text_field((string) $valor);
        }
    }

    /** @param mixed $valor texto "Título | URL" (uma por linha) ou array de itens */
    private static function linhas($valor): array
    {
        $itens = [];
        if (is_string($valor)) {
            foreach (preg_split('/\R/', $valor) ?: [] as $linha) {
                [$titulo, $url] = array_map('trim', array_pad(explode('|', $linha, 2), 2, ''));
                $itens[] = ['titulo' => $titulo, 'url' => $url];
            }
        } elseif (is_array($valor)) {
            $itens = $valor;
        }
        $limpos = [];
        foreach ($itens as $i) {
            $titulo = sanitize_text_field((string) ($i['titulo'] ?? ''));
            if ($titulo !== '') {
                $limpos[] = ['titulo' => $titulo, 'url' => esc_url_raw((string) ($i['url'] ?? ''))];
            }
        }
        return $limpos;
    }

    /** @param mixed $valor */
    public static function paraTexto(string $tipo, $valor): string
    {
        if ($tipo === 'linhas' && is_array($valor)) {
            return implode("\n", array_map(
                static fn($i) => trim(($i['titulo'] ?? '') . ' | ' . ($i['url'] ?? ''), ' |'),
                $valor
            ));
        }
        return is_scalar($valor) ? (string) $valor : '';
    }
}
