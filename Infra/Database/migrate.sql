CREATE TABLE IF NOT EXISTS parking (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    plate TEXT NOT NULL,
    type TEXT NOT NULL,
    entry_at TEXT NOT NULL,
    exit_at TEXT,
    amount REAL
);
