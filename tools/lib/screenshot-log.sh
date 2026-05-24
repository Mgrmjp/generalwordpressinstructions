#!/usr/bin/env bash

gwi_screenshot_log_init() {
    local step="$1"
    local screenshot_dir="$2"

    GWI_LOG_STEP="${step}"
    GWI_LOG_DIR="${screenshot_dir}/logs"
    GWI_LOG_STARTED_AT="$(date -u +"%Y-%m-%dT%H:%M:%SZ")"
    GWI_LOG_STARTED_EPOCH="${SECONDS}"
    GWI_LOG_SUCCESS=0
    GWI_LOG_UPDATED=0
    GWI_LOG_IMPORTED=0
    GWI_LOG_LINKED=0
    GWI_LOG_SKIPPED=0
    GWI_LOG_WARNINGS=0
    GWI_LOG_ERRORS=0

    if [ -n "${GWI_SCREENSHOT_RUN_ID:-}" ]; then
        GWI_LOG_RUN_ID="${GWI_SCREENSHOT_RUN_ID}"
    elif [ -f "${screenshot_dir}/.run-id" ]; then
        GWI_LOG_RUN_ID="$(tr -d '[:space:]' < "${screenshot_dir}/.run-id")"
    else
        GWI_LOG_RUN_ID="$(date -u +"%Y%m%dT%H%M%SZ")"
    fi

    mkdir -p "${GWI_LOG_DIR}"
    printf '%s\n' "${GWI_LOG_RUN_ID}" > "${screenshot_dir}/.run-id"

    GWI_LOG_JSONL="${GWI_LOG_DIR}/${GWI_LOG_RUN_ID}-${step}.jsonl"
    GWI_LOG_SUMMARY="${GWI_LOG_DIR}/${GWI_LOG_RUN_ID}-${step}-summary.json"
    GWI_LOG_PIPELINE="${GWI_LOG_DIR}/pipeline.jsonl"
}

gwi_screenshot_log_json_escape() {
    local value="$1"
    value="${value//\\/\\\\}"
    value="${value//\"/\\\"}"
    value="${value//$'\n'/\\n}"
    value="${value//$'\r'/\\r}"
    value="${value//$'\t'/\\t}"
    printf '%s' "${value}"
}

gwi_screenshot_log_write() {
    local level="$1"
    local message="$2"
    local data="${3:-{}}"
    local ts
    local escaped_message

    ts="$(date -u +"%Y-%m-%dT%H:%M:%SZ")"
    escaped_message="$(gwi_screenshot_log_json_escape "${message}")"
    printf '{"ts":"%s","runId":"%s","step":"%s","level":"%s","message":"%s","data":%s}\n' \
        "${ts}" "${GWI_LOG_RUN_ID}" "${GWI_LOG_STEP}" "${level}" "${escaped_message}" "${data}" \
        >> "${GWI_LOG_JSONL}"
    printf '{"ts":"%s","runId":"%s","step":"%s","level":"%s","message":"%s","data":%s}\n' \
        "${ts}" "${GWI_LOG_RUN_ID}" "${GWI_LOG_STEP}" "${level}" "${escaped_message}" "${data}" \
        >> "${GWI_LOG_PIPELINE}"
}

gwi_screenshot_log() {
    local level="$1"
    local message="$2"
    local data="${3:-{}}"

    gwi_screenshot_log_write "${level}" "${message}" "${data}"

    case "${level}" in
        error)
            printf '[gwi:%s:error] %s %s\n' "${GWI_LOG_STEP}" "${message}" "${data}" >&2
            GWI_LOG_ERRORS=$((GWI_LOG_ERRORS + 1))
            ;;
        warn)
            printf '[gwi:%s:warn] %s %s\n' "${GWI_LOG_STEP}" "${message}" "${data}" >&2
            GWI_LOG_WARNINGS=$((GWI_LOG_WARNINGS + 1))
            ;;
        *)
            printf '[gwi:%s:info] %s %s\n' "${GWI_LOG_STEP}" "${message}" "${data}"
            ;;
    esac
}

gwi_screenshot_log_finish() {
    local status="$1"
    local finished_at
    local duration_ms

    finished_at="$(date -u +"%Y-%m-%dT%H:%M:%SZ")"
    duration_ms="$(( (SECONDS - GWI_LOG_STARTED_EPOCH) * 1000 ))"

    cat > "${GWI_LOG_SUMMARY}" <<EOF
{
  "runId": "${GWI_LOG_RUN_ID}",
  "step": "${GWI_LOG_STEP}",
  "status": "${status}",
  "startedAt": "${GWI_LOG_STARTED_AT}",
  "finishedAt": "${finished_at}",
  "durationMs": ${duration_ms},
  "counts": {
    "success": ${GWI_LOG_SUCCESS},
    "updated": ${GWI_LOG_UPDATED},
    "imported": ${GWI_LOG_IMPORTED},
    "linked": ${GWI_LOG_LINKED},
    "skipped": ${GWI_LOG_SKIPPED},
    "warnings": ${GWI_LOG_WARNINGS},
    "errors": ${GWI_LOG_ERRORS}
  },
  "summaryPath": "${GWI_LOG_SUMMARY}",
  "jsonlPath": "${GWI_LOG_JSONL}"
}
EOF

    gwi_screenshot_log info "Run complete" "{\"status\":\"${status}\",\"durationMs\":${duration_ms},\"summaryPath\":\"${GWI_LOG_SUMMARY}\"}"
}
