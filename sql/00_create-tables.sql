-- Acceder a la BD
USE proyectodb;

-- Crear tabla usuarios (referente a los médicos y programadores/gestores de la web, donde
-- si es médico se debe introducir el numero de colegiado, y si no el de empleado)
CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    rango VARCHAR(255) NOT NULL,
    num_identif VARCHAR(20) NOT NULL UNIQUE,
    passwd VARCHAR(255) NOT NULL,
    fecha_alta DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Crear tabla vacuna
CREATE TABLE vacuna (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL,
    nombre_largo VARCHAR(100) NOT NULL,
    fabricante VARCHAR(255) NOT NULL,
    num_dosis INT(10) NOT NULL,
    tiempo_minimo INT,
    tiempo_maximo INT
);

-- Crear tabla paciente
CREATE TABLE pacientes (
    DNI CHAR(9) PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    nacimiento DATE NOT NULL
);

-- Crear tabla con las citas de vacunación
CREATE TABLE citas (
    id_cita INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    DNI CHAR(9) NOT NULL,
    num_dosis INT(10) NOT NULL,
    centro_vacunacion VARCHAR(255) NOT NULL,
    fecha DATETIME NOT NULL,
    FOREIGN KEY (DNI) REFERENCES pacientes(DNI),
    FOREIGN KEY (centro_vacunacion) REFERENCES centros(abreviatura)
);

-- Crear tabla con los registros de los vacunados
CREATE TABLE registro_vacunados (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    DNI CHAR(9) NOT NULL,
    num_dosis INT(10) NOT NULL,
    fabricante VARCHAR(255) NOT NULL,
    num_lote INT NOT NULL,
    centro_vacunacion VARCHAR(255) NOT NULL,
    fecha_vacunacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (DNI) REFERENCES pacientes(DNI)
);

-- Crear tabla con las citas de vacunación
CREATE TABLE centros (
   id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
   nombre VARCHAR(255) NOT NULL,
   abreviatura VARCHAR(255) NOT NULL,
   localidad VARCHAR(255) NOT NULL
);