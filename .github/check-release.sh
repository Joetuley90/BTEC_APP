#!/usr/bin/env bash
# Checks a Crafticulum release before the live site is told to fetch it. Run by .github/workflows/deploy.yml.
# Nothing here needs a password or a key. It fails (and stops the deploy) if anything below isn't right.
set -euo pipefail
cd "$(dirname "$0")/.."
R=crafticulum; fail(){ echo "::error::$*"; exit 1; }
echo "1. No settings file in the release"
[ ! -e "$R/config.php" ] || fail "config.php is in the crafticulum folder"
unzip -l crafticulum-release.zip | grep -qE ' config\.php$' && fail "config.php is in the zip" || true
echo "2. The zip and the folder hold the same files"
diff <(unzip -Z1 crafticulum-release.zip | grep -v '/$' | sort) <(cd "$R" && find . -type f | sed 's#^\./##' | sort) || fail "the zip and the folder don't match"
echo "3. Every PHP file is valid, and everything it needs is there"
for f in $(find "$R" -name '*.php'); do php -l "$f" >/dev/null || fail "$f has a PHP error"; done
missing=""; for f in "$R"/*.php; do for need in $( { grep -oE "require(_once)?[ (]*__DIR__ *\. *'/[^']+'" "$f" || true; } | sed -E "s/.*'\/([^']+)'/\1/"); do [ "$need" = config.php ] || [ -e "$R/$need" ] || missing="$missing $(basename "$f") needs $need;"; done; done
[ -z "$missing" ] || fail "missing files:$missing"
echo "4. Every script is valid JavaScript"
for f in $(find "$R" -name '*.js' -not -path '*/assets/*'); do node --check "$f" 2>/dev/null || node -e "new Function(require('fs').readFileSync('$f','utf8'))" || fail "$f has a JavaScript error"; done
echo "5. The app page points at its code, and the code is there"
src=$( { grep -oE 'src="app\.js\?v=[0-9a-f]+"' "$R/app.html" || true; } | head -1 | sed -E 's/src="([^?]+).*/\1/')
[ -n "$src" ] && [ -s "$R/$src" ] || fail "app.html doesn't point at app.js, or app.js is missing"
grep -q "app.js?v=" "$R/sw.js" || fail "sw.js doesn't keep app.js for offline use"
echo "6. The version file is right"
php -r '$j=json_decode(file_get_contents("crafticulum/version.json"),true); if(!is_array($j)||!is_int($j["v"]??null)||empty($j["date"])) exit(1);' || fail "version.json isn't right"
echo "7. No real college named"
! grep -rliE 'mid ?kent|maidstone|medway' "$R" --include=*.html --include=*.js --include=*.php --include=*.txt --include=*.json || fail "a real college is named in the release"
echo "8. The site starts and answers"
tmp=$(mktemp -d); cp -r "$R"/. "$tmp/"; cp "$tmp/config.example.php" "$tmp/config.php"; mkdir -p "$tmp/data"
php -S 127.0.0.1:8123 -t "$tmp" >/tmp/php.log 2>&1 </dev/null & PHPPID=$!; trap 'kill $PHPPID 2>/dev/null || true' EXIT; sleep 1.5
for u in "" "app.html" "terms.html" "accessibility.html" "$src"; do c=$(curl -s -o /dev/null -w '%{http_code}' "http://127.0.0.1:8123/$u"); [ "$c" = 200 ] || fail "/$u answered $c"; done
h=$(curl -s "http://127.0.0.1:8123/auth.php?a=health"); echo "$h" | php -r 'exit(is_array(json_decode(stream_get_contents(STDIN),true))?0:1);' || fail "the health check didn't answer: $h"
echo "All checks passed."
