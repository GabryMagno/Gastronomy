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
La fase di sviluppo è stata preceduta da un’analisi di alcuni siti web di gastronomie locali. È emerso che molte di esse non dispongono di un sito dedicato e si limitano a pubblicare informazioni e prodotti esclusivamente sui canali social. Alcune, invece, hanno un sito web, ma con funzionalità molto ridotte, spesso limitate alla sola presentazione di informazioni di base.

Da questa osservazione è nata l’idea di realizzare un sito web per una gastronomia che non solo ne curi la *presentazione online*, rendendola più visibile sul web, ma che consenta anche ai clienti di *consultare i prodotti disponibili* in negozio e prenotarli direttamente online.

Inoltre, si è pensato di arricchire l’offerta integrando un servizio di *degustazione prenotabile*, attraverso il quale i clienti possono vivere un’esperienza unica: assaggiare i prodotti in negozio, scoprire nuovi sapori e ricevere consigli personalizzati dallo staff sugli abbinamenti.

Infine, tenendo conto dell’importanza del feedback dei clienti, sono state introdotte funzionalità di *valutazione e recensione*. In questo modo è possibile raccogliere opinioni utili, migliorare il servizio e permettere agli utenti di visualizzare facilmente i prodotti più apprezzati.

È stata così definita la struttura gerarchica della gastronomia _*Cuochi per Caso*_ e sono state inoltre stabilite alcune *convenzioni interne*, così da garantire un’esperienza d’uso coerente e intuitiva per l’utente.

Le pagine del sito sono state progettate per risultare semplici da utilizzare e visivamente accattivanti, con una particolare attenzione alla *navigazione da dispositivi mobili*, oggi sempre più diffusi e parte integrante della vita quotidiana.

== Analisi utente
Il sito della gastronomia _Cuochi per Caso_ ha come obiettivo quello di raggiungere clienti abituali e nuovi utenti, offrendo la possibilità di *prenotare online i prodotti* direttamente da casa, evitando così la necessità di recarsi fisicamente in negozio. Inoltre, il sito propone la possibilità di *prenotare esperienze di degustazione* presso i locali della gastronomia.
Non sono richieste conoscenze pregresse per visitare il sito: tutte le informazioni sono presentate in modo chiaro e accessibile. I clienti hanno anche la possibilità di *visualizzare gli ingredienti* utilizzati per la preparazione dei prodotti gastronomici in vendita.

La navigazione risulta quindi intuitiva e consente all’utente di acquisire facilmente nuove informazioni semplicemente esplorando le pagine. Il *layout* e la *struttura* del sito sono stati progettati per favorire un’interazione semplice e immediata, così da facilitare la familiarizzazione con l’interfaccia.

Non esiste un target minimo o massimo per la visualizzazione del sito: tuttavia, la *registrazione* e la possibilità di effettuare prenotazioni sono consentite esclusivamente agli *utenti maggiorenni* _(18 anni in su)_.

== SEO
Le principali *ricerche* a cui il sito intende rispondere sono:
- il nome stesso del sito;
- ricerche generiche come “gastronomia”, “prodotti”, “degustazioni”;
- ricerche legate alla prenotazione di prodotti di gastronomia;
- ricerche relative alla prenotazione delle degustazioni;
- ricerche contenenti i nomi dei prodotti presenti nel sito.

Le parole chiave sono state selezionate per rivolgersi sia agli utenti che hanno già un’idea precisa di ciò che stanno cercando, sia a nuovi visitatori interessati a scoprire di più durante la navigazione.

Per *migliorare il ranking del sito* sono state intraprese diverse azioni:
- il contenuto del tag `title` è stato strutturato dal particolare al generale e arricchito con parole chiave pertinenti;
- in ogni pagina, tramite il metatag `keywords`, sono state indicate le parole chiave specifiche, evidenziate nel codice HTML anche attraverso il tag `<strong>`;
- separazione tra struttura e presentazione;
- separazione tra struttura e comportamento;
- non è stato fatto uso di `display: none` _(se non nel foglio `print.css`)_, né di tecniche come `height: 0` o `visibility: hidden`, che possono compromettere l’indicizzazione.

= Progettazione
== Tipi di utente
Nella fase di progettazione del sito sono stati individuati i seguenti tipi di utente:

