# Script PowerShell para buscar os 3 últimos vídeos do canal da PIB Pirajuí no YouTube
$ErrorActionPreference = "Stop"
$ChannelId = "UC3eJ1kGyTXwwpJnaNE8ICbQ"
$FeedUrl = "https://www.youtube.com/feeds/videos.xml?channel_id=$ChannelId"
$ProjectRoot = Split-Path -Parent $PSScriptRoot
$OutputDir = Join-Path $ProjectRoot "assets\data"
$OutputFile = Join-Path $OutputDir "ultimos-videos.json"

if (!(Test-Path $OutputDir)) {
    New-Item -ItemType Directory -Force -Path $OutputDir | Out-Null
}

$rawXml = (curl.exe -sL $FeedUrl) -join "`n"
[xml]$xml = $rawXml

$videos = @()
foreach ($entry in ($xml.feed.entry | Select-Object -First 3)) {
    $vid = $entry.videoId
    $videos += [ordered]@{
        videoId   = $vid
        title     = $entry.title
        published = $entry.published
        url       = "https://www.youtube.com/watch?v=$vid"
        embedUrl  = "https://www.youtube.com/embed/$vid"
    }
}

$payload = [ordered]@{
    channelId = $ChannelId
    updatedAt = (Get-Date).ToString("yyyy-MM-ddTHH:mm:sszzz")
    videos    = $videos
}

$json = $payload | ConvertTo-Json -Depth 4
[System.IO.File]::WriteAllText($OutputFile, $json, [System.Text.UTF8Encoding]::new($false))
Write-Output "Atualizado com sucesso: $OutputFile"
