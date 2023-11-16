
Eine std lib auf dem root level
-> sort auch die db und daten, etc.
-> pages auf dem level des Tools selbst
-> jedes modul hat eine eigene CLS
-> jedes modul fungiert als eine eigene seite (mit eigener sub-domain).

Wir starten mit dem Account-Management modul.
Dann kommt das Dialogue modul.

Die requests bleiben so.
Jede Page eine eigene seite.
Jedes modul hat seine eigenen tests.

-> jedes modul hat seine eigenen Klassen und muss dann halt mit dem modul name
-> space darauf zugreifen.