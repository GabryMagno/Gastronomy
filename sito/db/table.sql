USE gastronomia;

-- Elimina tabelle in ordine per evitare vincoli interrotti
DROP TABLE IF EXISTS prenotazioni_degustazioni;
DROP TABLE IF EXISTS prenotazioni;
DROP TABLE IF EXISTS valutazioni;
DROP TABLE IF EXISTS preferiti;
DROP TABLE IF EXISTS prodotto_ingredienti;
DROP TABLE IF EXISTS suggerimenti;
DROP TABLE IF EXISTS degustazioni;
DROP TABLE IF EXISTS prodotti;
DROP TABLE IF EXISTS ingredienti;
DROP TABLE IF EXISTS utenti;

CREATE TABLE utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(16) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nome VARCHAR(15) NOT NULL,
    cognome VARCHAR(15) NOT NULL,
    data_nascita DATE NOT NULL,
    data_iscrizione TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    url_immagine VARCHAR(255)
);

CREATE TABLE prodotti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    categoria enum('antipasto', 'primo', 'secondo', 'contorno','dolce') NOT NULL,
    unita enum('porzione','vaschetta','kg', 'pezzo') NOT NULL,
    min_prenotabile SMALLINT NOT NULL CHECK(min_prenotabile > 0),
    max_prenotabile SMALLINT NOT NULL CHECK(max_prenotabile > min_prenotabile),
    descrizione TEXT NOT NULL,
    prezzo DECIMAL(10, 2) NOT NULL,
    isDisponibile BOOLEAN NOT NULL,
    url_immagine VARCHAR(255) NOT NULL
);

CREATE TABLE ingredienti (
    nome VARCHAR(60) PRIMARY KEY,
    isCeliaco BOOLEAN NOT NULL,
    isVegano BOOLEAN NOT NULL,
    isVegetariano BOOLEAN NOT NULL
);

CREATE TABLE prodotto_ingredienti(
    id_prodotto INT NOT NULL,
    ingrediente VARCHAR(60) NOT NULL,
    quanto_basta BOOLEAN NOT NULL,
    quantita SMALLINT CHECK(quantita > 0),
    unita_misura enum ('g', 'ml', 'num_el'),
    CHECK((quanto_basta IS TRUE AND quantita IS NULL AND unita_misura IS NULL) OR (quanto_basta IS FALSE AND quantita IS NOT NULL AND unita_misura IS NOT NULL)),
    PRIMARY KEY(id_prodotto, ingrediente),
    FOREIGN KEY (id_prodotto) REFERENCES prodotti(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ingrediente) REFERENCES ingredienti(nome) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE degustazioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_prodotto INT NOT NULL,
    descrizione TEXT NOT NULL,
    disponibilita_persone INT NOT NULL,
    data_inizio DATE NOT NULL,
    data_fine DATE NOT NULL,
    prezzo DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_prodotto) REFERENCES prodotti(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE prenotazioni_degustazioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_degustazione INT NOT NULL,
    id_cliente INT NOT NULL,
    numero_persone INT NOT NULL,
    data_prenotazione DATE NOT NULL,
    data_scelta DATE NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES utenti(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_degustazione) REFERENCES degustazioni(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE preferiti (
    id_utente INT,
    id_prodotto INT,
    PRIMARY KEY (id_utente, id_prodotto),
    FOREIGN KEY (id_utente) REFERENCES utenti(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_prodotto) REFERENCES prodotti(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE prenotazioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utente INT NOT NULL,
    id_prodotto INT NOT NULL,
    quantita INT NOT NULL,
    data_ritiro DATE NOT NULL,
    FOREIGN KEY (id_utente) REFERENCES utenti(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_prodotto) REFERENCES prodotti(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE valutazioni (
    id_utente INT,
    id_prodotto INT,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    voto INT NOT NULL CHECK (voto >= 1 AND voto <= 5),
    commento TEXT NOT NULL CHECK (CHAR_LENGTH(commento) BETWEEN 30 AND 300),
    PRIMARY KEY (id_utente, id_prodotto),
    FOREIGN KEY (id_utente) REFERENCES utenti(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_prodotto) REFERENCES prodotti(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE suggerimenti (
    id_suggerimento INT AUTO_INCREMENT PRIMARY KEY,
    id_utente INT,
    data_inserimento DATE NOT NULL,
    suggerimento TEXT NOT NULL CHECK (CHAR_LENGTH(suggerimento) BETWEEN 30 AND 300),
    FOREIGN KEY (id_utente) REFERENCES utenti(id) ON DELETE CASCADE ON UPDATE CASCADE
);