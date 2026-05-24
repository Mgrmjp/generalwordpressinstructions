#!/usr/bin/env bash
set -euo pipefail

WP_PATH="${WP_PATH:-/app/wordpress}"
SCREENSHOT_DIR="${1:-/app/wordpress/wp-content/uploads/instruction-screenshots}"
TABLE_PREFIX="$(wp db prefix --path="${WP_PATH}")"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# shellcheck source=lib/screenshot-log.sh
source "${SCRIPT_DIR}/lib/screenshot-log.sh"

gwi_screenshot_log_init "import" "${SCREENSHOT_DIR}"

if [ ! -d "${SCREENSHOT_DIR}" ]; then
    gwi_screenshot_log error "Screenshot directory not found" "{\"path\":\"${SCREENSHOT_DIR}\"}"
    gwi_screenshot_log_finish "failed"
    echo "Run 'npm run screenshots' first to capture screenshots."
    exit 1
fi

png_count=$(find "${SCREENSHOT_DIR}" -maxdepth 1 -name "*.png" -type f | wc -l)

if [ "${png_count}" -eq 0 ]; then
    gwi_screenshot_log error "No PNG files found" "{\"path\":\"${SCREENSHOT_DIR}\"}"
    gwi_screenshot_log_finish "failed"
    exit 1
fi

gwi_screenshot_log info "Import run started" "{\"pngCount\":${png_count},\"screenshotDir\":\"${SCREENSHOT_DIR}\",\"jsonlPath\":\"${GWI_LOG_JSONL}\"}"

manifest_file="${SCREENSHOT_DIR}/.import-manifest"
: > "${manifest_file}"

imported=0
updated=0
failed=0

find_existing_attachment_id() {
    local screenshot_key="$1"
    local screenshot_id="$2"
    local language="$3"
    local attachment_id=""

    attachment_id="$(wp post list \
        --post_type=attachment \
        --meta_key=_gwi_screenshot_key \
        --meta_value="${screenshot_key}" \
        --field=ID \
        --path="${WP_PATH}" 2>/dev/null | head -n 1 || true)"

    if [ -n "${attachment_id}" ]; then
        echo "${attachment_id}"
        return
    fi

    attachment_id="$(wp db query "
        SELECT p.ID
        FROM ${TABLE_PREFIX}posts p
        INNER JOIN ${TABLE_PREFIX}postmeta pm ON p.ID = pm.post_id
        WHERE p.post_type = 'attachment'
          AND pm.meta_key = '_wp_attached_file'
          AND (
            pm.meta_value LIKE '%/${screenshot_id}-${language}.png'
            OR pm.meta_value LIKE '%/${screenshot_id}-${language}-%'
          )
        ORDER BY p.ID DESC
        LIMIT 1
    " --skip-column-names --path="${WP_PATH}" 2>/dev/null | tr -d '[:space:]' || true)"

    echo "${attachment_id}"
}

delete_duplicate_attachments() {
    local screenshot_key="$1"
    local screenshot_id="$2"
    local language="$3"
    local keep_id="$4"
    local duplicate_ids=""

    duplicate_ids="$(wp db query "
        SELECT p.ID
        FROM ${TABLE_PREFIX}posts p
        INNER JOIN ${TABLE_PREFIX}postmeta pm ON p.ID = pm.post_id
        WHERE p.post_type = 'attachment'
          AND p.ID <> ${keep_id}
          AND (
            p.ID IN (
              SELECT post_id
              FROM ${TABLE_PREFIX}postmeta
              WHERE meta_key = '_gwi_screenshot_key'
                AND meta_value = '${screenshot_key}'
            )
            OR (
              pm.meta_key = '_wp_attached_file'
              AND (
                pm.meta_value LIKE '%/${screenshot_id}-${language}.png'
                OR pm.meta_value LIKE '%/${screenshot_id}-${language}-%'
              )
            )
          )
    " --skip-column-names --path="${WP_PATH}" 2>/dev/null | tr '\n' ' ' || true)"

    if [ -n "${duplicate_ids// /}" ]; then
        wp post delete ${duplicate_ids} --force --path="${WP_PATH}" >/dev/null
        gwi_screenshot_log info "Removed duplicate attachments" "{\"screenshotId\":\"${screenshot_id}\",\"language\":\"${language}\",\"removedAttachmentIds\":\"${duplicate_ids// /,}\"}"
    fi
}

