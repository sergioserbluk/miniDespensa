CREATE DATABASE IF NOT EXISTS minidespensa;
USE minidespensa;

-- Tabla de categorías
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- Tabla de productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_barra VARCHAR(50) UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    id_categoria INT,
    unidad_medida VARCHAR(20) DEFAULT 'unidad',
    stock_actual DECIMAL(10,2) DEFAULT 0,
    precio_costo DECIMAL(10,2) DEFAULT 0,
    utilidad DECIMAL(5,2) DEFAULT 0,
    precio_venta DECIMAL(10,2) DEFAULT 0,
    fecha_actualizado DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    estado TINYINT DEFAULT 1,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id)
) ENGINE=InnoDB;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    rol ENUM('vendedor','repositor','compras','gerente','admin') NOT NULL,
    activo TINYINT DEFAULT 1
) ENGINE=InnoDB;

-- Tabla de compras (ingreso de stock)
CREATE TABLE compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    proveedor VARCHAR(100),
    total DECIMAL(10,2) DEFAULT 0,
    usuario_id INT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Detalle de productos comprados
CREATE TABLE detalle_compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT,
    producto_id INT,
    cantidad DECIMAL(10,2) DEFAULT 0,
    precio_unitario DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (compra_id) REFERENCES compras(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=InnoDB;

-- Tabla de ventas (facturas)
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo CHAR(1) CHECK (tipo IN ('A','B','C')),
    punto_venta INT,
    numero INT,
    cae VARCHAR(20),
    vencimiento_cae DATE,
    total DECIMAL(10,2) DEFAULT 0,
    usuario_id INT,
    cliente_nombre VARCHAR(100),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Detalle de productos vendidos
CREATE TABLE detalle_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT,
    producto_id INT,
    cantidad DECIMAL(10,2) DEFAULT 0,
    precio_unitario DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=InnoDB;