- *Ospite*: l’utente ospite può visualizzare le sezioni pubbliche del sito, visitando le pagine _Home, Chi Siamo, Prodotti, Degustazioni, Dettaglio Prodotto e Dettaglio Degustazione_. Non ha la possibilità di effettuare prenotazioni _(né di prodotti né di degustazioni)_ e non può accedere all’area personale se non è registrato. Non può aggiungere prodotti ai preferiti né inserire nuove recensioni o valutazioni, ma può consultare quelle già presenti. È comunque possibile, anche senza autenticazione, inviare feedback tramite l’apposito form presente nella pagina _Chi Siamo_.

- *Utente Registrato*: l’utente registrato ha accesso completo alle funzionalità riservate agli utenti autenticati. Può accedere all’area personale, che presenta una dashboard con i prodotti preferiti, le prenotazioni di prodotti e degustazioni e le recensioni inserite. Può effettuare prenotazioni, aggiungere o rimuovere prodotti dai preferiti, inserire, modificare o eliminare valutazioni e commenti. È inoltre possibile gestire i propri dati personali, inclusa la modifica della foto profilo. Anche l’invio di feedback è disponibile, e in questo caso il sistema traccia l’utente che lo invia.

_L’utente amministratore e la relativa area del sito non sono stati sviluppati. Considerata la ridotta dimensione del gruppo di lavoro, si è preferito concentrarsi sulle altre sezioni del sito, lasciando la gestione amministrativa come possibile implementazione futura._

== Funzionalità
Elenco delle funzionalità del sito:
- registrazione utente;
- login utente;
- logout utente;
- inserimento feedback;
- visualizzazione prodotti;
- visualizzazione prodotti filtrati;
- visualizzazione dei dettagli di un prodotto;
- visualizzazione valutazioni e recensioni di un prodotto;
- prenotazione di un prodotto _(utente registrato o autenticato)_;
- rimozione prenotazione di un prodotto prenotato _(utente registrato o autenticato)_;
- inserimento valutazione e recensione di un prodotto _(utente registrato o autenticato)_;
- eliminazione valutazione e recensione di un prodotto _(utente registrato o autenticato)_;
- visualizzazione degustazioni;
- visualizzazione degustazioni filtrate;
- prenotazione di una degustazione _(utente registrato o autenticato)_;
- rimozione di una degustazione prenotata _(utente registrato o autenticato)_;
- aggiunta di un prodotto ai preferiti _(utente registrato o autenticato)_;
- rimozione di un prodotto dai preferiti _(utente registrato o autenticato)_;
- visualizzazione elenco prenotazioni prodotti attive dell’utente _(utente registrato o autenticato)_;
- visualizzazione elenco degustazioni attive dell’utente _(utente registrato o autenticato)_;
- visualizzazione elenco prodotti preferiti dell’utente _(utente registrato o autenticato)_;
- visualizzazione elenco valutazioni e recensioni dell’utente _(utente registrato o autenticato)_;
- modifica dati utente _(utente registrato o autenticato)_;
- modifica immagine profilo _(utente registrato o autenticato)_;
- eliminazione account _(utente registrato o autenticato)_;

== Convenzioni interne
Si riportano di seguito le convenzioni interne del sito:
- *Link*: sia i link visitati sia quelli non visitati sono sempre sottolineati, per garantire coerenza e riconoscibilità. I link _non visitati_ hanno il testo di colore bianco; I link _visitati_ hanno il testo di colore giallo. Entrambi sono visualizzati su sfondo bordeaux scuro (`#73142F`). I colori scelti rientrano nella *palette grafica del sito*, riportata di seguito. \
  
  Si è riscontrato un piccolo problema legato al *contrasto insufficiente* tra il colore del testo dei link visitati e quello dei link non visitati. Per ovviare a questo problema sono state valutate diverse soluzioni: ad esempio, modificare lo stile dei link visitati (come renderli in *corsivo* o *doppia sottolineatura*), ma molti browser ne limitano l’efficacia e la funzionalità. In alternativa, era previsto l’uso di JavaScript per distinguere meglio i link, ma tale soluzione è stata successivamente commentata per mantenere una chiara separazione tra comportamento e stile. Lo script di riferimento si trova in index.html e può essere attivato decommentando la riga: `<script async src="assets/js/color.js"></script>`. È inoltre necessario decommentare la sezione CSS corrispondente in `desktop.css`.