for screenshot in "${SCREENSHOT_DIR}"/*.png; do
    [ -f "${screenshot}" ] || continue

    filename=$(basename "${screenshot}" .png)

    if [[ "${filename}" =~ ^(.+)-(en|fi)-context$ ]]; then
        screenshot_id="${BASH_REMATCH[1]}"
        language="${BASH_REMATCH[2]}"
        screenshot_key="${screenshot_id}-${language}-context"
    elif [[ "${filename}" =~ ^(.+)-(en|fi)$ ]]; then
        screenshot_id="${BASH_REMATCH[1]}"
        language="${BASH_REMATCH[2]}"
        screenshot_key="${screenshot_id}-${language}"
    else
        screenshot_id="${filename}"
        language="en"
        screenshot_key="${screenshot_id}-${language}"
    fi
    existing_id="$(find_existing_attachment_id "${screenshot_key}" "${screenshot_id}" "${language}")"
    file_bytes="$(wc -c < "${screenshot}" | tr -d '[:space:]')"

    if [ -n "${existing_id}" ]; then
        attached_file="$(wp post meta get "${existing_id}" _wp_attached_file --path="${WP_PATH}" 2>/dev/null || true)"

        if [ -z "${attached_file}" ]; then
            failed=$((failed + 1))
            gwi_screenshot_log warn "Attachment has no file metadata" "{\"attachmentId\":${existing_id},\"screenshotId\":\"${screenshot_id}\",\"language\":\"${language}\"}"
            continue
        fi

        dest="${WP_PATH}/wp-content/uploads/${attached_file}"
        cp "${screenshot}" "${dest}"
        wp media regenerate "${existing_id}" --yes --path="${WP_PATH}" >/dev/null
        wp post meta update "${existing_id}" _gwi_screenshot_key "${screenshot_key}" --path="${WP_PATH}" >/dev/null
        attachment_id="${existing_id}"
        delete_duplicate_attachments "${screenshot_key}" "${screenshot_id}" "${language}" "${attachment_id}"
        updated=$((updated + 1))
        GWI_LOG_UPDATED=$((GWI_LOG_UPDATED + 1))
        gwi_screenshot_log info "Updated attachment" "{\"screenshotId\":\"${screenshot_id}\",\"language\":\"${language}\",\"attachmentId\":${attachment_id},\"bytes\":${file_bytes},\"dest\":\"${dest}\"}"
    else
        attachment_id="$(wp media import "${screenshot}" \
            --path="${WP_PATH}" \
            --title="Screenshot: ${screenshot_id}" \
            --alt="WordPress admin screenshot: ${screenshot_id}" \
            --porcelain 2>/dev/null || echo "")"

        if [ -z "${attachment_id}" ]; then
            failed=$((failed + 1))
            gwi_screenshot_log error "Failed to import screenshot" "{\"screenshotId\":\"${screenshot_id}\",\"language\":\"${language}\",\"source\":\"${screenshot}\"}"
            continue
        fi

        wp post meta update "${attachment_id}" _gwi_screenshot_key "${screenshot_key}" --path="${WP_PATH}" >/dev/null
        imported=$((imported + 1))
        GWI_LOG_IMPORTED=$((GWI_LOG_IMPORTED + 1))
        gwi_screenshot_log info "Imported attachment" "{\"screenshotId\":\"${screenshot_id}\",\"language\":\"${language}\",\"attachmentId\":${attachment_id},\"bytes\":${file_bytes}}"
    fi

    image_url="$(wp post get "${attachment_id}" --field=guid --path="${WP_PATH}" 2>/dev/null || echo "")"

    if [ -z "${image_url}" ]; then
        failed=$((failed + 1))
        gwi_screenshot_log error "Failed to resolve attachment URL" "{\"attachmentId\":${attachment_id},\"screenshotId\":\"${screenshot_id}\",\"language\":\"${language}\"}"
        continue
    fi

    echo "${screenshot_id}|${image_url}|${language}|${attachment_id}" >> "${manifest_file}"
    GWI_LOG_SUCCESS=$((GWI_LOG_SUCCESS + 1))
done

status="success"
if [ "${failed}" -gt 0 ]; then
    status="failed"
fi

gwi_screenshot_log info "Import run finished" "{\"imported\":${imported},\"updated\":${updated},\"failed\":${failed},\"manifest\":\"${manifest_file}\"}"
gwi_screenshot_log_finish "${status}"
echo "Run 'wp eval-file /app/tools/link-screenshots.php' to link screenshots to tutorials."

if [ "${status}" = "failed" ]; then
    exit 1
fi
