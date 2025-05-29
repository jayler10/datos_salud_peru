CREATE DATABASE ingresantes;
USE ingresantes;
CREATE TABLE IF NOT EXISTS ingreso (
    idIngreso 		INT AUTO_INCREMENT PRIMARY KEY,
    sexo 			VARCHAR(20) NOT NULL,
	fechaNacimiento DATE,
	provincia		VARCHAR(250) NULL,
	modalidad		VARCHAR(250) NOT NULL
);

