DELETE FROM prodotto_ingredienti;
DELETE FROM ingredienti;
DELETE FROM preferiti;
DELETE FROM valutazioni;
DELETE FROM suggerimenti;
DELETE FROM prenotazioni_degustazioni;
DELETE FROM prenotazioni;
DELETE FROM degustazioni;
DELETE FROM prodotti;
DELETE FROM utenti;

ALTER TABLE prodotti AUTO_INCREMENT = 1;
ALTER TABLE valutazioni AUTO_INCREMENT = 1;
ALTER TABLE degustazioni AUTO_INCREMENT = 1;
ALTER TABLE prenotazioni_degustazioni AUTO_INCREMENT = 1;
ALTER TABLE suggerimenti AUTO_INCREMENT = 1;
ALTER TABLE prenotazioni AUTO_INCREMENT = 1;
ALTER TABLE utenti AUTO_INCREMENT = 1;

INSERT INTO prodotti 
(nome, categoria, unita, min_prenotabile, max_prenotabile, descrizione, prezzo, isDisponibile, url_immagine) 
VALUES
-- ANTIPASTI
('Bruschetta Classica', 'antipasto', 'pezzo', 1, 10, 'Pane casereccio con pomodoro fresco, basilico e olio EVO', 2.50, TRUE, 'assets/img/prodotti/bruschetta.webp'),
('Tagliere Salumi', 'antipasto', 'porzione', 1, 5, 'Selezione di salumi nostrani con pane fresco', 8.00, TRUE, 'assets/img/prodotti/tagliere_salumi.webp'),
('Insalata di Mare', 'antipasto', 'porzione', 1, 5, 'Gamberi, polpo e calamari conditi con limone e prezzemolo', 9.50, TRUE, 'assets/img/prodotti/insalata_mare.webp'),
('Olive Ascolane', 'antipasto', 'pezzo', 3, 20, 'Olive verdi ripiene di carne e fritte dorate', 0.30, TRUE, 'assets/img/prodotti/olive_ascolane.webp'),
('Parmigiana Mignon', 'antipasto', 'vaschetta', 1, 5, 'Vaschetta con mini porzioni di parmigiana di melanzane', 4.50, FALSE, 'assets/img/prodotti/parmigiana.webp'),
('Frittata di Zucchine', 'antipasto', 'pezzo', 1, 8, 'Frittata morbida alle zucchine fresche', 3.00, TRUE, 'assets/img/prodotti/frittata_zucchine.webp'),
('Insalata Russa', 'antipasto', 'kg', 1, 5, 'Insalata russa classica, prezzo al kg', 12.90, TRUE, 'assets/img/prodotti/insalata_russa.webp'),

-- PRIMI
('Lasagna alla Bolognese', 'primo', 'porzione', 1, 6, 'Sfoglia fresca con ragù di carne, besciamella e parmigiano', 7.50, TRUE, 'assets/img/prodotti/lasagna.webp'),
('Risotto ai Funghi', 'primo', 'porzione', 1, 6, 'Riso carnaroli con funghi porcini freschi', 8.00, TRUE, 'assets/img/prodotti/risotto_funghi.webp'),
('Trofie al Pesto', 'primo', 'porzione', 1, 6, 'Pasta fresca con pesto genovese e patate', 7.00, TRUE, 'assets/img/prodotti/trofie_pesto.webp'),
('Canneloni Ricotta e Spinaci', 'primo', 'porzione', 1, 6, 'Crespelle ripiene di ricotta fresca e spinaci', 7.50, FALSE, 'assets/img/prodotti/cannelloni.webp'),
('Spaghetti Carbonara', 'primo', 'porzione', 1, 6, 'Pasta con guanciale, uova e pecorino romano', 6.50, TRUE, 'assets/img/prodotti/carbonara.webp'),
('Gnocchi alla Sorrentina', 'primo', 'porzione', 1, 6, 'Gnocchi al forno con pomodoro e mozzarella', 7.00, TRUE, 'assets/img/prodotti/gnocchi_sorrentina.webp'),

