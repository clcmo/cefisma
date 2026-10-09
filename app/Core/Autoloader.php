<?php
declare(strict_types=1);

namespace Cefisma\Core;

/** Autoloader PSR-4 mínimo: Cefisma\Models\Edicao => app/Models/Edicao.php */
final class Autoloader
{
    public static function register(string $prefix, string $baseDir): void
    {
        spl_autoload_register(static function (string $class) use ($prefix, $baseDir): void {
            if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
                return;
            }
            $arquivo = $baseDir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (is_readable($arquivo)) {
                require $arquivo;
            }
        });
    }
}
