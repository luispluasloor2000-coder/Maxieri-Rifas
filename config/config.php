<?php
declare(strict_types=1);

function config_value(string $key, string $default = ''): string
{
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }

    return $value;
}

define('APP_NAME', config_value('APP_NAME', 'MAXIERI RIFAS'));
define('APP_SLOGAN', config_value('APP_SLOGAN', 'Cada número tiene una historia.'));
define('APP_URL', config_value('APP_URL'));
define('APP_ENV', config_value('APP_ENV', 'local'));
define('APP_DEBUG', filter_var(config_value('APP_DEBUG', APP_ENV === 'local' ? 'true' : 'false'), FILTER_VALIDATE_BOOLEAN));
define('APP_TIMEZONE', config_value('APP_TIMEZONE', 'America/Guayaquil'));

define('DB_HOST', config_value('DB_HOST', 'localhost'));
define('DB_PORT', config_value('DB_PORT', '3306'));
define('DB_NAME', config_value('DB_NAME', 'maxieri_rifas'));
define('DB_USER', config_value('DB_USER', 'maxieri_user'));
define('DB_PASS', config_value('DB_PASS', 'MaxieriRifas2026!'));
define('DB_CHARSET', config_value('DB_CHARSET', 'utf8mb4'));

date_default_timezone_set(APP_TIMEZONE);
