USE compras_publicas;

SET FOREIGN_KEY_CHECKS = 0;

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

-- Participación proveedores en procesos
INSERT INTO proceso_proveedor (id_proceso, id_proveedor, fecha_participacion) VALUES
(1, 3, '2024-01-20'), (1, 1, '2024-01-21'),
(2, 4, '2024-02-05'), (2, 1, '2024-02-06'),
(3, 2, '2024-03-15'),
(4, 3, '2024-04-10'), (4, 4, '2024-04-11'),
(5, 1, '2024-04-25'), (5, 5, '2024-04-26');

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

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Instituciones:' AS tabla, COUNT(*) AS registros FROM institucion_publica
UNION ALL SELECT 'Proveedores:', COUNT(*) FROM proveedor
UNION ALL SELECT 'Procesos:', COUNT(*) FROM proceso_compra
UNION ALL SELECT 'Contratos:', COUNT(*) FROM contrato
UNION ALL SELECT 'Pagos:', COUNT(*) FROM pago
UNION ALL SELECT 'Usuarios:', COUNT(*) FROM usuarios;
