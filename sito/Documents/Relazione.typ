#import "./graphic.typ": *
#import "@preview/treet:0.1.1": *

#show: doc => copertina(doc)

#set text(11pt, font: "New Computer Modern")

= Introduzione
Il presente documento costituisce la relazione del progetto di sviluppo di un sito web realizzato per il corso di *Tecnologie Web* _(L31 - Laurea in Informatica)_ nell’anno accademico 2024-2025.
L’obiettivo della relazione è descrivere le metodologie adottate e i ragionamenti che hanno guidato la progettazione e la realizzazione del prodotto.

Il progetto ha previsto lo sviluppo di un sito web accessibile a tutte le tipologie di utenti di una gastronomia, da noi denominata Cuochi per Caso.

Il sito offre diverse funzionalità:
- Visualizzazione dei prodotti disponibili presso la gastronomia;
- Consultazione delle recensioni e delle valutazioni lasciate dagli altri utenti;
- Invio di suggerimenti e feedback;
- Prenotazione di prodotti da ritirare direttamente in negozio;
- Prenotazione di eventi di degustazione, durante i quali i clienti possono assaggiare i prodotti più apprezzati, scoprire nuovi abbinamenti e ricevere consigli personalizzati dallo staff;
- Inserimento di recensioni e valutazioni sui prodotti acquistati;
- Registrazione, autenticazione e gestione del proprio account personale.

L’obiettivo finale è stato quello di sviluppare un sito intuitivo e completo, capace di modernizzare l’immagine della gastronomia.
In particolare, il progetto ha introdotto un sistema di prenotazione online e strumenti per raccogliere feedback e valutazioni da parte dei clienti, così da offrire al personale indicazioni utili per monitorare la qualità dei prodotti e individuare possibili miglioramenti.

= Analisi dei requisiti
== Analisi utente
== SEO

= Progettazione
== Schema organizzativo
== Tipi di utente
== Funzionalità
== Convenzioni interne


== Schema database
Si riporta, qui di seguito, l'immagine dello schema relazionale del database che è stato progettato in modo da rispettare sia la *Terza Forma Normale* _(3NF)_ che la *Forma Normale di Boyce-Codd* _(BCNF)_.

#figure(
  image("assets/db.png", width: 110%),
  caption: [Schema relazionale del database.],
)

= Realizzazione
== Struttura e contenuto
=== HTML
=== Popolamento database

== Presentazione
=== CSS
=== Immagini e icone
Le immagini del sito sono salvate principalmente in formato *WEBP* e hanno tutte una dimensione inferiore a *1MB*.
La scelta di questo formato è stata motivata dalla sua leggerezza e rapidità di caricamento nelle varie pagine. Tuttavia, essendo un formato relativamente recente, può comportare alcuni problemi di retrocompatibilità.

Un’eccezione riguarda le immagini del profilo utente: in questo caso è possibile caricare file nei formati *PNG*, *JPG* o *JPEG*, con un limite massimo di *2MB*.

I loghi, invece, sono stati salvati in formato *PNG* per preservare la trasparenza dello sfondo, mantenendo comunque una dimensione inferiore a *1MB*.
Le icone sono realizzate in grafica vettoriale e salvate in formato *SVG*.

Per ogni prodotto o evento di degustazione viene mostrata la relativa immagine.

=== Font
Il font scelto per l’intero sito web è _*Nunito*_.
La decisione è stata presa perché si tratta di un carattere sans-serif dalle forme tondeggianti, che garantisce una buona leggibilità sul web. Dispone inoltre di una vasta gamma di pesi (da Light a ExtraBold), è ottimizzato per la lettura su schermi digitali e supporta pienamente caratteri accentati e simboli delle lingue europee, compreso l’italiano. Un ulteriore vantaggio è la sua gratuità e la disponibilità tramite _Google Fonts_.

Per migliorare ulteriormente la leggibilità del testo, è stata applicata un’interlinea pari a 1.5em.

Per la versione stampata, invece, è stato adottato il font _*Times New Roman*_, un carattere con grazie, dimensione 12pt, anch’esso accompagnato da un’interlinea di 1.5em.

=== Colori

== Comportamento
=== PHP
=== JavaScript
=== Validazione dell'input
=== Sicurezza
=== Errori navigazione o del server

== Accessibilità
=== Aiuti per lo screen reader
=== Compatibilità

= Test effettuati
== Navigabilità ed accessibilità
== Falsi positivi
== Screen reader

= Organizzazione del gruppo
== Suddivisione dei compiti
Il lavoro è stato suddiviso tra i due membri del gruppo, ciascuno dei quali ha progettato specifiche pagine e sviluppato le corrispondenti funzionalità, interfaccia e stile. Di seguito viene riportata la ripartizione delle attività realizzate individualmente.

Durante l’intero arco della fase di progettazione e sviluppo, i membri del team si sono incontrati regolarmente tramite la piattaforma _Discord_ al fine di:
- monitorare lo stato di avanzamento del progetto;
- discutere eventuali osservazioni o criticità riscontrate;
- pianificare le successive fasi di lavoro;
- eseguire test in modo congiunto e simultaneo, riducendo così la possibilità di errori, anche di natura distrattiva.

- *Nicolò Bolzon*
  - HTML/CSS: 
  - PHP; 
  - JS: menu ad hamburger;
  - DB: progettazione, creazione, connessione, inserimento e popolamento;
  - Testing;
  - Relazione.

- *Gabriele Isacco Magnelli*
  - HTML/CSS:
  - PHP;
  - JS: validazione input, visualizzazione errori;
  - DB: progettazione, creazione, connessione, inserimento e popolamento;
  - Testing;
  - Relazione.

= Note