#!/usr/bin/env bash
set -euo pipefail

WP_PATH="${WP_PATH:-/app/wordpress}"
SCREENSHOT_DIR="${1:-/app/wordpress/wp-content/uploads/instruction-screenshots}"

if [ ! -d "${SCREENSHOT_DIR}" ]; then
    echo "Screenshot directory not found: ${SCREENSHOT_DIR}"
    echo "Run 'npm run screenshots' first to capture screenshots."
    exit 1
fi

png_count=$(find "${SCREENSHOT_DIR}" -name "*.png" -type f | wc -l)

if [ "${png_count}" -eq 0 ]; then
    echo "No PNG files found in ${SCREENSHOT_DIR}"
    exit 1
fi

echo "Found ${png_count} screenshots to import."

imported=0

for screenshot in "${SCREENSHOT_DIR}"/*.png; do
    filename=$(basename "${screenshot}" .png)

    if [[ "${filename}" =~ ^(.+)-(en|fi)$ ]]; then
        screenshot_id="${BASH_REMATCH[1]}"
        language="${BASH_REMATCH[2]}"
    else
        screenshot_id="${filename}"
        language="en"
    fi

    echo "Importing: ${screenshot_id} (${language})"

    attachment_id=$(wp media import "${screenshot}" \
        --path="${WP_PATH}" \
        --title="Screenshot: ${screenshot_id}" \
        --alt="WordPress admin screenshot: ${screenshot_id}" \
        --porcelain 2>/dev/null || echo "")

    if [ -z "${attachment_id}" ]; then
        echo "  WARNING: Failed to import ${screenshot}"
        continue
    fi

    image_url=$(wp post get "${attachment_id}" --field=guid --path="${WP_PATH}" 2>/dev/null || echo "")

    if [ -z "${image_url}" ]; then
        echo "  WARNING: Failed to get URL for attachment ${attachment_id}"
        continue
    fi

    echo "  Imported as attachment ${attachment_id}: ${image_url}"
    imported=$((imported + 1))

    echo "${screenshot_id}|${image_url}|${language}" >> "${SCREENSHOT_DIR}/.import-manifest"
done

echo ""
echo "Import complete: ${imported} screenshots imported."
echo "Run 'wp eval-file /app/tools/link-screenshots.php' to link screenshots to tutorials."
