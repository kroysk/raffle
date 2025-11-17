-- Raffles table
CREATE TABLE IF NOT EXISTS raffles (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    shopwired_account_id INTEGER NOT NULL REFERENCES shopwired_accounts(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    product_id VARCHAR(255), -- ShopWired product ID
    max_entries INTEGER,
    status VARCHAR(50) DEFAULT 'active', -- draft, active, completed, cancelled
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

