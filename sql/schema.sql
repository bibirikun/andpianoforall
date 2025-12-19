SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS Secuencia_Notas;
DROP TABLE IF EXISTS Canciones;
DROP TABLE IF EXISTS Secuencias;
DROP TABLE IF EXISTS Usuarios;
DROP TABLE IF EXISTS Notas;

CREATE TABLE Notas (
    ID_Nota INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(10) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Notas (Nombre) VALUES
('c3'), ('c-3'),
('d3'), ('d-3'),
('e3'),
('f3'), ('f-3'),
('g3'), ('g-3'),
('a4'), ('a-4'),
('b4'),
('c4'), ('c-4'),
('d4'), ('d-4'),
('e4'),
('f4'), ('f-4'),
('g4'), ('g-4'),
('a5'), ('a-5'),
('b5');

CREATE TABLE Secuencias (
    ID_Secuencia BIGINT AUTO_INCREMENT PRIMARY KEY,
    Usuario VARCHAR(50) NOT NULL,
    Fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Usuarios (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    Usuario VARCHAR(50) NOT NULL UNIQUE,
    Correo VARCHAR(255) NULL,
    Contraseña VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Secuencia_Notas (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    ID_Secuencia BIGINT,
    ID_Nota INT,
    Posicion INT,
    TimestampMs INT NOT NULL DEFAULT 0,
    DuracionMs INT NOT NULL DEFAULT 300,

    INDEX idx_secuencia (ID_Secuencia),
    INDEX idx_nota (ID_Nota),

    FOREIGN KEY (ID_Secuencia) REFERENCES Secuencias(ID_Secuencia),
    FOREIGN KEY (ID_Nota) REFERENCES Notas(ID_Nota)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Canciones (
    ID_Cancion INT AUTO_INCREMENT PRIMARY KEY,
    Titulo VARCHAR(100) NOT NULL UNIQUE,
    ID_Secuencia BIGINT NOT NULL,
    ID_Usuario INT,
    FechaCreacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
