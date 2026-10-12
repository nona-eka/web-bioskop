USE bioskop_db;

UPDATE users
SET password = '$2y$12$JjNFEthQJan6HZ1q/LkMHum1k4YT/s5Cz2u119Yb/guGR/J1Q7So.'
WHERE email = 'admin@gmail.com' AND role = 'admin';

UPDATE users
SET password = '$2y$12$70o0e0T6S3sOzS2z0xSYJuJVeBXzB8vQxAGB91/MOrfnfTQqu5y.e'
WHERE email = 'staf@gmail.com' AND role = 'staff';

UPDATE movies
SET poster = TRIM(poster)
WHERE poster IS NOT NULL;

UPDATE products
SET image = TRIM(image)
WHERE image IS NOT NULL;
