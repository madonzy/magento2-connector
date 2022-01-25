<?php
declare(strict_types=1);

namespace Yeremenko\Connector\Service;

class UriService
{
    /**
     * Generate URI
     *
     * Ex: to generate URI like: https://site.com/notifications/102030/read?notificationIds=310071
     *
     * 1) Set contract url: https://site.com/notifications/:customerNumber/read
     * 2) Pass params as: [
     *      ':customerNumber' => 102030, (must start with ':')
     *      'notificationIds' => 310071
     * ]
     *
     * @param string $endpoint
     * @param array $params
     *
     * @return string
     */
    public function getUri(string $endpoint, array $params = []): string
    {
        $endpoint = rtrim($endpoint, '/');

        if (!$params) {
            return $endpoint;
        }

        $urlParams = array_filter($params, static function ($key) {
            return is_string($key) && $key[0] === ':';
        }, ARRAY_FILTER_USE_KEY);

        $urlParams = array_map('rawurlencode', $urlParams);

        $endpoint = str_replace(array_keys($urlParams), array_values($urlParams), $endpoint);

        if ($query = http_build_query(array_diff_key($params, $urlParams))) {
            $endpoint .= "?$query";
        }

        return $endpoint;
    }
}
