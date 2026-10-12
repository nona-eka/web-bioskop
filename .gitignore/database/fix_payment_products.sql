CREATE TABLE IF NOT EXISTS payments (
  id INT(11) NOT NULL AUTO_INCREMENT,
  booking_id INT(11) NOT NULL,
  payment_method VARCHAR(50) NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  status ENUM('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
  paid_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY booking_id (booking_id),
  CONSTRAINT payments_ibfk_1 FOREIGN KEY (booking_id) REFERENCES bookings (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO payments (booking_id, payment_method, amount, status, paid_at)
SELECT b.id, COALESCE(b.payment_method, 'cash'), b.grand_total, b.payment_status,
       CASE WHEN b.payment_status = 'paid' THEN b.booking_date ELSE NULL END
FROM bookings b
LEFT JOIN payments p ON p.booking_id = b.id
WHERE p.id IS NULL;

INSERT INTO products (name, price, image, category)
SELECT seed.name, seed.price, seed.image, seed.category
FROM (
  SELECT 'Popcorn Caramel' AS name, 25000.00 AS price, '' AS image, 'food' AS category
  UNION ALL SELECT 'Kentang Goreng', 22000.00, '', 'food'
  UNION ALL SELECT 'Hotdog', 28000.00, '', 'food'
  UNION ALL SELECT 'Cola', 18000.00, '', 'beverage'
  UNION ALL SELECT 'Air Mineral', 10000.00, '', 'beverage'
  UNION ALL SELECT 'Popcorn + Cola', 38000.00, '', 'combo'
) seed
WHERE NOT EXISTS (SELECT 1 FROM products LIMIT 1);