- *Link circolari*: nel menu sono stati disabilitati e nascosti agli screen reader i link che rimandano alla stessa pagina già visualizzata, in modo da segnalare all’utente che non sono utilizzabili. Questi link hanno testo di colore bianco su sfondo blu scuro.

- *Form*: tutti i form del sito presentano uno sfondo bianco con testo nero, inclusi i titoli e le etichette dei campi, per garantire leggibilità e contrasto.
I placeholder dei campi di inserimento sono invece in grigio scuro, in modo da distinguersi dal testo effettivo inserito dall’utente. Ogni campo di inserimento di un form è seguito da un elemento `<details>` che fornisce suggerimenti e indicazioni sulle eventuali limitazioni o requisiti per l’inserimento dei dati, aiutando così l’utente a compilare correttamente il form.

== Schema database
Si riporta, qui di seguito, l'immagine dello schema relazionale del database che è stato progettato in modo da rispettare sia la *Terza Forma Normale* _(3NF)_ che la *Forma Normale di Boyce-Codd* _(BCNF)_.

#figure(
  image("assets/db.png", width: 110%),
  caption: [Schema relazionale del database.],
)

= Realizzazione
== Struttura e contenuto
=== HTML
Il sito web è stato sviluppato in *HTML5* con sintassi *XML*, in conformità al regolamento del progetto didattico.
Durante la fase di scrittura delle pagine HTML, si è cercato di mantenere una struttura il più possibile chiara e stabile, ricorrendo a segnaposto con la sintassi `[NomeSegnaposto]`. Questi segnaposto vengono poi sostituiti dinamicamente tramite PHP, utilizzando la funzione `str_replace()` seguendo un pattern definito.

Questo approccio consente di mantenere una struttura fissa nei file HTML, modificando allo stesso tempo i contenuti in maniera dinamica attraverso PHP e JavaScript.

```html
<ul class="list" id="prodotti" aria-label="Lista dei prodotti filtrati">
    [PRODUCTS]
</ul>
```

Grazie a `str_replace()`, il segnaposto `[PRODUCTS]` viene sostituito con l’elenco dei prodotti generato dinamicamente in PHP, aggiornato in base ai filtri selezionati dall’utente.

=== Popolamento database
Per il popolamento del database è stata creata una query di inserimento.
I prodotti sono stati scelti in modo casuale, cercando di coprire tutte le categorie principali _(antipasti, primi, secondi, contorni e dolci)_ e garantendo la presenza di alternative per diverse diete _(vegana, vegetariana e senza glutine)_.

Alcuni utenti, invece, sono stati inseriti sia manualmente, sia tramite il form di registrazione integrato nel sito.

Le immagini dei prodotti sono state realizzate con strumenti di intelligenza artificiale, così da ottenere un set grafico coerente e personalizzato.

== Presentazione
=== CSS
Per gestire al meglio l’aspetto *responsive del sito*, il CSS è stato suddiviso in tre fogli di stile distinti: `desktop.css`, `mobile.css`, `print.css`.

Particolare attenzione è stata posta nella scelta dei layout, privilegiando l’uso di *flexbox*. Questo approccio è stato utilizzato in modo consapevole, evitando strutture annidate troppo complesse (oltre il secondo livello), così da garantire buone prestazioni e facilità di manutenzione.
Non è stato invece adottato il layout *grid*, generalmente più adatto a griglie complesse ma in alcuni casi più oneroso da gestire rispetto a flexbox.

Per facilitare la gestione dei colori e garantire uniformità grafica, sono state definite delle *variabili CSS* all’inizio del foglio di stile, in modo da poterle riutilizzare in diverse parti del codice.

Per quanto riguarda il foglio `print.css`, è stato applicato uno stile di testo *giustificato*.
Sono stati rimossi tutti gli elementi interattivi del sito, come la navbar, i form e gli strumenti di navigazione. Allo stesso modo, sono state eliminate le immagini di sfondo e quelle non pertinenti al contenuto effettivo.

Per rendere la stampa più chiara e completa, ogni link viene seguito dal relativo indirizzo racchiuso tra parentesi quadre, ad esempio: `[collegamento]`.

=== Immagini e icone
Le immagini del sito sono salvate principalmente in formato *WEBP* e hanno tutte una dimensione inferiore a *1MB*.
La scelta di questo formato è stata motivata dalla sua leggerezza e rapidità di caricamento nelle varie pagine. Tuttavia, essendo un formato relativamente recente, può comportare alcuni problemi di retrocompatibilità.

