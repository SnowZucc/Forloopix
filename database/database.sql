CREATE DATABASE appg1d_projetcommun;
USE appg1d_projetcommun;

CREATE TABLE Utilisateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL ,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    type ENUM('agent', 'manager', 'admin'),
    etat ENUM('actif', 'inactif')
);

CREATE TABLE Messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_expediteur INT NOT NULL ,
    id_destinataire INT ,
    contenu TEXT NOT NULL ,
    date DATETIME NOT NULL ,

    FOREIGN KEY (id_expediteur) REFERENCES Utilisateurs(id),
    FOREIGN KEY (id_destinataire) REFERENCES Utilisateurs(id)
);

CREATE TABLE Documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    proprietaire INT ,
    lien VARCHAR(255) NOT NULL,
    type ENUM('texte', 'image', 'video', 'pdf') NOT NULL ,

    FOREIGN KEY (proprietaire) REFERENCES Utilisateurs(id)
);

-- Exemples d'utilisation
-- Utilisateurs mdp : motdepassehash123

INSERT INTO Utilisateurs (nom, prenom, email, mot_de_passe, type)
VALUES ('Bonbeurre', 'Jean', 'jean.bonbeurre@start-hut.com', '$2y$10$8cFccmfc6dbRvqwgLvY72OMvXu42pzDmTtT2s68SFhgu3X9Xxn5Dq', 'manager');
INSERT INTO Utilisateurs (nom, prenom, email, mot_de_passe, type)
VALUES ('Echtebez', 'Philippe', 'philippe.echtebez@start-hut.com', '$2y$10$8cFccmfc6dbRvqwgLvY72OMvXu42pzDmTtT2s68SFhgu3X9Xxn5Dq', 'agent');
INSERT INTO Utilisateurs (nom, prenom, email, mot_de_passe, type)
VALUES ('Alexandre', 'Louis', 'louis.alexandre@tutanota.com', '$2y$10$fcMAF5eBfJ5ipRcwafCRAepegQ2Lz0RlaHK4nDaMJmNTC36DPb.6G', 'admin');