#!/bin/sh
# Busca os 3 últimos vídeos do canal da PIB Pirajuí no YouTube via RSS/Atom público
CHANNEL_ID="UC3eJ1kGyTXwwpJnaNE8ICbQ"
FEED_URL="https://www.youtube.com/feeds/videos.xml?channel_id=${CHANNEL_ID}"
OUTPUT_DIR="/usr/share/nginx/html/assets/data"
OUTPUT_FILE="${OUTPUT_DIR}/ultimos-videos.json"

mkdir -p "$OUTPUT_DIR"

XML_CONTENT=$(curl -sL --max-time 20 "$FEED_URL")
if [ -z "$XML_CONTENT" ]; then
    echo "[$(date -Iseconds)] Erro ao buscar feed do YouTube" >&2
    exit 1
fi

UPDATED_AT=$(date -Iseconds)

echo "$XML_CONTENT" | awk -v updated_at="$UPDATED_AT" -v channel_id="$CHANNEL_ID" '
BEGIN {
    count = 0
    in_entry = 0
    print "{"
    print "  \"channelId\": \"" channel_id "\","
    print "  \"updatedAt\": \"" updated_at "\","
    print "  \"videos\": ["
}
/<entry>/ {
    in_entry = 1
    vid = ""
    title = ""
    pub = ""
}
in_entry && /<yt:videoId>/ {
    line = $0
    sub(/.*<yt:videoId>/, "", line)
    sub(/<\/yt:videoId>.*/, "", line)
    vid = line
}
in_entry && /<title>/ && title == "" {
    line = $0
    sub(/.*<title>/, "", line)
    sub(/<\/title>.*/, "", line)
    gsub(/&amp;/, "\\&", line)
    gsub(/&quot;/, "\\\"", line)
    gsub(/&#39;/, "'"'"'", line)
    gsub(/&lt;/, "<", line)
    gsub(/&gt;/, ">", line)
    gsub(/\\/, "\\\\", line)
    gsub(/"/, "\\\"", line)
    title = line
}
in_entry && /<published>/ && pub == "" {
    line = $0
    sub(/.*<published>/, "", line)
    sub(/<\/published>.*/, "", line)
    pub = line
}
/<\/entry>/ {
    if (in_entry && vid != "" && count < 3) {
        count++
        comma = (count < 3) ? "," : ""
        print "    {"
        print "      \"videoId\": \"" vid "\","
        print "      \"title\": \"" title "\","
        print "      \"published\": \"" pub "\","
        print "      \"url\": \"https://www.youtube.com/watch?v=" vid "\","
        print "      \"embedUrl\": \"https://www.youtube.com/embed/" vid "\""
        print "    }" comma
    }
    in_entry = 0
}
END {
    print "  ]"
    print "}"
}
' > "${OUTPUT_FILE}.tmp"

if [ -s "${OUTPUT_FILE}.tmp" ] && grep -q '"videoId"' "${OUTPUT_FILE}.tmp"; then
    mv "${OUTPUT_FILE}.tmp" "$OUTPUT_FILE"
    echo "[$(date -Iseconds)] ultimos-videos.json atualizado com sucesso."
else
    rm -f "${OUTPUT_FILE}.tmp"
    echo "[$(date -Iseconds)] Falha ao processar XML do YouTube." >&2
    exit 1
fi