Un’eccezione riguarda le immagini del profilo utente: in questo caso è possibile caricare file nei formati *PNG*, *JPG* o *JPEG*, con un limite massimo di *2MB*.

I loghi, invece, sono stati salvati in formato *PNG* per preservare la trasparenza dello sfondo, mantenendo comunque una dimensione inferiore a *1MB*.
Le icone sono realizzate in grafica vettoriale e salvate in formato *SVG*.

Per ogni prodotto o evento di degustazione viene mostrata la relativa immagine.

Le immagini dei prodotti sono state generate tramite strumenti di intelligenza artificiale.

=== Font
Il font scelto per l’intero sito web è _*Nunito*_.
La decisione è stata presa perché si tratta di un carattere sans-serif dalle forme tondeggianti, che garantisce una buona leggibilità sul web. Dispone inoltre di una vasta gamma di pesi (da Light a ExtraBold), è ottimizzato per la lettura su schermi digitali e supporta pienamente caratteri accentati e simboli delle lingue europee, compreso l’italiano. Un ulteriore vantaggio è la sua gratuità e la disponibilità tramite _Google Fonts_.

Per migliorare ulteriormente la leggibilità del testo, è stata applicata un’interlinea pari a 1.5em.

Per la versione stampata, invece, è stato adottato il font _*Times New Roman*_, un carattere con grazie, dimensione 12pt, anch’esso accompagnato da un’interlinea di 1.5em.

=== Colori
Abbiamo scelto la palette di colori ponendo particolare attenzione al *contrasto* e alla *leggibilità*.
La selezione è stata effettuata assicurandoci che il rapporto di contrasto tra testo e sfondo fosse sufficientemente elevato, in conformità alle *linee guida _WCAG_*: per la maggior parte dei contenuti è stato raggiunto il livello *AAA*, garantendo comunque sempre almeno il livello *AA*.

La palette adottata è la seguente:
#figure(
  image("assets/colori.png", width: 75%),
  caption: [Palette di colori adottata all'interno del sito.],
)

== Comportamento
=== PHP
Gestisce l’intero lato back-end dell’applicazione.
Si occupa di:
- Connessione al database, lettura e scrittura dei dati.
- Validazione avanzata delle informazioni ricevute dal front-end (es. controlli di coerenza).
- Verifica delle credenziali (es. nel cambio password: controllo che la password vecchia corrisponda a quella presente nel DB).
- Gestione della logica applicativa (prenotazioni, valutazioni, suggerimenti, ecc.).
- Sanitizzazione e filtraggio degli input utente, per ridurre i rischi di SQL injection, XSS e altre vulnerabilità.
PHP effettua quindi sia controlli di sicurezza sia controlli di business logic, che non possono essere affidati al solo JavaScript.

=== JavaScript
Utilizzato esclusivamente lato client per migliorare l’esperienza utente.
Le funzionalità implementate sono le seguenti:
- Gestione del menù ad hamburger (apertura/chiusura navigazione mobile).
- Validazioni preliminari sui form: controllo dei requisiti minimi degli input (lunghezza minima password, formato email, campi obbligatori, ecc.).
Questi controlli sono a scopo di usabilità (feedback immediato all’utente), ma vengono sempre replicati e approfonditi lato PHP per garantire la sicurezza e l’integrità dei dati.

=== Sicurezza
Sicurezza adottata all'interno del sito:

- Tutte le *query SQL* vengono eseguite tramite la libreria `mysqli` utilizzando i prepared statements, prevenendo tentativi di *SQL Injection*.
- Le password non vengono memorizzate in chiaro nel database, ma vengono protette tramite *hashing*.
- La connessione al database viene sempre chiusa tramite il metodo `CloseConnectionDB()`.
- È stato implementato un *sanitizer* per la sanitizzazione degli input degli utenti.

=== Errori navigazione o del server
Le direttive per la gestione e visualizzazione degli errori sono state definite nel file `.htaccess`.
In sintesi:
- Se l’utente tenta di accedere a pagine per cui non ha i permessi, viene mostrata una pagina *Error 403* personalizzata.
- Se l’utente visita un link errato o una pagina inesistente, viene mostrata una pagina *Error 404* personalizzata.
- In caso di errori lato server, come problemi di connessione al database, viene mostrata una pagina *Error 500* personalizzata.

