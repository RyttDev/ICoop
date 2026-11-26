CREATE DATABASE IF NOT EXISTS icoop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE icoop;

CREATE TABLE migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE members (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL UNIQUE,
    member_no VARCHAR(30) NOT NULL UNIQUE,
    full_name VARCHAR(255) NOT NULL,
    ic_no VARCHAR(30) NOT NULL UNIQUE,
    email VARCHAR(255) NULL,
    phone VARCHAR(30) NULL,
    address TEXT NULL,
    joined_at DATE NOT NULL,
    status ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT members_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE savings_accounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id BIGINT UNSIGNED NOT NULL,
    account_no VARCHAR(30) NOT NULL UNIQUE,
    account_type ENUM('share','monthly','voluntary') NOT NULL DEFAULT 'monthly',
    balance DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    status ENUM('active','closed') NOT NULL DEFAULT 'active',
    opened_at DATE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT savings_accounts_member_id_foreign FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE savings_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    savings_account_id BIGINT UNSIGNED NOT NULL,
    reference_no VARCHAR(40) NOT NULL UNIQUE,
    transaction_type ENUM('deposit','withdrawal','adjustment') NOT NULL,
    amount DECIMAL(14,2) NOT NULL,
    transaction_date DATE NOT NULL,
    notes VARCHAR(500) NULL,
    recorded_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT savings_transactions_account_id_foreign FOREIGN KEY (savings_account_id) REFERENCES savings_accounts(id) ON DELETE CASCADE,
    CONSTRAINT savings_transactions_recorded_by_foreign FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE financing_products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    code VARCHAR(30) NOT NULL UNIQUE,
    profit_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    maximum_amount DECIMAL(14,2) NOT NULL,
    maximum_tenure_months SMALLINT UNSIGNED NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE financing_applications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id BIGINT UNSIGNED NOT NULL,
    financing_product_id BIGINT UNSIGNED NOT NULL,
    application_no VARCHAR(40) NOT NULL UNIQUE,
    requested_amount DECIMAL(14,2) NOT NULL,
    approved_amount DECIMAL(14,2) NULL,
    tenure_months SMALLINT UNSIGNED NOT NULL,
    purpose TEXT NULL,
    applied_at DATE NOT NULL,
    approved_at DATE NULL,
    status ENUM('draft','submitted','review','pending_document','approved','rejected','disbursed','completed') NOT NULL DEFAULT 'submitted',
    reviewed_by BIGINT UNSIGNED NULL,
    remarks TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT financing_applications_member_id_foreign FOREIGN KEY (member_id) REFERENCES members(id),
    CONSTRAINT financing_applications_product_id_foreign FOREIGN KEY (financing_product_id) REFERENCES financing_products(id),
    CONSTRAINT financing_applications_reviewed_by_foreign FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE financing_repayments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    financing_application_id BIGINT UNSIGNED NOT NULL,
    receipt_no VARCHAR(40) NOT NULL UNIQUE,
    amount DECIMAL(14,2) NOT NULL,
    paid_at DATE NOT NULL,
    payment_method ENUM('cash','bank_transfer','online','salary_deduction') NOT NULL DEFAULT 'online',
    notes VARCHAR(500) NULL,
    recorded_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT financing_repayments_application_id_foreign FOREIGN KEY (financing_application_id) REFERENCES financing_applications(id) ON DELETE CASCADE,
    CONSTRAINT financing_repayments_recorded_by_foreign FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE financial_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_no VARCHAR(40) NOT NULL UNIQUE,
    transaction_date DATE NOT NULL,
    transaction_type ENUM('income','expense') NOT NULL,
    category VARCHAR(120) NOT NULL,
    description VARCHAR(500) NOT NULL,
    amount DECIMAL(14,2) NOT NULL,
    payment_method ENUM('cash','bank_transfer','online','cheque') NOT NULL DEFAULT 'online',
    member_id BIGINT UNSIGNED NULL,
    recorded_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT financial_transactions_member_id_foreign FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE SET NULL,
    CONSTRAINT financial_transactions_recorded_by_foreign FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_no VARCHAR(40) NOT NULL UNIQUE,
    document_type ENUM('invoice','quotation') NOT NULL DEFAULT 'invoice',
    member_id BIGINT UNSIGNED NOT NULL,
    attention_to VARCHAR(255) NULL,
    issue_date DATE NOT NULL,
    due_date DATE NULL,
    subtotal DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    discount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    status ENUM('draft','issued','paid','overdue','cancelled') NOT NULL DEFAULT 'draft',
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT invoices_member_id_foreign FOREIGN KEY (member_id) REFERENCES members(id),
    CONSTRAINT invoices_created_by_foreign FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE invoice_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    unit_price DECIMAL(14,2) NOT NULL,
    amount DECIMAL(14,2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT invoice_items_invoice_id_foreign FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE cooperative_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cooperative_name VARCHAR(255) NOT NULL,
    registration_no VARCHAR(100) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(30) NULL,
    bank_name VARCHAR(255) NULL,
    bank_account_no VARCHAR(100) NULL,
    address TEXT NULL,
    logo_path VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX sessions_user_id_index (user_id),
    INDEX sessions_last_activity_index (last_activity)
) ENGINE=InnoDB;

CREATE TABLE cache (
    `key` VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE cache_locks (
    `key` VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    INDEX jobs_queue_index (queue)
) ENGINE=InnoDB;

CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO users (name, email, password, created_at, updated_at) VALUES
('Nur Aisyah', 'admin@icoop.test', '$2y$12$vRmvWxv4xdK5wBObvnHxge8EhDR14yJnh8nKUpwqtXHoo52Ub4Q/2', NOW(), NOW());

INSERT INTO members (member_no, full_name, ic_no, email, phone, address, joined_at, status, created_at, updated_at) VALUES
('A-1001', 'Ahmad Firdaus', '900101-10-1234', 'ahmad@example.test', '012-3456789', 'Kuala Lumpur', '2024-01-15', 'active', NOW(), NOW()),
('A-1002', 'Siti Nurhaliza', '920202-10-2345', 'siti@example.test', '013-4567890', 'Shah Alam', '2024-03-20', 'active', NOW(), NOW()),
('A-1003', 'Mohd Arif', '880303-10-3456', 'arif@example.test', '014-5678901', 'Petaling Jaya', '2024-06-10', 'active', NOW(), NOW());

INSERT INTO savings_accounts (member_id, account_no, account_type, balance, opened_at, created_at, updated_at) VALUES
(1, 'SAV-1001', 'monthly', 1250.00, '2024-01-15', NOW(), NOW()),
(2, 'SAV-1002', 'monthly', 980.00, '2024-03-20', NOW(), NOW()),
(3, 'SAV-1003', 'share', 2000.00, '2024-06-10', NOW(), NOW());

INSERT INTO savings_transactions (savings_account_id, reference_no, transaction_type, amount, transaction_date, notes, recorded_by, created_at, updated_at) VALUES
(1, 'SAV-TX-0001', 'deposit', 250.00, CURDATE(), 'Simpanan bulanan', 1, NOW(), NOW()),
(2, 'SAV-TX-0002', 'deposit', 150.00, CURDATE(), 'Simpanan bulanan', 1, NOW(), NOW()),
(3, 'SAV-TX-0003', 'withdrawal', 500.00, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Pengeluaran simpanan', 1, NOW(), NOW());

INSERT INTO financing_products (name, code, profit_rate, maximum_amount, maximum_tenure_months, created_at, updated_at) VALUES
('Pembiayaan Peribadi', 'PP', 4.50, 50000.00, 60, NOW(), NOW()),
('Pembiayaan Pendidikan', 'PEND', 3.50, 30000.00, 48, NOW(), NOW());

INSERT INTO financing_applications (member_id, financing_product_id, application_no, requested_amount, tenure_months, purpose, applied_at, status, created_at, updated_at) VALUES
(1, 1, 'FIN-2026-0001', 10000.00, 36, 'Perbelanjaan peribadi', CURDATE(), 'review', NOW(), NOW()),
(2, 2, 'FIN-2026-0002', 8500.00, 24, 'Yuran pengajian', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'pending_document', NOW(), NOW());

INSERT INTO financial_transactions (transaction_no, transaction_date, transaction_type, category, description, amount, payment_method, member_id, recorded_by, created_at, updated_at) VALUES
('TRX-2026-0001', CURDATE(), 'income', 'Simpanan', 'Caruman simpanan Ahmad Firdaus', 250.00, 'online', 1, 1, NOW(), NOW()),
('TRX-2026-0002', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'expense', 'Pengeluaran Simpanan', 'Pengeluaran Mohd Arif', 500.00, 'bank_transfer', 3, 1, NOW(), NOW());

INSERT INTO invoices (invoice_no, member_id, issue_date, due_date, subtotal, discount, total, status, notes, created_by, created_at, updated_at) VALUES
('INV-2026-0001', 1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 250.00, 0.00, 250.00, 'issued', 'Caruman simpanan bulan semasa', 1, NOW(), NOW());

INSERT INTO invoice_items (invoice_id, description, quantity, unit_price, amount, created_at, updated_at) VALUES
(1, 'Caruman Simpanan Bulanan', 1.00, 250.00, 250.00, NOW(), NOW());

INSERT INTO cooperative_settings (cooperative_name, registration_no, email, phone, bank_name, bank_account_no, address, created_at, updated_at) VALUES
('iCOOP Koperasi Berhad', 'COOP/2026/001', 'info@icoop.test', '03-1234 5678', 'CIMB Bank', '8001805238', 'Kuala Lumpur, Malaysia', NOW(), NOW());

INSERT INTO migrations (migration, batch) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1);
