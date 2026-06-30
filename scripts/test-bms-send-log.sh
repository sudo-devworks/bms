#!/bin/bash

# ==========================================================
# Test BMS Backup Log Sender
# Script simulasi untuk memastikan bms-send-log.sh bisa
# mengirim log ke API BMS.
# ==========================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

source "$SCRIPT_DIR/bms-send-log.sh"

export BMS_API_URL="http://127.0.0.1:8002/api/backup/logs"
export BMS_API_TOKEN="bms_internal_backup_receiver_2026"
export BMS_JOB_CODE="BACKUP_TRACER_STUDY_DB_DAILY"

BACKUP_DATE=$(date '+%Y-%m-%d')
STARTED_AT=$(date '+%Y-%m-%d %H:%M:%S')

# Simulasi proses backup 2 detik
sleep 2

FINISHED_AT=$(date '+%Y-%m-%d %H:%M:%S')

DUMMY_BACKUP_DIR="$SCRIPT_DIR/dummy-backups"
DUMMY_BACKUP_FILE="$DUMMY_BACKUP_DIR/test_backup_$(date '+%Y_%m_%d_%H_%M_%S').bak"

mkdir -p "$DUMMY_BACKUP_DIR"

echo "Ini file dummy backup untuk test BMS" > "$DUMMY_BACKUP_FILE"

FILE_NAME=$(basename "$DUMMY_BACKUP_FILE")
FILE_SIZE=$(stat -c%s "$DUMMY_BACKUP_FILE")

bms_send_log \
    "success" \
    "$BACKUP_DATE" \
    "$STARTED_AT" \
    "$FINISHED_AT" \
    "$FILE_NAME" \
    "$DUMMY_BACKUP_FILE" \
    "$FILE_SIZE" \
    "" \
    "Test backup dummy berhasil" \
    ""