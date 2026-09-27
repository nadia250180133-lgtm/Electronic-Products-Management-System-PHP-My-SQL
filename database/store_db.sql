create DATABASE

USE store_db;

cREATE TABLE products (
   id INT AUTO_INCREMENT PRIMARY KEY,
   nama VARCHAR(100) NOT NULL,
   katagori VARCHAR(50) NOT NULL,
   harga DECIMAL(10, 2) NOT NULL,
    stok INT NOT NULL
    deskripsi TEXT
);

INSERT INTO products (nama, katagori, harga, stok, deskripsi) VALUES
('Laptop', 'Elektronik', 1500.00, 10, 'Laptop gaming dengan spesifikasi tinggi'),
('Smartphone', 'Elektronik', 800.00, 20, 'Smartphone terbaru dengan kamera canggih'),
('LG Monitor', 'Furniture', 200.00, 15, 'Monitor LED berkualitas tinggi'),
('Logitech K120', 'Furniture', 100.00, 30, 'Keyboard USB sederhana'),
('Logitech M170', 'Mouse', 150000, 7, 'Mouse wireless');
