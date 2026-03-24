-- ============================================================
-- SISTEMA DE GESTIÓN DE COMPRAS PÚBLICAS
-- Base de Datos MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS compras_publicas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE compras_publicas;

-- ============================================================
-- TABLA: usuarios
-- ============================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('administrador','desarrollador','supervisor') NOT NULL DEFAULT 'supervisor',
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: institucion_publica
-- ============================================================
CREATE TABLE IF NOT EXISTS institucion_publica (
    id_institucion INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(150),
    direccion VARCHAR(300),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: proveedor
-- ============================================================
CREATE TABLE IF NOT EXISTS proveedor (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    razon_social VARCHAR(200) NOT NULL,
    numero_identificacion VARCHAR(20) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    correo VARCHAR(150),
    direccion VARCHAR(300),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: proceso_compra
-- ============================================================
CREATE TABLE IF NOT EXISTS proceso_compra (
    id_proceso INT AUTO_INCREMENT PRIMARY KEY,
    id_institucion INT NOT NULL,
    tipo_proceso VARCHAR(100) NOT NULL,
    fecha_inicio DATE NOT NULL,
    estado ENUM('planificacion','publicado','en_evaluacion','adjudicado','desierto','cancelado') NOT NULL DEFAULT 'planificacion',
    descripcion TEXT,
    presupuesto_referencial DECIMAL(15,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_proceso_institucion FOREIGN KEY (id_institucion) REFERENCES institucion_publica(id_institucion) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: proceso_proveedor (relación muchos a muchos)
-- ============================================================
CREATE TABLE IF NOT EXISTS proceso_proveedor (
    id_proceso INT NOT NULL,
    id_proveedor INT NOT NULL,
    fecha_participacion DATE,
    PRIMARY KEY (id_proceso, id_proveedor),
    CONSTRAINT fk_pp_proceso FOREIGN KEY (id_proceso) REFERENCES proceso_compra(id_proceso) ON DELETE CASCADE,
    CONSTRAINT fk_pp_proveedor FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: contrato
-- ============================================================
CREATE TABLE IF NOT EXISTS contrato (
    id_contrato INT AUTO_INCREMENT PRIMARY KEY,
    id_proceso INT NOT NULL,
    id_proveedor INT NOT NULL,
    fecha_adjudicacion DATE NOT NULL,
    monto_contratado DECIMAL(15,2) NOT NULL,
    estado ENUM('vigente','finalizado','rescindido','suspendido') NOT NULL DEFAULT 'vigente',
    objeto_contrato TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_contrato_proceso FOREIGN KEY (id_proceso) REFERENCES proceso_compra(id_proceso) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_contrato_proveedor FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: pago
-- ============================================================
CREATE TABLE IF NOT EXISTS pago (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    id_contrato INT NOT NULL,
    fecha_pago DATE NOT NULL,
    monto_pagado DECIMAL(15,2) NOT NULL,
    descripcion VARCHAR(300),
    comprobante VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pago_contrato FOREIGN KEY (id_contrato) REFERENCES contrato(id_contrato) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- DATOS DE EJEMPLO
-- ============================================================

-- Usuarios (contraseñas hasheadas con password_hash)
-- admin123, dev123, super123
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Admin Principal', 'admin@compras.gob.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador'),
('Desarrollador TI', 'dev@compras.gob.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'desarrollador'),
('Supervisor Control', 'super@compras.gob.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'supervisor');

-- Nota: El hash arriba es para 'password' de Laravel/ejemplo. 
-- Al instalar, ejecutar el script create_users.php para generar hashes reales con password_hash().
-- Credenciales reales se insertan desde PHP:

-- Instituciones Públicas
INSERT INTO institucion_publica (nombre, telefono, correo, direccion) VALUES
('Ministerio de Salud Pública', '02-3814-400', 'contratacion@salud.gob.ec', 'Av. República de El Salvador N34-183, Quito'),
('Ministerio de Educación', '02-3961-300', 'adquisiciones@educacion.gob.ec', 'Av. Amazonas N34-451, Quito'),
('GAD Municipal de Quito', '02-3952-300', 'compras@quito.gob.ec', 'Venezuela 976 y Chile, Quito'),
('IESS - Instituto Ecuatoriano de Seguridad Social', '02-2559-000', 'contratacion@iess.gob.ec', 'Av. 10 de Agosto 2270, Quito');

-- Proveedores
INSERT INTO proveedor (razon_social, numero_identificacion, telefono, correo, direccion) VALUES
('Tecnologías Avanzadas S.A.', '1791234567001', '02-2345-678', 'ventas@tecavanzadas.com', 'Naciones Unidas E10-44, Quito'),
('Constructora del Pacífico Cía. Ltda.', '0992345678001', '04-2567-890', 'info@constructorapacifica.com', 'Av. Kennedy Norte, Guayaquil'),
('Suministros Médicos Ecuador S.A.', '1790987654001', '02-2678-901', 'ventas@sumedec.com', 'Av. 6 de Diciembre, Quito'),
('Distribuidora Educativa Nacional', '0990876543001', '04-2456-789', 'pedidos@distedu.com', 'Av. Barcelona, Guayaquil'),
('Servicios Integrados TIC S.A.S.', '1793456789001', '02-2890-123', 'proyectos@sitic.com.ec', 'Av. Eloy Alfaro N30-350, Quito');

-- Procesos de Compra
INSERT INTO proceso_compra (id_institucion, tipo_proceso, fecha_inicio, estado, descripcion, presupuesto_referencial) VALUES
(1, 'Licitación', '2024-01-15', 'adjudicado', 'Adquisición de equipos médicos para hospitales regionales', 850000.00),
(2, 'Subasta Inversa Electrónica', '2024-02-01', 'adjudicado', 'Compra de material didáctico para unidades educativas', 320000.00),
(3, 'Menor Cuantía', '2024-03-10', 'publicado', 'Mantenimiento de vías urbanas zona norte', 45000.00),
(1, 'Cotización', '2024-04-05', 'en_evaluacion', 'Adquisición de medicamentos esenciales', 175000.00),
(4, 'Licitación', '2024-04-20', 'planificacion', 'Sistema informático de gestión hospitalaria', 1200000.00);

-- Participación de proveedores en procesos
INSERT INTO proceso_proveedor (id_proceso, id_proveedor, fecha_participacion) VALUES
(1, 3, '2024-01-20'),
(1, 1, '2024-01-21'),
(2, 4, '2024-02-05'),
(2, 1, '2024-02-06'),
(3, 2, '2024-03-15'),
(4, 3, '2024-04-10'),
(4, 4, '2024-04-11'),
(5, 1, '2024-04-25'),
(5, 5, '2024-04-26');

-- Contratos
INSERT INTO contrato (id_proceso, id_proveedor, fecha_adjudicacion, monto_contratado, estado, objeto_contrato) VALUES
(1, 3, '2024-02-28', 820000.00, 'vigente', 'Suministro e instalación de equipos médicos especializados para 5 hospitales regionales'),
(2, 4, '2024-03-15', 310000.00, 'vigente', 'Provisión de textos escolares, útiles y material didáctico para el año lectivo 2024-2025');

-- Pagos
INSERT INTO pago (id_contrato, fecha_pago, monto_pagado, descripcion, comprobante) VALUES
(1, '2024-03-15', 246000.00, 'Anticipo 30% según cláusula contractual', 'CMP-2024-001'),
(1, '2024-05-20', 410000.00, 'Pago planilla 1 - entrega parcial equipos', 'CMP-2024-002'),
(2, '2024-04-01', 93000.00, 'Anticipo 30% contrato material didáctico', 'CMP-2024-003'),
(2, '2024-06-10', 155000.00, 'Pago planilla 1 - entrega primera fase', 'CMP-2024-004');

-- ============================================================
-- USUARIOS Y ROLES MySQL
-- ============================================================

-- Administrador: acceso total
CREATE USER IF NOT EXISTS 'admin_compras'@'localhost' IDENTIFIED BY 'Admin@2024Secure!';
GRANT ALL PRIVILEGES ON compras_publicas.* TO 'admin_compras'@'localhost';

-- Desarrollador: SELECT, INSERT, UPDATE en todas las tablas, sin acceso a tabla usuarios
CREATE USER IF NOT EXISTS 'dev_compras'@'localhost' IDENTIFIED BY 'Dev@2024Secure!';
GRANT SELECT, INSERT, UPDATE ON compras_publicas.institucion_publica TO 'dev_compras'@'localhost';
GRANT SELECT, INSERT, UPDATE ON compras_publicas.proveedor TO 'dev_compras'@'localhost';
GRANT SELECT, INSERT, UPDATE ON compras_publicas.proceso_compra TO 'dev_compras'@'localhost';
GRANT SELECT, INSERT, UPDATE ON compras_publicas.proceso_proveedor TO 'dev_compras'@'localhost';
GRANT SELECT, INSERT, UPDATE ON compras_publicas.contrato TO 'dev_compras'@'localhost';
GRANT SELECT, INSERT, UPDATE ON compras_publicas.pago TO 'dev_compras'@'localhost';

-- Supervisor: solo SELECT y vistas
CREATE USER IF NOT EXISTS 'super_compras'@'localhost' IDENTIFIED BY 'Super@2024Secure!';
GRANT SELECT ON compras_publicas.* TO 'super_compras'@'localhost';

FLUSH PRIVILEGES;

-- ============================================================
-- VISTAS ÚTILES
-- ============================================================
CREATE OR REPLACE VIEW v_contratos_detalle AS
SELECT 
    c.id_contrato,
    c.fecha_adjudicacion,
    c.monto_contratado,
    c.estado AS estado_contrato,
    c.objeto_contrato,
    pc.tipo_proceso,
    pc.fecha_inicio,
    ip.nombre AS institucion,
    p.razon_social AS proveedor
FROM contrato c
JOIN proceso_compra pc ON c.id_proceso = pc.id_proceso
JOIN institucion_publica ip ON pc.id_institucion = ip.id_institucion
JOIN proveedor p ON c.id_proveedor = p.id_proveedor;

CREATE OR REPLACE VIEW v_pagos_detalle AS
SELECT 
    pg.id_pago,
    pg.fecha_pago,
    pg.monto_pagado,
    pg.descripcion,
    pg.comprobante,
    c.objeto_contrato,
    p.razon_social AS proveedor,
    ip.nombre AS institucion
FROM pago pg
JOIN contrato c ON pg.id_contrato = c.id_contrato
JOIN proveedor p ON c.id_proveedor = p.id_proveedor
JOIN proceso_compra pc ON c.id_proceso = pc.id_proceso
JOIN institucion_publica ip ON pc.id_institucion = ip.id_institucion;
