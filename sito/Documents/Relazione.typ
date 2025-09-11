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
Si allega immagine dello schema relazionale del database:

#figure(
  image("", width: 80%),
  caption: [Schema relazionale del database.],
)

Il database è stato progettato in modo da rispettare sia la *Terza Forma Normale* _(3NF)_ che la *Forma Normale di Boyce-Codd* _(BCNF)_.

= Realizzazione
== Struttura e contenuto
=== HTML
=== Popolamento database

== Presentazione
=== CSS
=== Immagini e icone
=== Font
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

= Note