- delete news entries 
- add order
- add links & images to messages
- dont be able to start a dialogue with yourself
- dont start a dialogue, when th doialoge desc is empty


-> 

Was kann man man technisch machen, das keine user input features erfordert.



## Bugs
- nicht dieselbe message zweimal abschicken
- Warum ist da json in den messages ab der dritten?
- Erklärung des Runden-verhaltens
- einklappen der info-boxes bei default, außer bei den ersten 2 logins
- Count number of logins per account
- Create 2 needed users dialoge bug

### Querverweise von Dialogen

### Highlighting von den besten Messages/teilen

### Jede einzelne Message kategorisieren (3 dimensionen)
- welcher zeitraum 
- welche konzepte 
- welche existenzen sind betroffen/berührt
- das ganze wie mit den hashtags halten und beim 
  Schreiben Vorschläge machen
- später kann man auch vorschläge berechnen, anhand vorangeganener daten
  und der entfernung der worte
- man muss mindestens ein hashtag je kategorie pro message hinzufügen 
  die werden dann später mit bewertet

### Entwurf-Feld hinpacke, wenn man grade nicht absenden kann
- kein leere message absendenden
- Nochmal ein betätigungs-overlay wo die message dann in geparster 
  form ist, wo man das absenden bestätigen muss 
- Das overlay JS pure machen (ajax request)
- Ein notiz-feld anbieten, dass konstant ist, den ganzen dialog und privat

### Alle dialoge mit einer person zusammen fassen 
- aus & einklapp bar mit js

### Alle dialoge einer interessengruppe zusammen fassen
- aus & einklapp bar mit js

### Chatgpt als moderator
-> chatgpt für zusammenfassungen

### Andere einladen per mail

### Angebot-Markt von Themen über die man schnacken möchte
(das kann man erstmal nur über die beschreibung im profil machen)

### Wortsuche in den profil-texten

### Wortsuche in den dialogen??

### Einzelne zeilen in den messages hervorheben
-> sich auf einzelne Zeilen beziehen

### Commands: require link
- ref other messages/dialogues

### Word definitions
- freely select some or create new ones

### Interessengruppen
- Dynamisch anlegen udn beitreten
- nutzer danach sortiere

### Comments about dialogue messages 

### Add abstract after dialogue has ended

### Add attention history, what dialogues have been read

### Community notes only for corrcting mistakes

### Limited number of emojis per message
- limited number of links possibly

### Delete news entry

### Log actions and all stuff and display statistics about it
-> what links are cliked, what is done, etc.
-> this way we can also get some idea about the usage-patterns

### Private dialogues 

### Rate messages for quality

### Leagues 

### Dialogue-patterns

### DB-backup system

### DB corruption detection system

### email verification

### start page explanation

### follow accounts

### follow dialogues
- then you get news on new messages

### Get information about dialogues per mail once a day if you whish

### Get information about dialogues your turn is now (also per mail if you whish)

### end dialogues 

### delete dialogues?

### Full json API

### Mobile style

### Publish private dialogues afterwards

### Write reflection and append it to a dialogue

### multiple members per dialogue

### Test-Suite
- test dataclasses: Create test-dataclass-twin-file


# Dialogue

## Until Release
-> some testdata
-> more explanation

## Until open source
-> update readme (mostly done)
-> add comments into every file (dataclass, dataclasses and requests)
-> create website with dev infos
-> create website with the next features
-> create website with tasks

# Ein Ordner wo ich einzelne Features als Mark-down datei beschreibe

# STUFF OTHER PEOPLE CAN DO:
# DAS Wort definieren Feature
# Das Join feature
# Das comunity notes Feature
# Commands -> the interpreter is a great place to start contributing
# Write open tests -> i write basic test-stuff and people can write some tests
# Style and UX/UI improvements
# small additional features
# filter improvements
# security improvements (escaping, sonderfälle, ...



-> check-functions and constraints on read and write
-> the log on error and send mail to admin
ADD SYSTEM to log data corruption -> so we can fix the db by hand if
something goes wrong



-> What turn it is
-> later add grace period where you can add changes


-> Draft feld, wo man schonmal ideen únd notizen reinpacken kann
solange man nicht dran ist
-> draft mode, den man speichern kann aber noch bestätigen muss
bevor man ihn offiziell abgesendet hat

-> Accept invitation
-> Accept join_request
-> Create Join request
-> search by word
-> search by user
-> close dialogue
-> Search by state (open, closed, finished)
-> news


-> Existenz Space
-> Konzept Space
-> Zeit-Horizont


-> add bücher and other zitate sachen


-> follow user
-> write comment on message of foreign dialogue

-> later limited numbers of words OR messages
-> challenges




-> interpreter nutzen
-> auch für escape gleich alles, kann ja erstmal leer sein

-> Class based/Semantics based Code order


Ich sollte das so bauen, dass es maximal flexibel bleibt.
-> Das heißt Klassenbaisert(3 abschnitte:daten-mode; logik; view) und Daten-Entries ertsellen so das sman später
sachen hinzufügen kann.


- Login / Registration.
- Create Profile: description for profile
- create dialoge-entry
- invite member/ accept invitation; set settings
  - (number of days until next message needed)
- Member search
- Follow members
- set dialoge to done
- private dialoge
- news-page where you see if somebody has written something in your dialoges
- rate done dialoges


## Features for later
-> Multiple personas to use and create
-> Multiple dimensions of ratings
-> Categories for dialoges to sort them into
-> Wörter die man verlinken kann, als erklärungen wie man die wörter meint
Das kann dann eingeblendet werden
-> Man kann auch bilder, links usw. nutzen um viel besser narrative darzustellen
-> Kommentare auf grade laufende Dialoge & wenn sie fertuig sind, einzelne nachrichten
ergänzen, auch community notes

