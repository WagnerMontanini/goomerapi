<?php

/**
 * ###############
 * ###   URL   ###
 * ###############
 */

/**
 * @param string $path
 * @return string
 */
function url(string $path = null): string
{
    if ($_SERVER['HTTP_HOST'] === "goomerapi.test") {
        if ($path) {
            return CONF_URL_TEST . "/" . CONF_VERSAO_TEST . "/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
        }
        return CONF_URL_TEST . "/" . CONF_VERSAO_TEST;
    }

    if ($path) {
        return CONF_URL_BASE . "/" . CONF_VERSAO_BASE . "/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
    }

    return CONF_URL_BASE . "/" . CONF_VERSAO_BASE;
}

/**
 * ##################
 * ###   ASSETS   ###
 * ##################
 */

/**
 * @param string $image
 * @param int $width
 * @param int|null $height
 * @return string
 */
function image(?string $image, int $width, int $height = null): ?string
{
    if ($image) {
        return url() . "/" . (new WagnerMontanini\GoomerApi\Support\Thumb())->make($image, $width, $height);
    }

    return null;
}
