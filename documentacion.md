# CENTRO EDUCATIVO - DOCUMENTACIÓN DEL PROYECTO

## Índice
1. [Introducción](#1-introducción)
2. [Diseño de la Base de Datos](#2-diseño-de-la-base-de-datos)
3. [Funcionamiento de la Aplicación](#3-funcionamiento-de-la-aplicación)
4. [Despliegue en AWS](#4-despliegue-en-aws)
5. [Medidas de Seguridad](#5-medidas-de-seguridad)
6. [Acceso a la Aplicación](#6-acceso-a-la-aplicación)
7. [Posibles Mejoras Futuras](#7-posibles-mejoras-futuras)

---

## 1. Introducción

### 1.1 Contexto del Proyecto
Este proyecto se ha desarrollado como parte del módulo de **Implantación de Aplicaciones Web (IAW)** del Ciclo Formativo de Grado Superior de **Administración de Sistemas Informáticos en Red (ASIR)**.

### 1.2 Temática Elegida
Se ha optado por desarrollar un **Sistema de Gestión de Centro Educativo**, una aplicación web que permite administrar la información básica de un centro educativo, incluyendo:

- **Estudiantes**: Datos de los alumnos matriculados
- **Profesores**: Información del personal docente
- **Cursos**: Oferta formativa del centro

### 1.3 Tecnologías Utilizadas

| Tecnología | Uso |
|------------|-----|
| HTML5 | Estructura de las páginas web |
| CSS3 | Estilos y diseño visual |
| JavaScript | Validaciones y interactividad |
| PHP | Lógica de servidor y conexión a BD |
| MySQL | Sistema de gestión de base de datos |
| Apache | Servidor web |

### 1.4 Estructura del Proyecto

```
/centro_educativo
├── css/
│   └── estilos.css          # Estilos de la aplicación
├── js/
│   └── scripts.js           # Validaciones JavaScript
├── includes/
│   └── conexion.php         # Conexión a base de datos
├── index.php                # Página principal (dashboard)
├── estudiantes.php          # CRUD completo de estudiantes
├── profesores.php           # Gestión de profesores
├── cursos.php               # Gestión de cursos
├── base_datos.sql           # Script SQL de la base de datos
└── documentacion.md         # Este documento
```

---

## 2. Diseño de la Base de Datos

### 2.1 Diagrama Entidad-Relación

```
┌─────────────────┐         ┌─────────────────┐         ┌─────────────────┐
│   PROFESORES    │         │     CURSOS      │         │   ESTUDIANTES   │
├─────────────────┤         ├─────────────────┤         ├─────────────────┤
│ id_profesor (PK)│◄────────│ id_profesor (FK)│         │ id_estudiante(PK│
│ nombre          │    1:N  │ id_curso (PK)   │◄────────│ id_curso (FK)   │
│ apellidos       │         │ nombre          │    1:N  │ nombre          │
│ email           │         │ descripcion     │         │ apellidos       │
│ telefono        │         │ horas           │         │ email           │
│ especialidad    │         │ fecha_inicio    │         │ telefono        │
│ fecha_alta      │         │ fecha_fin       │         │ fecha_nacimiento│
│ activo          │         │ activo          │         │ direccion       │
└─────────────────┘         └─────────────────┘         │ fecha_matricula │
                                                        │ activo          │
                                                        └─────────────────┘
```

### 2.2 Descripción de las Tablas

#### Tabla: `profesores`
Almacena la información del personal docente del centro.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_profesor | INT (PK) | Identificador único del profesor |
| nombre | VARCHAR(50) | Nombre del profesor |
| apellidos | VARCHAR(100) | Apellidos del profesor |
| email | VARCHAR(100) | Correo electrónico (único) |
| telefono | VARCHAR(15) | Teléfono de contacto |
| especialidad | VARCHAR(100) | Área de especialización |
| fecha_alta | DATE | Fecha de incorporación |
| activo | TINYINT(1) | Estado (1=activo, 0=eliminado) |

#### Tabla: `cursos`
Contiene la información de los cursos ofertados.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_curso | INT (PK) | Identificador único del curso |
| nombre | VARCHAR(100) | Nombre del curso |
| descripcion | TEXT | Descripción del curso |
| horas | INT | Número total de horas |
| id_profesor | INT (FK) | Profesor responsable |
| fecha_inicio | DATE | Fecha de inicio |
| fecha_fin | DATE | Fecha de finalización |
| activo | TINYINT(1) | Estado activo/inactivo |

#### Tabla: `estudiantes`
Tabla principal con CRUD completo. Almacena los datos de los estudiantes.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id_estudiante | INT (PK) | Identificador único |
| nombre | VARCHAR(50) | Nombre del estudiante |
| apellidos | VARCHAR(100) | Apellidos del estudiante |
| email | VARCHAR(100) | Correo electrónico (único) |
| telefono | VARCHAR(15) | Teléfono de contacto |
| fecha_nacimiento | DATE | Fecha de nacimiento |
| direccion | VARCHAR(200) | Dirección postal |
| id_curso | INT (FK) | Curso en el que está matriculado |
| fecha_matricula | DATE | Fecha de matrícula |
| activo | TINYINT(1) | Estado activo/inactivo |

### 2.3 Relaciones

1. **profesores → cursos** (1:N): Un profesor puede impartir varios cursos
2. **cursos → estudiantes** (1:N): Un curso puede tener varios estudiantes matriculados

---

## 3. Funcionamiento de la Aplicación

### 3.1 Página Principal (index.php)
- Muestra estadísticas generales del centro
- Tarjetas con contadores de estudiantes, profesores y cursos
- Accesos rápidos para crear nuevos registros
- Navegación a todas las secciones

### 3.2 Gestión de Estudiantes (estudiantes.php)
**CRUD completo implementado:**

| Operación | Descripción |
|-----------|-------------|
| **CREATE** | Formulario para registrar nuevo estudiante con validación JavaScript |
| **READ** | Listado de todos los estudiantes con búsqueda en tiempo real |
| **UPDATE** | Formulario para editar datos existentes precargados |
| **DELETE** | Eliminación lógica (soft delete) con confirmación |

### 3.3 Gestión de Profesores (profesores.php)
- **Listar**: Tabla con todos los profesores y sus cursos asignados
- **Crear**: Formulario con campos obligatorios y validación

### 3.4 Gestión de Cursos (cursos.php)
- **Listar**: Tabla con cursos, profesor responsable y número de estudiantes
- **Crear**: Formulario con selector de profesor

### 3.5 Validaciones JavaScript
- Campos obligatorios marcados con asterisco (*)
- Validación de formato de email
- Confirmación antes de eliminar registros
- Búsqueda en tiempo real en las tablas

---

## 4. Despliegue en AWS

### 4.1 Requisitos Previos
- Cuenta de AWS (Free Tier)
- Cliente SSH (PuTTY en Windows o terminal en Linux/Mac)
- Par de claves (.pem) para acceso SSH

### 4.2 Paso 1: Crear Instancia EC2

1. Acceder a la consola de AWS: https://console.aws.amazon.com
2. Ir a **EC2** → **Instancias** → **Lanzar instancia**
3. Configurar:
   - **Nombre**: CentroEducativo-Server
   - **AMI**: Amazon Linux 2023 o Ubuntu 22.04 LTS
   - **Tipo de instancia**: t2.micro (Free Tier)
   - **Par de claves**: Crear o seleccionar existente
   - **Red**: Crear grupo de seguridad nuevo

### 4.3 Paso 2: Configurar Security Groups

Configurar las siguientes reglas de entrada:

| Tipo | Protocolo | Puerto | Origen | Descripción |
|------|-----------|--------|--------|-------------|
| SSH | TCP | 22 | Mi IP | Acceso administración |
| HTTP | TCP | 80 | 0.0.0.0/0 | Acceso web público |
| HTTPS | TCP | 443 | 0.0.0.0/0 | Acceso web seguro |

> **IMPORTANTE**: Nunca abrir el puerto 3306 (MySQL) a Internet

### 4.4 Paso 3: Conectar por SSH

```bash
# Linux/Mac
chmod 400 mi-clave.pem
ssh -i mi-clave.pem ec2-user@<IP-PUBLICA>

# Windows (PuTTY)
# Convertir .pem a .ppk con PuTTYgen
# Conectar usando la IP pública
```

### 4.5 Paso 4: Instalar LAMP Stack

#### Para Amazon Linux 2023:
```bash
# Actualizar sistema
sudo dnf update -y

# Instalar Apache
sudo dnf install httpd -y
sudo systemctl start httpd
sudo systemctl enable httpd

# Instalar PHP
sudo dnf install php php-mysqlnd php-mbstring -y
sudo systemctl restart httpd

# Instalar MySQL/MariaDB
sudo dnf install mariadb105-server -y
sudo systemctl start mariadb
sudo systemctl enable mariadb

# Configurar MySQL
sudo mysql_secure_installation
```

#### Para Ubuntu 22.04:
```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Apache
sudo apt install apache2 -y

# Instalar PHP
sudo apt install php libapache2-mod-php php-mysql -y

# Instalar MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

### 4.6 Paso 5: Crear Usuario MySQL (Seguridad)

```sql
-- Acceder a MySQL como root
sudo mysql -u root -p

-- Crear usuario específico para la aplicación
CREATE USER 'centro_user'@'localhost' IDENTIFIED BY 'ContraseñaSegura123!';

-- Crear base de datos
CREATE DATABASE centro_educativo CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;

-- Dar permisos SOLO a la base de datos necesaria
GRANT SELECT, INSERT, UPDATE, DELETE ON centro_educativo.* TO 'centro_user'@'localhost';

-- Aplicar cambios
FLUSH PRIVILEGES;
EXIT;
```

### 4.7 Paso 6: Subir Archivos del Proyecto

```bash
# Opción 1: SCP (desde tu ordenador local)
scp -i mi-clave.pem -r /ruta/proyecto/* ec2-user@<IP>:/var/www/html/

# Opción 2: Git (si el proyecto está en repositorio)
cd /var/www/html
sudo git clone <URL-REPOSITORIO> .

# Opción 3: SFTP con FileZilla
# Usar la clave .pem en la configuración del sitio
```

### 4.8 Paso 7: Configurar Permisos

```bash
# Dar permisos al directorio web
sudo chown -R apache:apache /var/www/html/  # Amazon Linux
sudo chown -R www-data:www-data /var/www/html/  # Ubuntu

# Permisos de archivos
sudo chmod -R 755 /var/www/html/
```

### 4.9 Paso 8: Importar Base de Datos

```bash
# Importar el script SQL
mysql -u centro_user -p centro_educativo < /var/www/html/base_datos.sql
```

### 4.10 Paso 9: Configurar Conexión PHP

Editar el archivo `includes/conexion.php`:

```php
$servidor = "localhost";
$usuario = "centro_user";           // Usuario creado
$password = "ContraseñaSegura123!"; // Contraseña segura
$baseDatos = "centro_educativo";
```

### 4.11 Paso 10: Verificar Funcionamiento

1. Abrir navegador
2. Acceder a: `http://<IP-PUBLICA-EC2>/`
3. Verificar que se muestra la página principal
4. Probar todas las funcionalidades CRUD

---

## 5. Medidas de Seguridad

### 5.1 Nivel de Red (AWS Security Groups)
- ✅ Puerto SSH (22) restringido a IP específica
- ✅ Solo puertos 80/443 abiertos al público
- ✅ Puerto MySQL (3306) NO expuesto a Internet

### 5.2 Nivel de Base de Datos
- ✅ Usuario MySQL específico (no root)
- ✅ Contraseña segura con caracteres especiales
- ✅ Permisos limitados a la base de datos del proyecto
- ✅ Solo operaciones necesarias (SELECT, INSERT, UPDATE, DELETE)

### 5.3 Nivel de Aplicación
- ✅ Archivo de conexión separado en `/includes/`
- ✅ Sanitización de datos con `limpiarDatos()`
- ✅ Uso de consultas preparadas (prepared statements)
- ✅ Validación de formularios en cliente (JavaScript)
- ✅ Escape de caracteres especiales con `htmlspecialchars()`
- ✅ Soft delete en lugar de eliminación física

### 5.4 Buenas Prácticas Adicionales
- Charset UTF-8 configurado correctamente
- Mensajes de error genéricos en producción
- Cierre de conexiones al finalizar cada página

---

## 6. Acceso a la Aplicación

### 6.1 Entorno Local (XAMPP)
1. Copiar proyecto a `C:\xampp\htdocs\centro_educativo\`
2. Iniciar Apache y MySQL desde XAMPP
3. Importar `base_datos.sql` en phpMyAdmin
4. Acceder a: `http://localhost/centro_educativo/`

### 6.2 Entorno AWS (Producción)
- **URL**: `http://<IP-PUBLICA-EC2>/`
- **Acceso SSH**: `ssh -i clave.pem ec2-user@<IP-PUBLICA>`

---

## 7. Posibles Mejoras Futuras

### 7.1 Funcionalidad
- [ ] Implementar sistema de autenticación (login/logout)
- [ ] Añadir gestión de asignaturas
- [ ] Crear módulo de matrículas detallado
- [ ] Implementar exportación a PDF/Excel
- [ ] Añadir paginación para listados grandes

### 7.2 Seguridad
- [ ] Implementar HTTPS con certificado SSL (Let's Encrypt)
- [ ] Añadir protección CSRF en formularios
- [ ] Implementar rate limiting para prevenir ataques
- [ ] Cifrado de datos sensibles

### 7.3 Interfaz
- [ ] Añadir modo oscuro
- [ ] Implementar gráficos estadísticos
- [ ] Mejorar experiencia móvil
- [ ] Añadir notificaciones en tiempo real

### 7.4 Arquitectura
- [ ] Migrar a arquitectura MVC
- [ ] Implementar sistema de caché
- [ ] Añadir API REST
- [ ] Configurar backups automáticos

---

## Conclusión

Este proyecto demuestra la implementación completa de una aplicación web CRUD utilizando las tecnologías fundamentales del desarrollo web (HTML, CSS, JavaScript, PHP, MySQL) y su despliegue en un entorno cloud (AWS EC2).

La aplicación cumple con todos los requisitos académicos del módulo IAW, incluyendo:
- Base de datos relacional con 3 tablas relacionadas
- CRUD completo sobre la tabla principal
- Validaciones en cliente con JavaScript
- Diseño responsive y profesional
- Documentación detallada del despliegue en AWS
- Implementación de medidas de seguridad básicas

---

**Proyecto desarrollado para IAW - CFGS ASIR**

*Fecha: Enero 2026*
