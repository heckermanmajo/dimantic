
1. Aufschreiben der features die wir wollen
2. Erstellen der Konzeptdateien
   - architektur
   - Projekt-Ziel
3. Komponenten und klassen anlegen als mock + documentation
4. Migration
5. Implementieren der Komponenten für den MVP

- All tables need to be different, if you need to use traits or
  any other oop polymorphism stuff, you actually need to optimize 
  the database schema. (think wallet)

- add saveguard to savng errors into the db only try once, so we 
  dont get into a endless loop of saving errors.


