#!/bin/bash

# ==========================================================
# BMS Backup Log Sender
# Helper script untuk mengirim hasil backup ke BMS API.
#
# Catatan:
# - Script ini TIDAK menjalankan backup.
# - Script ini hanya mengirim log hasil backup.
# - Bisa dipanggil dari script backup/cron existing.
# ==========================================================

BMS_API_URL="${BMS_API_URL:-http://127.0.0.1:8000/api/backup/logs}"
BMS_API_TOKEN="${BMS_API_TOKEN:-}"
BMS_JOB_CODE="${BMS_JOB_CODE:-}"

bms_send_log() {
    local status="$1"
    local backup_date="$2"
    local started_at="$3"
    local finished_at="$4"
    local file_name="$5"
    local file_path="$6"
    local file_size_bytes="$7"
    local checksum="$8"
    local message="$9"
    local error_message="${10}"

    if [ -z "$BMS_API_TOKEN" ]; then
        echo "[BMS] ERROR: BMS_API_TOKEN belum diisi."
        return 1
    fi

    if [ -z "$BMS_JOB_CODE" ]; then
        echo "[BMS] ERROR: BMS_JOB_CODE belum diisi."
        return 1
    fi

    if [ -z "$status" ]; then
        echo "[BMS] ERROR: status belum diisi."
        return 1
    fi

    if [ -z "$backup_date" ]; then
        echo "[BMS] ERROR: backup_date belum diisi."
        return 1
    fi

    # Untuk nilai kosong, kirim null agar JSON tetap valid.
    [ -z "$started_at" ] && started_at="null" || started_at="\"$started_at\""
    [ -z "$finished_at" ] && finished_at="null" || finished_at="\"$finished_at\""
    [ -z "$file_name" ] && file_name="null" || file_name="\"$file_name\""
    [ -z "$file_path" ] && file_path="null" || file_path="\"$file_path\""
    [ -z "$file_size_bytes" ] && file_size_bytes="null"
    [ -z "$checksum" ] && checksum="null" || checksum="\"$checksum\""
    [ -z "$message" ] && message="null" || message="\"$message\""
    [ -z "$error_message" ] && error_message="null" || error_message="\"$error_message\""

    response=$(curl -s -X POST "$BMS_API_URL" \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -H "X-BMS-Token: $BMS_API_TOKEN" \
        -d "{
            \"job_code\": \"$BMS_JOB_CODE\",
            \"status\": \"$status\",
            \"backup_date\": \"$backup_date\",
            \"started_at\": $started_at,
            \"finished_at\": $finished_at,
            \"file_name\": $file_name,
            \"file_path\": $file_path,
            \"file_size_bytes\": $file_size_bytes,
            \"checksum\": $checksum,
            \"message\": $message,
            \"error_message\": $error_message
        }")

    echo "[BMS] Response: $response"
}