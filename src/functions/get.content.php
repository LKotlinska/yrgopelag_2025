<?php

declare(strict_types=1);

function getContent(string $url, string $payload)
{
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    $protocols = [
        // Works on localhost
        CURL_HTTP_VERSION_1_1,
        CURL_HTTP_VERSION_2TLS,
        // Fallback (fix for deployment)
    ];

    foreach ($protocols as $version) {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_HTTP_VERSION   => $version,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYHOST => 3,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        file_put_contents(
            __DIR__ . '/payload-log.txt',
            date('c') . "\n" . $payload . "\n\n",
            FILE_APPEND
        );

        $data     = curl_exec($ch);
        $errno    = curl_errno($ch);
        $error    = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        unset($ch);

        // Log all non-200 responses INCLUDING BODY
        if ($data === false || $httpCode !== 200) {
            file_put_contents(
                __DIR__ . '/curl-error-log.txt',
                sprintf(
                    "[%s]\nProtocol: %s\nHTTP: %d\ncURL errno: %d\ncURL error: %s\nResponse body:\n%s\n\n",
                    date('c'),
                    $version === CURL_HTTP_VERSION_2TLS ? 'HTTP/2' : 'HTTP/1.1',
                    $httpCode,
                    $errno,
                    $error ?: '(none)',
                    $data !== false ? $data : '(no body)'
                ),
                FILE_APPEND
            );
            usleep(300_000);
        }

        // Success path
        if ($data !== false && $httpCode === 200) {
            return $data;
        }

        // IMPORTANT: do NOT retry on business errors
        if ($httpCode >= 400 || $httpCode < 500) {
            return $data !== false ? $data : 'An unknown error has occurred';
        }
    }

    return 'No HTTP protocols defined for your request';
}
