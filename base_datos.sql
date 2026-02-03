-- ============================================================
-- CENTRO EDUCATIVO - SCRIPT DE BASE DE DATOS
-- Proyecto IAW - CFGS ASIR
-- ============================================================

-- Crear la base de datos
DROP DATABASE IF EXISTS centro_educativo;
CREATE DATABASE centro_educativo CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE centro_educativo;

-- ============================================================
-- TABLA: usuarios
-- Sistema de autenticacion con roles (admin/profesor)
-- ============================================================
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(150) NOT NULL,
    rol ENUM('admin', 'profesor', 'estudiante') NOT NULL DEFAULT 'profesor',
    email VARCHAR(100),
    id_estudiante INT DEFAULT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_estudiante FOREIGN KEY (id_estudiante)
        REFERENCES estudiantes(id_estudiante) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Usuarios por defecto (password: admin123 y profesor123)
-- El sistema acepta tanto texto plano como hash para pruebas
INSERT INTO usuarios (username, password, nombre_completo, rol, email) VALUES
('admin', 'admin123', 'Administrador del Sistema', 'admin', 'admin@centro.edu'),
('profesor1', 'profesor123', 'Maria Garcia Lopez', 'profesor', 'maria.garcia@centro.edu');


-- ============================================================
-- TABLA: profesores
-- Almacena información de los profesores del centro
-- ============================================================
CREATE TABLE profesores (
    id_profesor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(15),
    especialidad VARCHAR(100) NOT NULL,
    fecha_alta DATE NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    CONSTRAINT chk_email_profesor CHECK (email LIKE '%@%.%')
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: cursos
-- Almacena información de los cursos ofertados
-- Relacionada con profesores (un profesor imparte un curso)
-- ============================================================
CREATE TABLE cursos (
    id_curso INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    horas INT NOT NULL,
    id_profesor INT,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    activo TINYINT(1) DEFAULT 1,
    CONSTRAINT fk_curso_profesor FOREIGN KEY (id_profesor) 
        REFERENCES profesores(id_profesor) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT chk_horas CHECK (horas > 0)
) ENGINE=InnoDB;

-- ============================================================
-- TABLA: estudiantes
-- Tabla principal - CRUD completo
-- Almacena información de los estudiantes matriculados
-- Relacionada con cursos (un estudiante está en un curso)
-- ============================================================
CREATE TABLE estudiantes (
    id_estudiante INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(15),
    fecha_nacimiento DATE,
    direccion VARCHAR(200),
    id_curso INT,
    fecha_matricula DATE NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    CONSTRAINT fk_estudiante_curso FOREIGN KEY (id_curso)
        REFERENCES cursos(id_curso) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT chk_email_estudiante CHECK (email LIKE '%@%.%')
) ENGINE=InnoDB;

-- ============================================================
-- DATOS DE EJEMPLO: Profesores
-- ============================================================
INSERT INTO profesores (nombre, apellidos, email, telefono, especialidad, fecha_alta) VALUES
('Maria', 'Garcia Lopez', 'maria.garcia@centro.edu', '612345678', 'Matematicas', '2020-09-01'),
('Carlos', 'Martinez Ruiz', 'carlos.martinez@centro.edu', '623456789', 'Lengua y Literatura', '2019-09-01'),
('Ana', 'Fernandez Diaz', 'ana.fernandez@centro.edu', '634567890', 'Ingles', '2021-09-01'),
('Pedro', 'Lopez Sanchez', 'pedro.lopez@centro.edu', '645678901', 'Informatica', '2018-09-01'),
('Laura', 'Rodriguez Perez', 'laura.rodriguez@centro.edu', '656789012', 'Ciencias Naturales', '2022-09-01');

-- ============================================================
-- DATOS DE EJEMPLO: Cursos
-- ============================================================
INSERT INTO cursos (nombre, descripcion, horas, id_profesor, fecha_inicio, fecha_fin) VALUES
('1 ESO - Grupo A', 'Primer curso de Educacion Secundaria Obligatoria', 1050, 1, '2024-09-10', '2025-06-20'),
('2 ESO - Grupo A', 'Segundo curso de Educacion Secundaria Obligatoria', 1050, 2, '2024-09-10', '2025-06-20'),
('3 ESO - Grupo A', 'Tercer curso de Educacion Secundaria Obligatoria', 1050, 3, '2024-09-10', '2025-06-20'),
('1 Bachillerato - Ciencias', 'Primer curso de Bachillerato modalidad Ciencias', 1000, 4, '2024-09-10', '2025-06-20');

-- ============================================================
-- DATOS DE EJEMPLO: Estudiantes
-- ============================================================
INSERT INTO estudiantes (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, id_curso, fecha_matricula) VALUES
('Pablo', 'Hernandez Gomez', 'pablo.hernandez@alumno.centro.edu', '611111111', '2010-03-15', 'Calle Mayor 10, Madrid', 1, '2024-07-15'),
('Lucia', 'Sanchez Torres', 'lucia.sanchez@alumno.centro.edu', '622222222', '2010-07-22', 'Avenida Central 25, Madrid', 1, '2024-07-16'),
('Daniel', 'Martin Vega', 'daniel.martin@alumno.centro.edu', '633333333', '2009-11-08', 'Plaza Espana 5, Madrid', 2, '2024-07-10'),
('Sara', 'Ruiz Moreno', 'sara.ruiz@alumno.centro.edu', '644444444', '2009-05-30', 'Calle Luna 18, Madrid', 2, '2024-07-12'),
('Alejandro', 'Jimenez Blanco', 'alejandro.jimenez@alumno.centro.edu', '655555555', '2008-02-14', 'Paseo del Prado 30, Madrid', 3, '2024-07-08'),
('Elena', 'Castro Ramos', 'elena.castro@alumno.centro.edu', '666666666', '2008-09-25', 'Gran Via 45, Madrid', 3, '2024-07-09'),
('Miguel', 'Flores Ortega', 'miguel.flores@alumno.centro.edu', '677777777', '2007-12-03', 'Calle Sol 8, Madrid', 4, '2024-07-05'),
('Carmen', 'Navarro Gil', 'carmen.navarro@alumno.centro.edu', '688888888', '2007-04-18', 'Avenida Libertad 12, Madrid', 4, '2024-07-06'),
('Adrian', 'Reyes Molina', 'adrian.reyes@alumno.centro.edu', '699999999', '2010-08-10', 'Calle Norte 22, Madrid', 1, '2024-07-20'),
('Marta', 'Vargas Ibanez', 'marta.vargas@alumno.centro.edu', '610101010', '2009-01-27', 'Plaza Mayor 3, Madrid', 2, '2024-07-18');

-- ============================================================
-- ÍNDICES ADICIONALES PARA MEJORAR EL RENDIMIENTO
-- ============================================================
CREATE INDEX idx_estudiantes_curso ON estudiantes(id_curso);
CREATE INDEX idx_cursos_profesor ON cursos(id_profesor);
CREATE INDEX idx_estudiantes_activo ON estudiantes(activo);
CREATE INDEX idx_profesores_activo ON profesores(activo);
CREATE INDEX idx_cursos_activo ON cursos(activo);

-- ============================================================
-- VERIFICACIÓN: Consulta de prueba
-- ============================================================
SELECT 'Base de datos creada correctamente' AS mensaje;
SELECT COUNT(*) AS total_profesores FROM profesores;
SELECT COUNT(*) AS total_cursos FROM cursos;
SELECT COUNT(*) AS total_estudiantes FROM estudiantes;
