#!/bin/bash

# ============================================================
# CENTRO EDUCATIVO - SCRIPT DE CONFIGURACIÓN EC2 (UBUNTU)
# ============================================================

# 1. Actualizar el sistema
sudo apt update && sudo apt upgrade -y

# 2. Instalar Apache, PHP y MySQL
sudo apt install apache2 php libapache2-mod-php php-mysql mysql-server -y

# 3. Habilitar mod_rewrite de Apache (útil para URLs amigables)
sudo a2enmod rewrite
sudo systemctl restart apache2

# 4. Configurar la base de datos
# Crear el usuario y la base de datos (usando los valores por defecto del proyecto)
sudo mysql -e "CREATE DATABASE IF NOT EXISTS centro_educativo CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'admin_centro'@'localhost' IDENTIFIED BY 'Centro123!';"
sudo mysql -e "GRANT ALL PRIVILEGES ON centro_educativo.* TO 'admin_centro'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# 5. Configurar variables de entorno para PHP en Apache
# Esto permite que conexion.php lea las credenciales
sudo bash -c 'cat <<EOF >> /etc/apache2/envvars
export DB_HOST="localhost"
export DB_NAME="centro_educativo"
export DB_USER="admin_centro"
export DB_PASS="Centro123!"
EOF'

# Reiniciar Apache para cargar las variables
sudo systemctl restart apache2

echo "============================================================"
echo " SERVIDOR CONFIGURADO CORRECTAMENTE"
echo "============================================================"
echo "Siguiente paso: Importar la base de datos y subir los archivos."
echo "Comando para importar: sudo mysql centro_educativo < base_datos.sql"
echo "============================================================"
