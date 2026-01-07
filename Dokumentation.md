# Dokumentation


## Schritte


### Datentabelle

    Muss foldende Feder zwingend enhalten

    - pwgen_user	varchar(20)
    - pwgen_id	varchar(20)
    - pwgen_hash	varchar(80)
    - id	varchar(80) (kann primary key sein)

### Vorkalkulieren von Daten

    `call fill_pwgen_precalc('username7',7,'ABCDEFGHJKLMNPRSTUVXYZ123456789',2000000)`
    `call fill_pwgen_precalc('uniqueid8',8,'0123456789',2000000)`

### Unique-ID füllen (optional)

    bsb Wahlscheinnnummer oder hier pwgen_id
    `call setPWGenRandom('data_table','uniqueid8',8,'pwgen_id');`


### Username füllen (optional)
    `call setPWGenRandom('data_table','username7',7,'pwgen_user');`

### Passwörter erzeugen

    In der UI des Datenstammes die Schaltfläsche pw_gen_command aufrufen
