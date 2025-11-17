-- Raffle Entries table
CREATE TABLE IF NOT EXISTS raffle_entries (
    id SERIAL PRIMARY KEY,
    raffle_id INTEGER NOT NULL REFERENCES raffles(id) ON DELETE CASCADE,
    customer_name VARCHAR(255),
    customer_email VARCHAR(255),
    customer_address VARCHAR(255),
    raffle_number BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_raffle_entries_raffle_id ON raffle_entries(raffle_id);