-- SECONDI
('Pollo Arrosto', 'secondo', 'pezzo', 1, 4, 'Pollo intero arrosto con erbe aromatiche', 12.00, TRUE, 'assets/img/prodotti/pollo_arrosto.webp'),
('Scaloppine al Limone', 'secondo', 'porzione', 1, 6, 'Fettine di vitello al limone', 9.00, TRUE, 'assets/img/prodotti/scaloppine.webp'),
('Baccalà alla Vicentina', 'secondo', 'porzione', 1, 6, 'Filetto di baccalà stufato con cipolle e latte', 11.00, FALSE, 'assets/img/prodotti/baccala.webp'),
('Involtini di Carne', 'secondo', 'pezzo', 1, 8, 'Carne di vitello ripiena e cotta al forno', 4.00, TRUE, 'assets/img/prodotti/involtini.webp'),
('Arrosto di Tacchino', 'secondo', 'porzione', 1, 6, 'Fettine di tacchino al forno con erbe aromatiche', 8.50, TRUE, 'assets/img/prodotti/arrosto_tacchino.webp'),
('Polpette al Sugo', 'secondo', 'pezzo', 2, 12, 'Polpette di carne in salsa di pomodoro', 1.50, TRUE, 'assets/img/prodotti/polpette.webp'),
('Arrosto di Maiale', 'secondo', 'kg', 1, 5, 'Arrosto di maiale cotto lentamente, prezzo al kg', 24.00, TRUE, 'assets/img/prodotti/arrosto_maiale.webp'),

-- CONTORNI
('Patate al Forno', 'contorno', 'porzione', 1, 6, 'Patate al forno croccanti con rosmarino', 4.00, TRUE, 'assets/img/prodotti/patate_forno.webp'),
('Verdure Grigliate', 'contorno', 'porzione', 1, 6, 'Misto di zucchine, melanzane, pomodori, asparagi e peperoni grigliati', 4.50, TRUE, 'assets/img/prodotti/verdure_grigliate.webp'),
('Insalata Mista', 'contorno', 'porzione', 1, 6, 'Insalata fresca con pomodori, cetrioli e lattuga', 3.50, TRUE, 'assets/img/prodotti/insalata.webp'),
('Spinaci al Burro', 'contorno', 'porzione', 1, 6, 'Spinaci freschi saltati al burro', 3.50, TRUE, 'assets/img/prodotti/spinaci.webp'),
('Caponata Siciliana', 'contorno', 'porzione', 1, 6, 'Melanzane, peperoni e cipolle in agrodolce', 5.00, TRUE, 'assets/img/prodotti/caponata.webp'),
('Zucchine Trifolate', 'contorno', 'porzione', 1, 6, 'Zucchine saltate in padella con aglio e prezzemolo', 4.00, FALSE, 'assets/img/prodotti/zucchine.webp'),
('Insalata di Farro', 'contorno', 'kg', 1, 4, 'Insalata di farro con verdure di stagione', 15.00, TRUE, 'assets/img/prodotti/insalata_farro.webp'),

-- DOLCI
('Tiramis&ugrave;', 'dolce', 'porzione', 1, 6, 'Classico dolce al cucchiaio con savoiardi, caffè e mascarpone', 4.50, TRUE, 'assets/img/prodotti/tiramisu.webp'),
('Panna Cotta', 'dolce', 'porzione', 1, 6, 'Panna cotta con topping di frutti di bosco', 4.00, TRUE, 'assets/img/prodotti/pannacotta.webp'),
('Cassata Siciliana', 'dolce', 'porzione', 1, 6, 'Dolce tipico con ricotta, pan di spagna e canditi', 5.00, FALSE, 'assets/img/prodotti/cassata.webp'),
('Crostata alla Marmellata', 'dolce', 'pezzo', 1, 8, 'Frolla con confettura di albicocca', 3.00, TRUE, 'assets/img/prodotti/crostata.webp'),
('Bign&egrave; alla Crema', 'dolce', 'pezzo', 2, 12, 'Pasta <span lang="fr">choux</span> ripiena di crema pasticcera', 1.50, TRUE, 'assets/img/prodotti/bigne.webp'),
('Biscotti Artigianali', 'dolce', 'kg', 1, 3, 'Selezione di biscotti di frolla artigianali', 18.00, TRUE, 'assets/img/prodotti/biscotti.webp'),
('<span lang="en">Cheesecake</span> ai Frutti Rossi', 'dolce', 'porzione', 1, 6, '<span lang="en">Cheesecake</span> con topping ai frutti rossi', 4.50, TRUE, 'assets/img/prodotti/cheesecake.webp');


