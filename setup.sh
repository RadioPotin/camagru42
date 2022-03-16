#!/bin/sh

set -eu

sqlite3 src/login.db ".read db.sql"
