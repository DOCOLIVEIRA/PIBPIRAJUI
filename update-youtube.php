<?php
/**
 * Atualiza e retorna os 3 últimos vídeos do canal da PIB Pirajuí no YouTube.
 * Funciona automaticamente na HostGator (cPanel/PHP) a partir das 06h00 (horário de Brasília)
 * ou via Tarefa Cron no cPanel: 0 6 * * * /usr/local/bin/php /home/USUARIO/public_html/update-youtube.php
 */
date_default_timezone_set('America/Sao_Paulo');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$channelId = 'UC3eJ1kGyTXwwpJnaNE8ICbQ';
$feedUrl   = "https://www.youtube.com/feeds/videos.xml?channel_id={$channelId}";
$dataDir   = __DIR__ . '/assets/data';
$jsonFile  = $dataDir . '/ultimos-videos.json';

if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0755, true);
}

$forceUpdate = isset($_GET['force']) || php_sapi_name() === 'cli';
$needsUpdate = $forceUpdate || !file_exists($jsonFile);

if (!$needsUpdate && file_exists($jsonFile)) {
    $lastModified = filemtime($jsonFile);
    $todaySixAm   = strtotime('today 06:00:00');
    $now          = time();

    if ($now >= $todaySixAm && $lastModified < $todaySixAm) {
        $needsUpdate = true;
    } elseif (($now - $lastModified) > 86400) {
        $needsUpdate = true;
    }
}

if ($needsUpdate) {
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 15,
            'user_agent' => 'Mozilla/5.0 (compatible; PIBPirajuiBot/1.0)'
        ]
    ]);

    $xmlContent = @file_get_contents($feedUrl, false, $ctx);
    if (!$xmlContent && function_exists('curl_init')) {
        $ch = curl_init($feedUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; PIBPirajuiBot/1.0)'
        ]);
        $xmlContent = curl_exec($ch);
        curl_close($ch);
    }

    if ($xmlContent) {
        $xml = @simplexml_load_string($xmlContent);
        if ($xml && isset($xml->entry)) {
            $videos = [];
            $nsYt   = $xml->getNamespaces(true)['yt'] ?? 'http://www.youtube.com/xml/schemas/2015';

            foreach ($xml->entry as $entry) {
                $yt      = $entry->children($nsYt);
                $videoId = (string)($yt->videoId ?? '');
                if (!$videoId) continue;

                $videos[] = [
                    'videoId'   => $videoId,
                    'title'     => (string)$entry->title,
                    'published' => (string)$entry->published,
                    'url'       => "https://www.youtube.com/watch?v={$videoId}",
                    'embedUrl'  => "https://www.youtube.com/embed/{$videoId}"
                ];

                if (count($videos) >= 3) break;
            }

            if (!empty($videos)) {
                $payload = [
                    'channelId' => $channelId,
                    'updatedAt' => date('c'),
                    'videos'    => $videos
                ];
                @file_put_contents(
                    $jsonFile,
                    json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                );
            }
        }
    }
}

if (file_exists($jsonFile)) {
    readfile($jsonFile);
} else {
    http_response_code(503);
    echo json_encode(['error' => 'Dados indisponíveis no momento.']);
}