-- INGREDIENTI
INSERT INTO ingredienti VALUES 
("Semi di Sesamo",1,1,1),
("Farro",0,1,1),
("Noci",1,1,1),
("Finocchio",1,1,1),
("Succo di Limone",1,1,1),
("Mele",1,1,1),
("Patata Rossa",1,1,1),
("Uova",1,0,1),
("Semola",0,1,1),
("Farina 00",0,1,1),
("Sale fino",1,1,1),
("Sale grosso",1,1,1),
("Sale",1,1,1),
("Passata di pomodoro",1,1,1),
("Aglio",1,1,1),
("Basilico",1,1,1),
("Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,1,1),
("Mozzarella",1,0,1),
("Parmiggiano Reggiano <abbr title=\"Denominazione di Origine Protetta\">DOP</abbr>",1,0,0),
("Pane casereccio",0,1,1),
("Pomodoro fresco",1,1,1),
("Gamberi",1,0,0),
("Polpo",1,0,0),
("Calamari",1,0,0),
("Limone",1,1,1),
("Prezzemolo",1,1,1),
("Olive verdi",1,1,1),
("Melanzane",1,1,1),
("Patate",1,1,1),
("Carote",1,1,1),
("Piselli",1,1,1),
("Maionese",1,0,1),
("Ragù di carne",1,0,0),
("Besciamella",0,0,1),
("Riso Carnaroli",1,1,1),
("Funghi porcini",1,1,1),
("Pesto alla genovese",1,0,1),
("Spinaci",1,1,1),
("Guanciale",0,0,0),
("Pecorino Romano <abbr title=\"Denominazione di Origine Protetta\">DOP</abbr>",1,0,0),
("Pomodori pelati",1,1,1),
("Pollo intero",1,0,0),
("Erbe aromatiche miste",1,1,1),
("Fettine di vitello",1,0,0),
("Filetto di baccalà",1,0,0),
("Cipolle",1,1,1),
("Latte intero",1,0,1),
("Carne di vitello",1,0,0),
("Fettine di tacchino",1,0,0),
("Carne macinata mista",1,0,0),
("Patate novelle",1,1,1),
("Rosmarino",1,1,1),
("Zucchine",1,1,1),
("Pomodori",1,1,1),
("Asparagi",1,1,1),
("Peperoni",1,1,1),
("Lattuga",1,1,1),
("Cetrioli",1,1,1),
("Burro",1,0,1),
("Aceto",1,1,1),
("Panna fresca",1,0,1),
("Savoiardi",0,0,1),
("Caffè espresso",1,1,1),
("Mascarpone",1,0,1),
("Frutti di bosco misti",1,1,1),
("Ricotta",1,0,1),
("Pan di spagna",0,0,1),
("Canditi misti",1,0,1),
("Confettura di albicocca",1,1,1),
("Crema pasticcera",0,0,1),
("Frolla artigianale",0,0,1),
("Formaggio spalmabile",1,0,1),
("Prosciutto crudo",1,0,0),
("Prosciutto cotto",1,0,0),
("Salame",1,0,0),
("Mortadella",1,0,0),
("Rucola",1,1,1),
("Aceto balsamico",1,1,1),
("Miele",1,0,1),
("Pepe nero",1,1,1),
("Zucchero",1,1,1),
("Cannella in polvere",1,1,1),
("Cioccolato fondente",1,0,1),
("Vaniglia",1,1,1),
("Sfoglia fresca per lasagna",0,0,1),
("Brodo vegetale",1,1,1),
("Zenzero",1,1,1),
("Trofie",0,1,1),
("Fagiolini",1,1,1),
("Crespelle",0,0,1),
("Spaghetti",0,1,1),
("Lonza di maiale",1,0,0),
("Cacao amaro in polvere",1,1,1),
("Gelatina in fogli",1,0,0),
("Origano",1,1,1);

-- PRODOTTI_INGREDIENTI
INSERT INTO prodotto_ingredienti VALUES
-- Bruschetta Classica
(1,"Pane casereccio",0,250,"g"),
(1,"Pomodori",0,300,"g"),
(1,"Aglio",0,1,"num_el"),
(1,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(1,"Sale",1,null,null),
(1,"Origano",0,3000,"g"),
-- Tagliere di salumi
(2,"Prosciutto crudo",0,100,"g"),
(2,"Prosciutto cotto",0,100,"g"),
(2,"Salame",0,100,"g"),
(2,"Mortadella",0,100,"g"),
(2,"Pane casereccio",0,200,"g"),
(2,"Rucola",1,null,null),
(2,"Aceto balsamico",1,null,null),
(2,"Olive verdi",0,50,"g"),
-- Insalata di mare
(3,"Gamberi",0,150,"g"),
(3,"Polpo",0,150,"g"),
(3,"Calamari",0,150,"g"),
(3,"Limone",1,null,null),
(3,"Prezzemolo",1,null,null),
(3,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(3,"Sale fino",1,null,null),
-- Olive Ascolane
(4,"Olive verdi",0,150,"g"),
(4,"Carne macinata mista",0,100,"g"),
(4,"Uova",0,1,"num_el"),
(4,"Pane casereccio",0,50,"g"),
(4,"Farina 00",0,50,"g"),
(4,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(4,"Sale fino",1,null,null),
(4,"Pepe nero",1,null,null),
-- Parmigiana Mignon
(5,"Melanzane",0,300,"g"),
(5,"Passata di pomodoro",0,200,"g"),
(5,"Mozzarella",0,150,"g"),
(5,"Parmiggiano Reggiano <abbr title=\"Denominazione di Origine Protetta\">DOP</abbr>",0,50,"g"),
(5,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(5,"Basilico",1,null,null),
(5,"Sale fino",1,null,null),
-- Frittata di Zucchine
(6,"Uova",0,3,"num_el"),
(6,"Zucchine",0,200,"g"),
(6,"Parmiggiano Reggiano <abbr title=\"Denominazione di Origine Protetta\">DOP</abbr>",0,30,"g"),
(6,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(6,"Sale fino",1,null,null),
(6,"Pepe nero",1,null,null),
-- Insalata Russa
(7,"Patate",0,200,"g"),
(7,"Carote",0,100,"g"),
(7,"Piselli",0,100,"g"),
(7,"Maionese",0,150,"g"),
(7,"Sale fino",1,null,null),
(7,"Pepe nero",1,null,null),
-- Lasagna alla Bolognese
(8,"Sfoglia fresca per lasagna",0,200,"g"),
(8,"Ragù di carne",0,150,"g"),
(8,"Besciamella",0,100,"g"),
(8,"Parmiggiano Reggiano <abbr title=\"Denominazione di Origine Protetta\">DOP</abbr>",0,50,"g"),
(8,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(8,"Sale fino",1,null,null),
(8,"Pepe nero",1,null,null),
-- Risotto ai Funghi
(9,"Riso Carnaroli",0,180,"g"),
(9,"Funghi porcini",0,100,"g"),
(9,"Brodo vegetale",0,500,"ml"),
(9,"Parmiggiano Reggiano <abbr title=\"Denominazione di Origine Protetta\">DOP</abbr>",0,30,"g"),
(9,"Burro",0,20,"g"),
(9,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(9,"Aglio",0,1,"num_el"),
(9,"Prezzemolo",1,null,null),
(9,"Sale fino",1,null,null),
-- Trofie al Pesto
(10,"Trofie",0,180,"g"),
(10,"Pesto alla genovese",0,100,"g"),
(10,"Patate",0,100,"g"),
(10,"Fagiolini",0,100,"g"),
(10,"Parmiggiano Reggiano <abbr title=\"Denominazione di Origine Protetta\">DOP</abbr>",0,30,"g"),
(10,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(10,"Sale fino",1,null,null),
-- Canneloni Ricotta e Spinaci
(11,"Crespelle",0,200,"g"),
(11,"Ricotta",0,150,"g"),
(11,"Spinaci",0,100,"g"),
(11,"Passata di pomodoro",0,200,"g"),
(11,"Mozzarella",0,100,"g"),
(11,"Parmiggiano Reggiano <abbr title=\"Denominazione di Origine Protetta\">DOP</abbr>",0,50,"g"),
(11,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(11,"Aglio",0,1,"num_el"),
(11,"Basilico",1,null,null),
(11,"Sale fino",1,null,null),
-- Spaghetti Carbonara
(12,"Spaghetti",0,180,"g"),
(12,"Guanciale",0,100,"g"),
(12,"Uova",0,2,"num_el"),
(12,"Pecorino Romano <abbr title=\"Denominazione di Origine Protetta\">DOP</abbr>",0,50,"g"),
(12,"Pepe nero",1,null,null),
(12,"Sale fino",1,null,null),
-- Gnocchi alla Sorrentina
(13,"Patata Rossa",0,3000,"g"),
(13,"Uova",0,1,"num_el"),
(13,"Semola",1,null,null),
(13,"Farina 00",0,300,"g"),
(13,"Sale fino",1,null,null),
(13,"Passata di pomodoro",0,600,"g"),
(13,"Aglio",0,1,"num_el"),
(13,"Basilico",0,6,"num_el"),
(13,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(13,"Mozzarella",0,250,"g"),
(13,"Parmiggiano Reggiano <abbr title=\"Denominazione di Origine Protetta\">DOP</abbr>",0,70,"g"),
-- Pollo Arrosto
(14,"Pollo intero",0,1200,"g"),
(14,"Erbe aromatiche miste",1,null,null),
(14,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(14,"Sale grosso",1,null,null),
(14,"Pepe nero",1,null,null),
-- Scaloppine al Limone
(15,"Fettine di vitello",0,150,"g"),
(15,"Limone",1,null,null),
(15,"Burro",0,20,"g"),
(15,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(15,"Prezzemolo",1,null,null),
(15,"Sale fino",1,null,null),
(15,"Pepe nero",1,null,null),
-- Baccalà alla Vicentina
(16,"Filetto di baccalà",0,200,"g"),
(16,"Cipolle",0,100,"g"),
(16,"Latte intero",0,200,"ml"),
(16,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(16,"Prezzemolo",1,null,null),
(16,"Sale fino",1,null,null),
(16,"Pepe nero",1,null,null),
-- Involtini di Carne
(17,"Carne di vitello",0,150,"g"),
(17,"Prosciutto crudo",0,50,"g"),
(17,"Parmiggiano Reggiano <abbr title=\"Denominazione di Origine Protetta\">DOP</abbr>",0,30,"g"),
(17,"Pane casereccio",0,50,"g"),
(17,"Uova",0,1,"num_el"),
(17,"Farina 00",0,50,"g"),
(17,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(17,"Sale fino",1,null,null),
(17,"Pepe nero",1,null,null),
-- Arrosto di Tacchino
(18,"Fettine di tacchino",0,150,"g"),
(18,"Erbe aromatiche miste",1,null,null),
(18,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(18,"Sale grosso",1,null,null),
(18,"Pepe nero",1,null,null),
-- Polpette al Sugo
(19,"Carne macinata mista",0,100,"g"),
(19,"Uova",0,1,"num_el"),
(19,"Pane casereccio",0,50,"g"),
(19,"Parmiggiano Reggiano <abbr title=\"Denominazione di Origine Protetta\">DOP</abbr>",0,30,"g"),
(19,"Passata di pomodoro",0,200,"g"),
(19,"Aglio",0,1,"num_el"),
(19,"Basilico",1,null,null),
(19,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(19,"Sale fino",1,null,null),
(19,"Pepe nero",1,null,null),
-- Arrosto di Maiale
(20,"Lonza di maiale",0,1000,"g"),
(20,"Erbe aromatiche miste",1,null,null),
(20,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(20,"Sale grosso",1,null,null),
(20,"Pepe nero",1,null,null),
-- Patate al Forno
(21,"Patate novelle",0,300,"g"),
(21,"Rosmarino",1,null,null),
(21,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(21,"Sale grosso",1,null,null),
(21,"Pepe nero",1,null,null),
-- Verdure Grigliate
(22,"Zucchine",0,100,"g"),
(22,"Melanzane",0,100,"g"),
(22,"Pomodori",0,100,"g"),
(22,"Asparagi",0,100,"g"),
(22,"Peperoni",0,100,"g"),
(22,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(22,"Sale fino",1,null,null),
(22,"Pepe nero",1,null,null),
-- Insalata Mista
(23,"Noci",0,8,"num_el"),
(23,"Finocchio",0,1,"num_el"),
(23,"Mele",0,2,"num_el"),
(23,"Semi di Sesamo",0,30,"g"),
(23,"Succo di limone",1,null,null),
(23,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(23,"Sale fino",1,null,null),
(23,"Pepe nero",1,null,null),
-- Spinaci al Burro
(24,"Spinaci",0,200,"g"),
(24,"Burro",0,20,"g"),
(24,"Aglio",0,1,"num_el"),
(24,"Sale fino",1,null,null),
(24,"Pepe nero",1,null,null),
-- Caponata Siciliana
(25,"Melanzane",0,200,"g"),
(25,"Peperoni",0,100,"g"),
(25,"Cipolle",0,100,"g"),
(25,"Aceto",0,50,"ml"),
(25,"Zucchero",0,20,"g"),
(25,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(25,"Sale fino",1,null,null),
(25,"Pepe nero",1,null,null),
-- Zucchine Trifolate
(26,"Zucchine",0,200,"g"),
(26,"Aglio",0,1,"num_el"),
(26,"Prezzemolo",1,null,null),
(26,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(26,"Sale fino",1,null,null),
(26,"Pepe nero",1,null,null),
-- Insalata di Farro
(27,"Farro",0,200,"g"),
(27,"Pomodori",0,100,"g"),
(27,"Cetrioli",0,100,"g"),
(27,"Peperoni",0,100,"g"),
(27,"Olio <abbr title=\"Extra Vergine di Oliva\">EVO</abbr>",1,null,null),
(27,"Succo di limone",1,null,null),
(27,"Sale fino",1,null,null),
(27,"Pepe nero",1,null,null),
-- Tiramisù
(28,"Savoiardi",0,100,"g"),
(28,"Caffè espresso",0,100,"ml"),
(28,"Mascarpone",0,150,"g"),
(28,"Uova",0,2,"num_el"),
(28,"Zucchero",0,50,"g"),
(28,"Cacao amaro in polvere",1,null,null),
-- Panna Cotta
(29,"Panna fresca",0,200,"ml"),
(29,"Zucchero",0,50,"g"),
(29,"Vaniglia",1,null,null),
(29,"Gelatina in fogli",0,5,"g"),
(29,"Frutti di bosco misti",0,100,"g"),
-- Cassata Siciliana
(30,"Ricotta",0,200,"g"),
(30,"Zucchero",0,100,"g"),
(30,"Pan di spagna",0,150,"g"),
(30,"Canditi misti",0,50,"g"),
(30,"Cioccolato fondente",0,50,"g"),
(30,"Vaniglia",1,null,null),
-- Crostata alla Marmellata
(31,"Frolla artigianale",0,200,"g"),
(31,"Confettura di albicocca",0,100,"g"),
(31,"Uova",0,1,"num_el"),
(31,"Zucchero",0,50,"g"),
(31,"Burro",0,50,"g"),
-- Bignè alla Crema
(32,"Frolla artigianale",0,100,"g"),
(32,"Crema pasticcera",0,150,"g"),
(32,"Zucchero",0,20,"g"),
(32,"Burro",0,30,"g"),
(32,"Uova",0,1,"num_el"),
-- Biscotti Artigianali
(33,"Frolla artigianale",0,300,"g"),
(33,"Zucchero",0,100,"g"),
(33,"Burro",0,100,"g"),
(33,"Uova",0,1,"num_el"),
(33,"Vaniglia",1,null,null),
-- Cheesecake ai Frutti Rossi
(34,"Frolla artigianale",0,300,"g"),
(34,"Formaggio spalmabile",0,200,"g"),
(34,"Panna fresca",0,100,"ml"),
(34,"Zucchero",0,100,"g"),
(34,"Uova",0,2,"num_el"),
(34,"Burro",0,50,"g"),
(34,"Frutti di bosco misti",0,100,"g"),
(34,"Vaniglia",1,null,null);

-- DEGUSTAZIONI
INSERT INTO degustazioni 
(id_prodotto, descrizione, disponibilita_persone, data_inizio, data_fine, prezzo) 
VALUES
-- FUTURE
(8, 'Lasagna alla Bolognese in degustazione: un assaggio della tradizione emiliana con sfoglia fresca e ragù ricco di sapore. Un piatto che rappresenta la convivialità e il gusto autentico della nostra cucina. Degustazione completa con bevanda inclusa.', 10, '2025-09-27', '2025-09-27', 18.00),
(15, 'Scopri la delicatezza delle Scaloppine al Limone in versione degustazione: carne tenera con un tocco di freschezza agrumata, perfetta per chi ama i sapori semplici e raffinati. Comprende servizio al tavolo e calice di vino.', 6, '2025-09-04', '2025-10-04', 20.00),
(28, 'Un dolce momento con il nostro Tiramis&ugrave;: crema soffice al mascarpone, savoiardi e caffè per una degustazione che chiude in dolcezza la tua esperienza da noi. Bevanda calda o digestivo inclusi.', 9, '2025-08-12', '2025-09-12', 15.00),

-- PRESENTI
(2, 'Tagliere di Salumi in degustazione: un assaggio delle nostre migliori selezioni di salumi nostrani accompagnati da pane fresco. Un incontro tra tradizione e convivialità. Servizio e bevanda inclusi.', 0, '2025-09-10', '2025-10-10', 14.00),
(9, 'Risotto ai Funghi: una degustazione dedicata al gusto autentico dei porcini freschi, servito in porzioni assaggio per apprezzarne cremosità e intensità. Degustazione completa con calice di vino.', 4, '2025-08-15', '2025-09-15', 18.00),
(18, 'Arrosto di Tacchino in degustazione: fettine cotte al forno con erbe aromatiche, leggere ma ricche di sapore. Ideale per chi cerca equilibrio e tradizione, con servizio e bevanda inclusi.', 2, '2025-08-25', '2025-09-25', 20.00),

-- PASSATE
(29, 'Panna Cotta con frutti di bosco: una degustazione dedicata a chi ama i dolci freschi e delicati, con la possibilità di scoprire abbinamenti sorprendenti. Comprende bevanda calda o fresca.', 0, '2025-07-18', '2025-08-18', 12.00);

-- UTENTI
INSERT INTO utenti ( email, username, password, nome, cognome, data_nascita, data_iscrizione) VALUES
("user@user.com","user","04f8996da763b7a969b1028ee3007569eaf3a635486ddab211d512c85b9df8fb","User", "User", "2000-01-01", "2025-02-01"),
("Licu@lica.com", "Lica04", "1bed7160e8125ec75d6fd2abcaa165f0266e4dbea81e42725006967aec0eb5d7", "Angy", "Licola", "2004-12-10", "2025-01-01"),
("gabry@magno.it", "Gabry", "e790a542941d6263f18a53159c106a135cf908e8159732f10a7fd7553ab61294", "Gabriele", "Magnoni", "2001-12-06", "2024-12-31");

-- PRENOTAZIONI PRODOTTI
INSERT INTO prenotazioni (id_utente, id_prodotto, quantita, data_prenotazione) VALUES
(1, 8, 2, '2025-09-01'),
(2, 15, 1, '2025-09-05'),
(3, 12, 1, '2025-09-03'),
(1, 1, 2, '2025-08-15'),
(2, 2, 3, '2025-08-20'),
(3, 3, 1, '2025-08-18'),
(1, 4, 2, '2025-07-10'),
(2, 5, 1, '2025-07-15'),
(3, 6, 1, '2025-07-12');

-- PRENOTAZIONI DEGUSTAZIONI
INSERT INTO prenotazioni_degustazioni (id_cliente, id_degustazione, numero_persone, data_prenotazione) VALUES
(1, 2, 2, '2025-08-04'),
(2, 1, 4, '2025-08-29'),
(3, 2, 1, '2025-08-18'),
(1, 3, 2, '2025-08-17'),
(2, 4, 4, '2025-08-26'),
(3, 5, 1, '2025-08-15'),
(1, 7, 2, '2025-08-12'),
(2, 6, 4, '2025-08-21'),
(3, 4, 1, '2025-08-11');

-- PREFERITI
INSERT INTO preferiti (id_utente, id_prodotto) VALUES
(1, 8),
(2, 15),
(3, 12),
(1, 1),
(2, 2),
(3, 3),
(1, 4),
(2, 5),
(3, 6),
(1, 7),
(2, 8),
(3, 9);


-- VALUTAZIONI
INSERT INTO valutazioni (id_utente, id_prodotto, voto, commento, data) VALUES
(1, 8, 5, "La lasagna era deliziosa, con un ragù ricco e saporito. La porzione era abbondante e il servizio eccellente.", "2025-09-15"),
(2, 15, 4, "Le scaloppine erano tenere e ben condite, anche se avrei preferito un po' più di salsa al limone.", "2025-09-10"),
(3, 28, 5, "Il tiramisù era perfetto: cremoso e con il giusto equilibrio di sapori. Un vero piacere per il palato!", "2025-09-12"),
(1, 2, 4, "Il tagliere di salumi era vario e di buona qualità. Il pane fresco ha fatto la differenza.", "2025-09-20"),
(2, 9, 5, "Il risotto ai funghi era eccezionale, con funghi freschi e una consistenza cremosa. Lo consiglio vivamente!", "2025-09-18"),
(3, 18, 3, "L'arrosto di tacchino era buono ma un po' asciutto. Forse un po' più di salsa avrebbe migliorato il piatto.", "2025-09-22");

-- SUGGERIMENTI
INSERT INTO suggerimenti (id_utente, suggerimento, data_inserimento) VALUES
(null, "Sarebbe utile inserire un menù digitale aggiornato quotidianamente con foto dei piatti disponibili.", "2025-08-31"),
(1, "Sarebbe interessante aggiungere una funzione carrello online per facilitare gli ordini e renderli più veloci.","2025-08-31"),
(null, "Ottima scelta di salumi e formaggi, sarebbe utile indicare meglio la provenienza.","2025-09-05"),
(null, "Prezzi nella media, ma proporrei offerte o menù combinati per il pranzo.","2025-07-22"),
(3, "Ottima varietà di piatti pronti, ma aggiungerei più opzioni vegetariane e vegane","2025-09-03"),
(2, "Servizio cordiale e veloce, ma migliorerei l’esposizione dei prodotti al banco.","2025-08-16");
