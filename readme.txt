=== ANAC XML Render ===
Tags: anac, xml, comuni, pa, amministrazioni, locali, pubblicazione, online, software, gratuito, obbligo, legge, comune, modulo, decreto, 14 marzo, 2013, enzo, costantini
Requires at least: 3.8
Tested up to: 4.7.2
Version: 1.5.7
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin per la gestione e la visualizzazione di file XML con tracciato ANAC (Legge 190/2012 Art.1, comma 32). 

== Description ==

<strong>ANAC XML Render</strong> è un plugin che con l'uso di due semplici shortcode permette la visualizzazione tabellare di appalti e bandi di gara pubblicati sui siti istituzionali delle PA  ai fini della trasparenza (D.Lgs. 33/2013).<br>
Il plugin permette, inoltre, di impostare (o creare) una cartella sul Server e  caricare i file XML attraverso un modulo lato amministrativo. Quest'ultima funzione si rivela particolarmente utile se si ha poca dimestichezza con un client FTP. 

= Caratteristiche =

* visualizzazione tabellare con popup per visualizzare i dettagli della singola gara
* calcolo dei totali (numero lotti, totale aggiudicato, totale liquidato)  
* paginazione con un numero di lotti per pagina impostabili nello shortcode (default 10)
* evidenziazione colorata dei lotti che presentano uno sbilancio tra aggiudicato e liquidato (impostabile 0/1 con default 0)
* possibilit&agrave; di filtrare i dati a testo libero e, quindi, di visualizzare in tabella solo quelli che soddisfano ai criteri del filtro
* possibilità di scaricare i dati visualizzati in formati aperti e standardizzati indicati nelle linee guida dell'<strong>AgID</strong> (<em>JSON, XML</em>) 
* se si applica un filtro alla tabella vengono scaricati solo i dati di interesse
* permette di caricare direttamente dalla bacheca i file XML in una cartella preassegnata senza dover ricorrere a un client FTP 
* tramite un secondo shortcode permette di visualizzare il contenuto della cartella contenente i file XML; dalla stessa interfaccia è possibile sia scaricare i singoli file, sia vederne direttamente il contenuto in modo tabellare

== Installation ==
1. Scaricare il plugin e installarlo in wordpress
2. Attivare il plugin

= Uso =

<h4>Per visualizzare i dati di un singolo file in una tabella</h4>
* Inserire in qualunque pagina, articolo o documento della trasparenza dove si vuole visualizzare la tabella dei dati ANAC lo shortcode:

<pre>[<strong>anac_xml_render</strong> <strong>xml_url</strong>="Url completo del file XML"]</pre>

<h5>Esempio 1</h5>
<pre>[anac_xml_render xml_url="http://www.sito.it/avcp/2015.xml"]</pre> 
Con questo codice vengono usate le impostazioni di default (<em>Nessuna evidenziazione e 10 lotti per pagina</em>)

<h5>Esempio 2</h5>
<pre>[anac_xml_render xml_url="http://www.sito.it/avcp/2015.xml" highlight=1 items_per_page=5]</pre> 
Con questo codice viene:<br> 
- attivata l'evidenziazione dei lotti che presentano uno sbilancio tra aggiudicato e liquidato<br>
- attivata la visualizzazione di 5 lotti per pagina.<br>

= Opzioni =
1. anac_xml_render =  rappresenta l&#39;handle dello shortcode
2. xml_url = URL completo del file XML 
3. highlight = Se impostato evidenzia i lotti che presentano uno sbilancio tra aggiudicato e liquidato (0/1) (default 0)
4. items_per_page = Numero di lotti per pagina (default 10) 

<h4>Per visualizzare l'elenco dei file XML presenti nella cartella preimpostata</h4>
Creare una nuova pagina o un documento della trasparenza e inserire shortcode:

<pre>[<strong>anac_xml_file_list</strong> <strong>path_url</strong>="Url completo della cartella in cui risiedono i file XML" ]</pre> 

<h5>Esempio</h5>
<pre>[anac_xml_file_list path_url="http://www.sito.it/anac/"]</pre>  
Nella pagina verr&agrave; visualizzata una tabella con l'elenco dei file Xml presenti nella cartella <strong>http://www.sito.it/anac/</strong>
<br>Da questa pagina sar&agrave; possibile scaricare o visualizzare i singoli dataset XML.   

== Screenshots ==

1. Esempio tabella generata
2. Esempio della tabella con dettaglio
3. Schermata della bacheca da cui si possono caricare i file XML 
4. Schermata di esempio della pagina che mostra l'elenco dei file XML

== Changelog ==

= Versione 1.5.7 15/11/2017 =

1. Corretto il bug "XML File does not exist" segnalato e corretto da Leonardo Giacone (@leopeo)

= Versione 1.5.6 28/01/2017 =

1. Corretto il bug sul mancato upload del file XML (segnalato e corretto da  @itnmario)

= Versione 1.5.5 27/01/2017 =

1. Migliorata la formattazione dei numeri
2. Modifiche minori

= Versione 1.5.4 24/01/2017 =

1. Verificata compatibilità con WP 4.7.1
2. Inserita la formattazione del SI nei numeri
3. Modifiche minori

= Versione 1.5.3 20/02/2016 =

1. Migliorato l'algoritmo di ricerca
2. Modifiche minori

= Versione 1.5.2 16/02/2016 =

1. Aggiunta la ricerca su singola colonna
2. I totali della tabella filtrata vengono riportati in fondo alla tabella
3. Modifiche minori

= Versione 1.5.1 10/02/2016 =

1. &Egrave; stato inserito il calcolo dei totali parziali quando viene applicato un filtro. Essi vengono visualizzati in una seconda riga sotto i titoli, mentre in fondo alla tabella rimangono i totali generali di tutto il documento visualizzato.
2. Modifiche minori

= Versione 1.5.0 08/02/2016 =

1. &Egrave; stata potenziata la funzione di esportazione dei dati nei formati aperti (<strong>XML</strong> e <strong>JSON</strong>) usando come sorgente per l'esportazione direttamente il dataset XML piuttosto che i dati presentati in tabella. &Egrave; stato eliminato il formato CSV perché poco adatto ai dati strutturati del formato ANAC. 
Se vengono impostati dei filtri di ricerca, verr&agrave; esportato solo il risultato della ricerca. 
2. Modifiche minori

= Versione 1.4.5 06/02/2016 =

1. Aggiunta la possibilit&agrave; di impostare la cartella predefinita per caricare i file XML e lo shortlink della pagina che visualizza il contenuto della cartella
2. Modifiche minori

= Versione 1.4.0 02/02/2016 =

1. Aggiunto un secondo shortcode che permette di visualizzare nel proprio tema l'elenco dei file XML presenti nella cartella preassegnata
2. Modifiche minori

= Versione 1.3.0 31/01/2016 =

1. Aggiunto il modulo per il caricamento diretto del file XML in una cartella preassegnata (/avcp)
2. Modifiche minori

= Versione 1.2.0 30/01/2016 =

1. Aggiunta la possibilità di navigare nella tabella tramite filtro di ricerca anche con la paginazione attiva
2. Esportazione nei formati aperti (CSV, JSON, XML) 
3. Esportazione dei soli dati filtrati
4. Possibilità di visualizzare anche file collocati su server diverso dal proprio
5. Eliminazione di alcune opzioni inutili
6. Modifiche minori 

= Versione 1.1.0 25/01/2016 =

* Primo rilascio su wordpress.org 