Ogni pagina di errore include un’immagine simpatica e una breve descrizione testuale dell’errore, con riferimenti divertenti all’ambiente della gastronomia.

== Accessibilità
Sono di seguito elencate tutte le scelte effettuate per migliorare l’accessibilità del sito web a tutte le categorie di utenti.
Ognuna mira al raggiungimento del livello di conformità *AA* delle _WCAG_, come stabilito dalla legge italiana:
- Navigazione da tastiera completa e accessibile.
- Form accessibili.
- Struttura gerarchica del sito ampia e poco profonda.
- I tag di heading `(h1, h2, …)` sono stati utilizzati rispettando la gerarchia.
- La sezione _above the fold_ è stata progettata per rispondere alle domande: _Dove sono? Dove posso andare? Di cosa si tratta?_
- Contrasto adeguato tra colore del testo e colore di sfondo.
- Distinzione tra link visitati e non visitati.
- Le immagini di contenuto possiedono un attributo alt adeguato.
- Le immagini puramente decorative sono state inserite in background tramite CSS, poiché fanno parte della presentazione.
- Utilizzo del tag `<abbr>` per le abbreviazioni.
- Utilizzo del tag `<span lang="">` per permettere agli screen reader di leggere correttamente parole in una lingua diversa da quella principale del sito.
- Utilizzo del tag `<time datetime="">` per le date.
- Presenza di skip link _(es. “Salta al contenuto principale”)_ per evitare di dover scorrere il menu a ogni pagina.
- Evidenza visiva dello stato di focus sugli elementi interattivi _(link, pulsanti, campi input)_.
- Ordine logico e coerente della navigazione tramite tabulazione.
- Etichette (`<label>`) collegate correttamente ai campi dei form.
- Indicazioni sugli errori nei form chiare e non basate solo sul colore.

=== Aiuti per lo screen reader
All’interno del sito sono stati utilizzati gli attributi *ARIA* per facilitare l’interazione con il sito a tutte le categorie di utenti.
Seguendo le buone pratiche, all’inizio di ogni pagina è presente una sezione con link di aiuto alla navigazione, i cosiddetti *"skip to content"*. Questi link consentono agli utenti che navigano tramite tastiera o utilizzano screen reader di saltare direttamente a specifiche sezioni della pagina, evitando contenuti non rilevanti e permettendo di raggiungere più rapidamente il contenuto desiderato.

=== Compatibilità
Il sito è progettato per essere completamente responsive, garantendo una visualizzazione ottimale sia su *desktop* sia su *dispositivi mobili*, con una larghezza minima dello schermo assicurata di _320px_.

Per migliorare l’esperienza utente su schermi piccoli, il menu di navigazione si trasforma in un *menu hamburger*, semplificando l’accesso alle diverse sezioni del sito. Anche i filtri presenti nella pagina prodotti diventano compressi sui dispositivi mobili, permettendo una navigazione più agevole senza sovraccaricare lo schermo.

L’uso di strutture CSS come *flexbox*, insieme all’impiego di *unità relative* per font e dimensioni (`em, rem, %`), permette di mantenere un *layout flessibile e adattabile* a diverse risoluzioni. Inoltre, immagini e componenti vengono scalati dinamicamente per garantire coerenza visiva e leggibilità su tutti i dispositivi.

All'interno del sito, inoltre, *non sono presenti tabelle*.

= Test effettuati
== Navigabilità ed accessibilità
Abbiamo utilizzato diversi strumenti per effettuare test manuali e automatici:

