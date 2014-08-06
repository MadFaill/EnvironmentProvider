EnvironmentProvider
===================

Автоопределение среды и подгрузка необходимого конфига.

Из коробки:

- Определение Env
- Конфиг-object
- Env-object

Код распространяется по лицензии [MIT](http://opensource.org/licenses/MIT) и предоставляется **AS-IS**.

Пример конфига для определения среды
====================================

```ini

    ; /env/mapper.ini
    [settings]
    fallback = default
    config_path = _PROVIDER_INI_FILE_PATH_"/config"

    [default]
    scan[] = 'production.public-domain'

    [mad-environ]
    ; scan IP ADDRESS
    scan[] = "::1"
    ; scan IP ADDRESS
    scan[] = "127.0.0.1"
    ; scan CONSOLE USER
    scan[] = "MadFaill"
    ; scan HTTP-DOMAIN
    scan[] = "madfaill.local-domain"

```

```ini
    ; mad-env.ini
    [group-1]
    option[g1] = mad-env
```

```php
$cfg = __DIR__.'/env/mapper.ini';

$provider = \EnvironmentProvider\Provider::initWithINIFile($cfg);
$config = $provider->Config();

var_dump($config->get());
var_dump($config->get('group-1'));
var_dump($config->get('group-1', 'option'));
var_dump($config->get('group-1', 'option', 'g1'));
```

Так же можно посмотреть примеры в `examples`

Установка
=========

```json
{
    "require": {
        "mad-tools/environment-provider": "dev-master"
    }
}
```
