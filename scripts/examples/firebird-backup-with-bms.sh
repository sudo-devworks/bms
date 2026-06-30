#!/bin/bash

# ==========================================================
# Example: Firebird Backup + BMS Log Sender
#
# Catatan:
# - Ini contoh integrasi.
# - Sesuaikan DB_PATH, BACKUP_DIR, dan gbak credential.
# - Script ini bisa dijadikan referensi untuk server backup asli.
# ==========================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BMS_HELPER="$SCRIPT_DIR/../bms-send-log.sh"

source "$BMS_HELPER"

# =========================
# Konfigurasi BMS
# =========================
export BMS_API_URL="http://localhost/api/backup/logs"
export BMS_API_TOKEN="zgmfx20a"
export BMS_JOB_CODE="BACKUP_TRACER_STUDY_DB_DAILY"

# =========================
# Konfigurasi Backup Firebird
# =========================
DB_PATH="/home/database/ALUMNI_STIP.FDB"
BACKUP_DIR="/home/databases/backups/export/$(date '+%Y')/$(date '+%m')"
BACKUP_FILE="$BACKUP_DIR/tracer_study_$(date '+%Y_%m_%d_%H_%M_%S').fbk"

FB_USER="SYSDBA"
FB_PASSWORD="isi_password_firebird"

MIN_SIZE=1048576

BACKUP_DATE=$(date '+%Y-%m-%d')
STARTED_AT=$(date '+%Y-%m-%d %H:%M:%S')

mkdir -p "$BACKUP_DIR"

ERROR_LOG="/tmp/bms_firebird_backup_error_$(date '+%Y%m%d_%H%M%S').log"

gbak -b -v -user "$FB_USER" -password "$FB_PASSWORD" "$DB_PATH" "$BACKUP_FILE" 2> "$ERROR_LOG"

EXIT_CODE=$?
FINISHED_AT=$(date '+%Y-%m-%d %H:%M:%S')

if [ "$EXIT_CODE" -ne 0 ]; then
    ERROR_MESSAGE=$(cat "$ERROR_LOG" | tail -n 5 | tr '\n' ' ')

    bms_send_log \
        "failed" \
        "$BACKUP_DATE" \
        "$STARTED_AT" \
        "$FINISHED_AT" \
        "" \
        "" \
        "" \
        "" \
        "Backup Firebird gagal saat proses gbak" \
        "$ERROR_MESSAGE"

    exit 1
fi

if [ ! -f "$BACKUP_FILE" ]; then
    bms_send_log \
        "failed" \
        "$BACKUP_DATE" \
        "$STARTED_AT" \
        "$FINISHED_AT" \
        "" \
        "" \
        "" \
        "" \
        "Backup Firebird gagal, file hasil backup tidak ditemukan" \
        "File backup tidak ditemukan setelah gbak selesai"

    exit 1
fi

FILE_NAME=$(basename "$BACKUP_FILE")
FILE_SIZE=$(stat -c%s "$BACKUP_FILE")

if [ "$FILE_SIZE" -lt "$MIN_SIZE" ]; then
    bms_send_log \
        "warning" \
        "$BACKUP_DATE" \
        "$STARTED_AT" \
        "$FINISHED_AT" \
        "$FILE_NAME" \
        "$BACKUP_FILE" \
        "$FILE_SIZE" \
        "" \
        "Backup Firebird selesai tetapi ukuran file lebih kecil dari batas minimal" \
        ""

    exit 0
fi

bms_send_log \
    "success" \
    "$BACKUP_DATE" \
    "$STARTED_AT" \
    "$FINISHED_AT" \
    "$FILE_NAME" \
    "$BACKUP_FILE" \
    "$FILE_SIZE" \
    "" \
    "Backup Firebird berhasil" \
    ""

exit 0