- *WCAG Color Contrast Checker*: per la verifica del contrasto dei colori e l’applicazione di vari filtri visivi _(protanopia, protanomalia, deuteranopia, deuteranomalia, tritanopia, tritanomalia, acromatopsia, acromatomalia)_. I test hanno sempre raggiunto un livello di conformità *WCAG AA* e, nella maggior parte dei casi, AAA;
- *WAVE by WebAIM, Silktide Inspector*: per controlli generali di accessibilità. Non sono stati segnalati errori; in particolare, i test automatici effettuati da Silktide hanno evidenziato una conformità *WCAG AA* e, nella maggior parte dei casi, AAA;
- *W3C Validator (HTML e CSS)*: non sono stati riscontrati errori
- *TotalValidator*: utilizzato per ulteriori verifiche di accessibilità e validità del codice;
- *Lighthouse:* per la valutazione delle prestazioni del sito, che hanno registrato punteggi buoni e, in alcuni casi, ottimi.
- *WCAG Color contrast checker*: controllo contrasto dei colori ed applicazione di vari filtri visivi _(protanopia, protanomalia, deuteranopia, deuteranomalia, tritanopia, tritanomalia, acromatopsia, acromatomalia)_, sempre raggiunto un livello di conformità WCAG AA e nella maggior parte dei casi AAA;
- *WAVE by WebAIM, Silktide Inspector*: per controlli generali di accessibilità e non vengono segnalati errori, in particolre nella maggior parte delle pagine i test automatici effettuati da Silktide hanno sempre raggiunto un livello di conformità WCAG AA e nella maggior parte dei casi AAA;
- *W3C Validator (per HTML e CSS)*: non vengono segnalati errori;
- *TotalValidator*;
- *Lighthouse*: calcolo prestazioni del sito con buoni e, in alcuni casi, ottimi punteggi;
- *Compatibilità con browser diversi* quali Microsoft Edge, Google Chrome, Mozilla Firefox e Opera;
- *Compatibilità con sistemi operativi diversi* quali Microsoft Windows 10, Windows 11, Ubuntu 23.10, Ubuntu 22.04, Android 15, iPadOS 18.6.2; 

L’unico problema riscontrato su _iPadOS_ riguarda la proprietà `background-attachment: fixed`, che non è correttamente supportata sui dispositivi iOS. Apple ha scelto di disattivarne il comportamento nativo per motivi legati alle prestazioni e alla gestione della GPU. Si tratta comunque di un aspetto puramente decorativo, che non compromette l’accessibilità né la fruizione dei contenuti.

== Falsi positivi
Vengono riportati qua di seguito i falsi positivi segnalati dagli strumenti di validazione _(Total Validator, Silktide, WAVE)_:
- in tutte le pagine vengono erroneamente segnalati errori di ortografia;
- sono presenti alcuni _warning_ dovuti al fatto che gli attributi `alt` delle immagini superano i 75 caratteri. Nel nostro sito, tuttavia, tutti gli `alt` rimangono comunque sotto i 100 caratteri, poiché per alcune immagini era necessario fornire descrizioni testuali più dettagliate, in modo da permettere agli utenti con disabilità visiva di comprendere meglio il contenuto.
- in _WAVE_ viene visualizzato un alert in cui si evidenzia la sottolineatura errata dei link nella pagina user-profile.php, cosa che è, invece, corretta.


== Screen reader
Durante la fase di testing, l’accessibilità del sito è stata verificata utilizzando gli screen reader _NVDA_, _VoiceOver_, _TalkBack_ e lo strumento integrato in _Silktide_. Non sono stati riscontrati problemi.

= Organizzazione del gruppo
== Suddivisione dei compiti
Il lavoro è stato suddiviso tra i due membri del gruppo, ciascuno dei quali ha progettato specifiche pagine e sviluppato le corrispondenti funzionalità, interfaccia e stile. Di seguito viene riportata la ripartizione delle attività realizzate individualmente.

Durante l’intero arco della fase di progettazione e sviluppo, i membri del team si sono incontrati regolarmente tramite la piattaforma _Discord_ al fine di:
- monitorare lo stato di avanzamento del progetto;
- discutere eventuali osservazioni o criticità riscontrate;
- pianificare le successive fasi di lavoro;
- eseguire test in modo congiunto e simultaneo, riducendo così la possibilità di errori, anche di natura distrattiva.

- *Nicolò Bolzon*
  - HTML: `index, chisiamo, 403, 404, 500, ringraziamenti, degustazioni, prodotto, user-profile`;
  - CSS: `desktop, mobile, print`;
  - PHP; 
  - JS: `menu ad hamburger`;
  - DB: `progettazione, creazione, connessione, inserimento e popolamento`;
  - Testing;
  - Relazione.

- *Gabriele Isacco Magnelli*
  - HTML: `index, ringraziamenti, conferma-scelta, degustazione, prodotti, prodotto, login, register, user-settings`;
  - CSS: `desktop, mobile`;
  - PHP;
  - JS: `validazione input, visualizzazione errori`;
  - DB: `progettazione, creazione, connessione, inserimento e popolamento`;
  - Testing;
  - Relazione.