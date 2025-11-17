CREATE TABLE IF NOT EXISTS shopwired_accounts (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    shopwired_api_key VARCHAR(255) NOT NULL,
    shopwired_api_secret VARCHAR(255) NOT NULL,
    shopwired_webhooks_secret VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);