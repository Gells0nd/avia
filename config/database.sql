-- Создание таблицы пользователей, если её нет
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы рейсов, если её нет
CREATE TABLE IF NOT EXISTS flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_city VARCHAR(255) NOT NULL,
    to_city VARCHAR(255) NOT NULL,
    departure_date DATETIME NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    seats_available INT NOT NULL DEFAULT 100,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы заказов
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    flight_id INT NOT NULL,
    passenger_name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    status ENUM('active', 'cancelled', 'completed') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (flight_id) REFERENCES flights(id) ON DELETE CASCADE
);

-- Добавляем индексы для оптимизации запросов
CREATE INDEX IF NOT EXISTS idx_orders_user_id ON orders(user_id);
CREATE INDEX IF NOT EXISTS idx_orders_flight_id ON orders(flight_id);
CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status);
CREATE INDEX IF NOT EXISTS idx_flights_departure_date ON flights(departure_date);
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);

-- Добавляем поле seats_available в таблицу flights, если его еще нет
ALTER TABLE flights ADD COLUMN IF NOT EXISTS seats_available INT NOT NULL DEFAULT 100; 