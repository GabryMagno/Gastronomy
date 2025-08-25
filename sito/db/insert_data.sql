INSERT INTO prodotti 
(nome, categoria, unita, min_prenotabile, max_prenotabile, descrizione, prezzo, isDisponibile, url_immagine) 
VALUES
-- ANTIPASTI
('Bruschetta Classica', 'antipasto', 'pezzo', 1, 10, 'Pane casereccio con pomodoro fresco, basilico e olio EVO', 2.50, TRUE, 'assets/img/prodotti/bruschetta.webp'),
('Tagliere Salumi', 'antipasto', 'porzione', 1, 5, 'Selezione di salumi nostrani con pane fresco', 8.00, TRUE, 'assets/img/prodotti/tagliere_salumi.webp'),
('Insalata di Mare', 'antipasto', 'porzione', 1, 5, 'Gamberi, polpo e calamari conditi con limone e prezzemolo', 9.50, TRUE, 'assets/img/prodotti/insalata_mare.webp'),
('Olive Ascolane', 'antipasto', 'pezzo', 3, 20, 'Olive verdi ripiene di carne e fritte dorate', 0.30, TRUE, 'assets/img/prodotti/olive_ascolane.webp'),
('Parmigiana Mignon', 'antipasto', 'vaschetta', 1, 5, 'Vaschetta con mini porzioni di parmigiana di melanzane', 4.50, TRUE, 'assets/img/prodotti/parmigiana.webp'),
('Frittata di Zucchine', 'antipasto', 'pezzo', 1, 8, 'Frittata morbida alle zucchine fresche', 3.00, TRUE, 'assets/img/prodotti/frittata_zucchine.webp'),
('Insalata Russa', 'antipasto', 'kg', 1, 5, 'Insalata russa classica, prezzo al kg', 12.90, TRUE, 'assets/img/prodotti/insalata_russa.webp'),

-- PRIMI
('Lasagna alla Bolognese', 'primo', 'porzione', 1, 6, 'Sfoglia fresca con ragù di carne, besciamella e parmigiano', 7.50, TRUE, 'assets/img/prodotti/lasagna.webp'),
('Risotto ai Funghi', 'primo', 'porzione', 1, 6, 'Riso carnaroli con funghi porcini freschi', 8.00, TRUE, 'assets/img/prodotti/risotto_funghi.webp'),
('Trofie al Pesto', 'primo', 'porzione', 1, 6, 'Pasta fresca con pesto genovese e patate', 7.00, TRUE, 'assets/img/prodotti/trofie_pesto.webp'),
('Canneloni Ricotta e Spinaci', 'primo', 'porzione', 1, 6, 'Crespelle ripiene di ricotta fresca e spinaci', 7.50, TRUE, 'assets/img/prodotti/cannelloni.webp'),
('Spaghetti Carbonara', 'primo', 'porzione', 1, 6, 'Pasta con guanciale, uova e pecorino romano', 6.50, TRUE, 'assets/img/prodotti/carbonara.webp'),
('Gnocchi Sorrentina', 'primo', 'porzione', 1, 6, 'Gnocchi al forno con pomodoro e mozzarella', 7.00, TRUE, 'assets/img/prodotti/gnocchi_sorrentina.webp'),

-- SECONDI
('Pollo Arrosto', 'secondo', 'pezzo', 1, 4, 'Pollo intero arrosto con erbe aromatiche', 12.00, TRUE, 'assets/img/pollo_arrosto.webp'),
('Scaloppine al Limone', 'secondo', 'porzione', 1, 6, 'Fettine di vitello al limone', 9.00, TRUE, 'assets/img/scaloppine.webp'),
('Baccalà alla Vicentina', 'secondo', 'porzione', 1, 6, 'Filetto di baccalà stufato con cipolle e latte', 11.00, TRUE, 'assets/img/baccala.webp'),
('Involtini di Carne', 'secondo', 'pezzo', 1, 8, 'Carne di vitello ripiena e cotta al forno', 4.00, TRUE, 'assets/img/involtini.webp'),
('Arrosto di Tacchino', 'secondo', 'porzione', 1, 6, 'Fettine di tacchino al forno con erbe aromatiche', 8.50, TRUE, 'assets/img/arrosto_tacchino.webp'),
('Polpette al Sugo', 'secondo', 'pezzo', 2, 12, 'Polpette di carne in salsa di pomodoro', 1.50, TRUE, 'assets/img/polpette.webp'),
('Arrosto di Maiale', 'secondo', 'kg', 1, 5, 'Arrosto di maiale cotto lentamente, prezzo al kg', 24.00, TRUE, 'assets/img/arrosto_maiale.webp'),

-- CONTORNI
('Patate al Forno', 'contorno', 'porzione', 1, 6, 'Patate al forno croccanti con rosmarino', 4.00, TRUE, 'assets/img/patate_forno.webp'),
('Verdure Grigliate', 'contorno', 'porzione', 1, 6, 'Misto di zucchine, melanzane, pomodori, asparagi e peperoni grigliati', 4.50, TRUE, 'assets/img/verdure_grigliate.webp'),
('Insalata Mista', 'contorno', 'porzione', 1, 6, 'Insalata fresca con pomodori, cetrioli e lattuga', 3.50, TRUE, 'assets/img/insalata.webp'),
('Spinaci al Burro', 'contorno', 'porzione', 1, 6, 'Spinaci freschi saltati al burro', 3.50, TRUE, 'assets/img/spinaci.webp'),
('Caponata Siciliana', 'contorno', 'porzione', 1, 6, 'Melanzane, peperoni e cipolle in agrodolce', 5.00, TRUE, 'assets/img/caponata.webp'),
('Zucchine Trifolate', 'contorno', 'porzione', 1, 6, 'Zucchine saltate in padella con aglio e prezzemolo', 4.00, TRUE, 'assets/img/zucchine.webp'),
('Insalata di Farro', 'contorno', 'kg', 1, 4, 'Insalata di farro con verdure di stagione', 15.00, TRUE, 'assets/img/insalata_farro.webp'),

-- DOLCI
('Tiramis&ugrave;', 'dolce', 'porzione', 1, 6, 'Classico dolce al cucchiaio con savoiardi, caffè e mascarpone', 4.50, TRUE, 'assets/img/tiramisu.webp'),
('Panna Cotta', 'dolce', 'porzione', 1, 6, 'Panna cotta con topping di frutti di bosco', 4.00, TRUE, 'assets/img/pannacotta.webp'),
('Cassata Siciliana', 'dolce', 'porzione', 1, 6, 'Dolce tipico con ricotta, pan di spagna e canditi', 5.00, TRUE, 'assets/img/cassata.webp'),
('Crostata alla Marmellata', 'dolce', 'pezzo', 1, 8, 'Frolla con confettura di albicocca', 3.00, TRUE, 'assets/img/crostata.webp'),
('Bign&egrave; alla Crema', 'dolce', 'pezzo', 2, 12, 'Pasta <span lang="fr">choux</span> ripiena di crema pasticcera', 1.50, TRUE, 'assets/img/bigne.webp'),
('Biscotti Artigianali', 'dolce', 'kg', 1, 3, 'Selezione di biscotti di frolla artigianali', 18.00, TRUE, 'assets/img/biscotti.webp'),
('<span lang="en">Cheesecake</span> ai Frutti Rossi', 'dolce', 'porzione', 1, 6, '<span lang="en">Cheesecake</span> con topping ai frutti rossi', 4.50, TRUE, 'assets/img/cheesecake.webp');