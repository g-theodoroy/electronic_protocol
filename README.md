# electronic_protocol

<img src="https://cloud.githubusercontent.com/assets/23439035/22380227/4f839c3a-e4c5-11e6-91fc-457f55281e6e.png" width="99%">

# Ηλεκτρονικό Πρωτόκολλο Σχολείου
- **Πρωτοκόλληση** Εισερχομένων και Εξερχομένων εγγράφων
- **Επισύναψη** Αρχείων και **καθορισμός Ημερομηνίας Διατήρησης - Καταστροφής** ανάλογα με το Φάκελο Αρχειοθέτησης **Φ.**
- **Φάκελοι Αρχειοθέτησης** και **Χρόνος Διατήρησης Εγγράφων**. Δυνατότητα τροποποίησης ανάλογα με τις εκάστοτε εγκυκλίους
- **Αναζήτηση** Πρωτοκόλλου, **Ανάκτηση - Διαγραφή** συνημμένων αρχείων
- **Εκτύπωση Απόδειξης** Κατάθεσης Πρωτοκόλλου
- Εκτύπωση Ηλεκτρονικού Πρωτοκόλλου για **βιβλιοδέτηση**
- Εκτύπωση λίστας εγγράφων για **Εκκαθάριση Αρχείου** μετά τη λήξη Διατήρησης αυτών
- **Backup** βάσης δεδομένων, εύκολο κατέβασμα για φύλαξη αυτών
- **Διαχείριση Χρηστών** με ρόλους "Διαχειριστής", "Αναθέτων", "Συγγραφέας", "Αναγνώστης" και ανάλογη πρόσβαση
- **Σύστημα αναθέσεων**: Ο "Διαχειριστής" και "Αναθέτων" μπορούν να αναθέτουν προς Διεκπεραίωση Πρωτόκολλα σε "Συγγραφείς"...
- **Πρωτοκόλληση email**: Σύνδεση με λογαριασμό email. Επισκόπηση εισερχομένων μηνυμάτων και των συνημμένων αρχείων και Πρωτοκόλληση με αποθήκευση των επιλεγμένων

#
# Βίντεο 
https://www.youtube.com/watch?v=Xl6qhbJWTGs


#
# Οδηγίες Χρήσης

https://drive.google.com/file/d/0B2ACFOVDi2ORWmZjUGNmQTNpVlk/view?usp=sharing

#

# Γρήγορη εγκατάσταση και εκτέλεση με Docker

Ο πιο εύκολος και γρήγορος τρόπος εγκατάστασης και εκτέλεσης είναι με τη χρήση του docker, ακολουθώντας τα παρακάτω βήματα:

- Εγκατάσταση του [Docker](https://docs.docker.com/get-docker/)
- Κλωνοποίηση του repo (```git clone https://github.com/g-theodoroy/electronic_protocol.git``` και είσοδος στο folder ```electronic_protocol```)
- Δημιουργία αρχείου ```.env``` (από πρότυπο ```.env-docker```)
- Τρέξιμο των παρακάτω εντολών:
- ```docker run --rm -v $(pwd):/app composer install --no-dev --ignore-platform-req=ext-gd```
(για εγκατάσταση των εξαρτήσεων του composer)
- ```docker compose up -d``` (για να τρέξει το σύστημα στο background (daemon))
- ```docker compose exec app php artisan key:generate``` (για δημιουργία νέου κλειδιού στο laravel)
- ```docker compose exec app php artisan migrate:refresh --seed``` (για δημιουργία πινάκων στη βάση)
- ```docker compose exec app php artisan config:cache``` (για ανανέωση cache)

Αυτό ήταν!
Το σύστημα είναι πλέον έτοιμο να δουλέψει στη διεύθυνση: ```http://localhost```



**ΠΑΡΑΤΗΡΗΣΕΙΣ**: 
- Με τον παραπάνω τρόπο η εφαρμογή είναι πλήρως λειτουργική και το μόνο που εγκαθιστούμε στο σύστημα είναι το Docker. Δε χρειάζεται εγκατάσταση Laravel, Web server, βάσης, ούτε παραμετροποίηση αυτών. Συνεπώς, η μέθοδος αυτή προτείνεται και για γρήγορη δοκιμή.
- Τα παραπάνω εκτελούνται μόνο την πρώτη φορά. Η εφαρμογή στο εξής θα "σηκώνεται" με κάθε restart. Αν για κάποιο λόγο δεν "ανέβει", εκτελούμε ```docker compose up -d``` στο φάκελο που βρίσκεται το ```docker-compose.yml```. Για να "κατεβάσουμε" την εφαρμογή, γράφουμε: ```docker compose down```.
- Αν θέλουμε να τρέξει η εφαρμογή σε άλλο port (όχι το 80), αλλάζουμε την παράμετρο APP_PORT στο .env
- Αν χρησιμοποιούμε παλιότερη έκδοση του docker, αντικαθιστούμε το ```docker compose``` με ```docker-compose``` παντού.
- Λόγω της φύσης του docker, ενδείκνυται εγκατάσταση με τη μέθοδο αυτή *σε σύστημα με λειτουργικό Linux*.

#
# Εγκατάσταση

Για την εγκατάσταση του framework laravel υπάρχουν διαθέσιμοι αρκετοί οδηγοί στο υπερκείμενο:

#### Για Debian 9:

https://tecadmin.net/install-laravel-on-debian-9-stretch/

#### Για Centos 7:

https://tecadmin.net/install-laravel-framework-on-centos/

#### Για Ubuntu:

https://websiteforstudents.com/install-laravel-php-framework-on-ubuntu-16-04-17-10-18-04-with-apache2-and-php-7-2-support/

#### Χρήση xampp

Αν θέλετε να χρησιμοποιήσετε xampp σε linux ή Windows ρίξτε μια ματιά στους επόμενους υπερσυνδέσμους:

https://www.5balloons.info/how-to-install-laravel-5-5-with-xampp-on-linux/

https://www.5balloons.info/install-laravel-5-7-xampp-windows/

#### Παρατηρήσεις

Σε όλους τους οδηγούς είναι σημαντικό να προσέξουμε και να **εγκαταστήσουμε τις απαραίτητες εξαρτήσεις**.

Όταν φτάσουμε στην εγκατάσταση του laravel

```git clone https://github.com/laravel/laravel.git```

ή

```sudo composer create-project laravel/laravel MyProject --prefer-dist```

**αντικαθιστούμε** με την παρακάτω εντολή:

```sudo git clone https://github.com/g-theodoroy/electronic_protocol.git .```

Οι οδηγοί αναφέρονται στη **mysql (mariaDB)** σαν Βάση Δεδομένων. Μπορείτε να επιλέξετε και τη **sqlite** (η πρότασή μου για ευκολία)

Για να ανακατευθύνουν τον **apache** server χρησιμοποιούν τη ντιρεκτίβα **VirtualHost**.

Προσωπικά Θεωρώ πιο εύκολο να ρυθμιστεί με τη χρήση της ντιρεκτίβας **Alias**


#### Ρυθμίσεις php

Θα πρέπει να ενεργοποιηθούν στο ```php.ini``` οι ακόλουθες extentions:

```
extension=gd
extension=fileinfo
extension=imap
extension=mbstring
extension=exif
extension=openssl
extension=pdo_sqlite
```

Για χρήση με mysql ή mariaDB και οι παρακάτω:

```
extension=mysqli
extension=pdo_mysql

```

#### Ρύθμίση email με το Gmail

Για να ρυθμίσετε το email συμπληρώστε ανάλογα τα στοιχεία στο αρχείο ```.env```.

γραμμές 26 έως 40:
```
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=gm@ilusern@me
MAIL_PASSWORD=gm@ilp@ss
MAIL_ENCRYPTION=tls

IMAP_HOST=imap.gmail.com
IMAP_PORT=993
IMAP_ENCRYPTION=ssl
IMAP_VALIDATE_CERT=false
IMAP_USERNAME=gm@ilusern@me
IMAP_PASSWORD=gm@ilp@ss
IMAP_DEFAULT_ACCOUNT=default
IMAP_PROTOCOL=imap
```


#### Ρύθμίση email με το ΠΣΔ Πανελλήνιο Σχολικό Δίκτυο

```
MAIL_DRIVER=smtp
MAIL_HOST=mail.sch.gr
MAIL_PORT=25
MAIL_USERNAME=sch_usern@me
MAIL_PASSWORD=sch_p@ss
MAIL_ENCRYPTION=

IMAP_HOST=mail.sch.gr
IMAP_PORT=143
IMAP_ENCRYPTION=tls
IMAP_VALIDATE_CERT=true
IMAP_USERNAME=sch_usern@me
IMAP_PASSWORD=sch_p@ss
IMAP_DEFAULT_ACCOUNT=default
IMAP_PROTOCOL=imap
```

IMAP εναλλακτική ρύθμιση

```
IMAP_HOST=mail.sch.gr
IMAP_PORT=993
IMAP_ENCRYPTION=ssl
IMAP_VALIDATE_CERT=false
IMAP_USERNAME=username SCH
IMAP_PASSWORD=pass SCH
IMAP_DEFAULT_ACCOUNT=default
IMAP_PROTOCOL=imap
```



**Για να εφαρμοστούν οι αλλαγές** που κάνατε πρέπει να καθαρίσετε τις ήδη αποθηκευμένες ρυθμίσεις **config** με:
``` php artisan config:clear```

**Αν το smtp Gmail δεν συνδέεται**

Αν δεν μπορείτε να στείλετε email με το gmail λογαριασμό σας ενεργοποιήστε την πρόσβαση σε "λιγότερο ασφαλείς" εφαρμογές. Κάντε login στο gmail λογαριασμό σας και επισκεφθείτε τον επόμενο σύνδεσμο:

https://myaccount.google.com/lesssecureapps


**Imap**

Μπορείτε να ρυθμίσετε περισσότερους λογαριασμούς imap για λήψη email στο αρχείο ```config/imap.php```. Υπάρχουν ήδη λογαριασμοί με τις κατάλληλες ρυθμίσεις και πρέπει να συμπληρωθεί μόνο **username** και **password**. Ο Διαχειριστής μπορεί να επιλέγει τον ενεργό λογαριασμό email.

Τα μηνύματα από τον φάκελο **INBOX** μεταφέρονται ή στον φάκελο **INBOX/beenRead** αν δεν Πρωτοκολληθούν, είτε στον **INBOX/inProtocol** όταν Πρωτοκολληθούν.


#### Προτεινόμενη ρύθμιση του λογαριασμού email

Για να ελέγχονται όλα τα email από το Ηλ. Πρωτόκολλο διαβάζονται όλα τα εισερχόμενα μηνύματα είτε είναι σεσημασμένα σαν διαβασμένα είτε όχι. Τα μηνύματα που επεξεργάστηκαν με το Ηλ. Πρωτόκολλο μεταφέρονται σε φακέλους όπως περιγράφεται παραπάνω. 

Τα σχολεία έχουν ένα λογαριασμό με τη μορφή **mail@[όνομα σχολείου].[νομός].sch.gr**. Αν δεν θέλουμε να γίνεται σε αυτό το λογαριασμό η μεταφορά των email στους φακέλους μπορούμε να φτιάξουμε ένα νέο λογααριασμό (πχ toGymnasioMou@gmail.com) και να ρυθμίσουμε τον επίσημο λογαριασμό του σχολείου να προωθεί τα email στο νέο λογαριασμό. Εκεί θα δουλεύουμε με το Ηλ.Πρωτόκολλο. Αυτό το λογαριασμό μπορούμε να τον συντηρούμε με ένα πρόγραμμα Ηλ. Αλληλογραφίας (διαγραφή παλιών ή μη σημαντικών email ).



### Ενδεικτικά σας παρουσιάζω αναλυτικά τις ενέργειες που πρέπει να γίνουν για εγκατάσταση σε Ubuntu 22.04


## Εγκατάσταση σε Ubuntu 22.04

Ενημερώθηκε 19 Νοε 2022

#### Ενημέρωση του συστήματος
```
sudo apt update
sudo apt upgrade
```

#### Εγκατάσταση apache, php, sqlite3 ...
```
sudo apt install mc apache2 libapache2-mod-php sqlite3 curl git -y

sudo apt install php php-sqlite3 php-imap  php-dev php-zip php-curl php-pear php-mbstring php-mysql php-gd php-xml  -y
```

#### Ρύθμιση της php (php.ini) για το laravel
αντικαταστείστε τις αγκύλες [έκδοση της php] με τον αντίστοιχο αριθμό (**8.1** σήμερα, 19 Νοε 2022)
```
sudo nano /etc/php/[έκδοση της php]/apache2/php.ini
```
αλλάζουμε τις τιμές των παρακάτω παραμέτρων ως εξής:

memory_limit = 256M

upload_max_filesize = 64M

#### Εγκατάσταση composer
```
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

#### Δημιουργία φακέλου ```/opt/protocol``` και κατέβασμα του Ηλ. Πρωτοκόλλου από το github 
```
sudo mkdir /opt/protocol
cd /opt/protocol
sudo git clone https://github.com/g-theodoroy/electronic_protocol.git .
sudo composer install --no-dev
```

#### Ρύθμιση δικαιωμάτων του φακέλου ```/opt/protocol``` στον χρήστη www-data
```
sudo chown -R www-data:www-data /opt/protocol/
sudo chmod -R 755 /opt/protocol/
```

#### Δημιουργία του αρχείου με τη ρύθμιση alias
```
sudo mkdir /etc/apache2/alias
sudo nano /etc/apache2/alias/protocol.conf
```
Γράφουμε στο αρχείο ```protocol.conf``` τα παρακάτω:
```
Alias /protocol "/opt/protocol/public"

<Directory "/opt/protocol/public">
        DirectoryIndex index.php
        AllowOverride All
        Options FollowSymlinks
        Require all granted
</Directory>
```

#### Ρύθμιση του apache να διαβάσει το αρχείο protocol.conf
```
sudo nano /etc/apache2/apache2.conf
```
Προσθήκη στο τέλος
```
Include "alias/*"
```

#### Ρύθμιση και επανεκκίνηση apache
```
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Το Ηλ. Πρωτόκολλο είναι προσβάσιμο στον υπερσύνδεσμο
http://localhost/protocol


#### Χρήση και ρύθμιση της mariaDB (mysql)

```
sudo apt install mariadb-server
sudo systemctl enable mariadb --now
sudo mysql_secure_installation
sudo systemctl restart mariadb


mysql -u root -p

create database protocol;
grant all privileges on protocol.* to 'protocol_user'@'localhost' identified by '12345678';
flush privileges;
exit;

```

#### Ρυθμίσεις Πρωτοκόλλου για mariaDB (mysql)


```
cd /opt/protocol/

sudo nano .env
```

Τροποποιούμε στο αρχείο ```.env``` τα παρακάτω:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=protocol
DB_USERNAME=protocol_user
DB_PASSWORD=12345678
```

Γέμισμα της ΒΔ

```
sudo php artisan migrate --seed
sudo systemctl restart apache2

sudo rm /opt/protocol/storage/conf/.denyregister

```


## Λειτουργικό σύστημα Windows με εγκατεστημένο Xampp, Wamp, ...


Υπάρχουν διάφορες εφαρμογές που στήνουν στα Windows Apache και Mysql servers για παραγωγή και δοκιμή webapp όπως: Xampp, Wamp, ...


Θα περιγράψουμε ενδεικτικά πως μπορεί να γίνει αυτό σε Xampp. Με παρόμοιο τρόπο μπορεί να γίνει και σε άλλες εναλλακτικές λύσεις.



### Βίντεο εγκατάστασης του Ηλ. Πρωτοκόλλου σε windows 10

https://youtu.be/xvDHwjHb7wc


## Εγκατάσταση σε Xampp


Εγκαταστήστε το [Xampp](https://www.apachefriends.org/)


Εγκαταστήστε τον [Composer](https://getcomposer.org/download/)


Κατεβάστε το [Ηλεκτρονικό Πρωτόκολλο](https://github.com/g-theodoroy/electronic_protocol/archive/master.zip)


### Ρυθμίσεις
Αποσυμπιέστε το αρχείο **electronic_protocol-master.zip** σε όποιο φάκελο επιθυμείτε. Εδώ θα χρησιμοποιήσουμε τον δίσκο **C:\\**. Μετονομάστε το φάκελο **C:\electronic_protocol-master** σε ένα πιο σύντομο όνομα πχ protocol, ...  Στο τέλος θα έχουμε τα αρχεία του Ηλ.Πρωτοκόλλου στον φάκελο **C:\protocol**.


Ανοίξτε τον φάκελο (**C:\protocol**) με την Εξερεύνηση των windows και αλλάξτε τις τιμές των παρακάτω μεταβλητών στα ακόλουθα αρχεία:

## Αν χρησιμοποιείτε sqlite

#### .env

 - γραμμή 9:
   - DB_CONNECTION=sqlite
 - γραμμή 29:
   - MAIL_USERNAME=**gm@ilusern@me**
 - γραμμή 30:
   - MAIL_PASSWORD=**gm@ilp@ss**


## Αν χρησιμοποιείτε mysql

#### .env

- γραμμή 9:
   - DB_CONNECTION=**mysql**
 - γραμμή 12:
   - DB_DATABASE=**d@t@b@se**
 - γραμμή 14:
   - DB_PASSWORD=**p@ssw@rd**
 - γραμμή 29:
   - MAIL_USERNAME=**gm@ilusern@me**
 - γραμμή 30:
   - MAIL_PASSWORD=**gm@ilp@ss**
#### config/database.php
 - γραμμή 48:      
   - 'database' => env('DB_DATABASE', '**d@t@b@se**'),
 - γραμμή 50:      
   - 'password' => env('DB_PASSWORD', '**p@ssw@rd**'),
 
 
Αν δεν αλλάξατε κάτι η mysql στο Xampp έχει εξορισμού:
- username root (αυτό δεν θέλει αλλαγή)
- password      (κενό)

Δώστε ένα όνομα στη d@t@b@se πχ: protocol


#### public/.htaccess

**Σεπτέμβριος 2019**. Παρατηρήθηκε ότι δεν ανακατευθύνονται σωστά τα urls. Ενώ βλέπουμε την αρχική σελίδα μόλις πατήσουμε "Εγγραφή" ή "Είσοδος" βγάζει **Internal server error**.

Προσθέτουμε κάτω από τη γραμμή 6 ```RewriteEngine On``` το ```RewriteBase /protocol```  
```
        RewriteEngine On
        RewriteBase /protocol
```
όπου **protocol** το όνομα που χρησιμοποιούμε για ψευδώνυμο **Alias** όπως περιγράφεται πιο κάτω.


### Ρύθμιση Apache του Xampp να ανακατευθύνεται στο Ηλ. Πρωτόκολλο


Αυτό γίνεται με δύο τρόπους:
- Ρύθμιση virtual-host του Apache
- Ρύθμιση μέσω της ντιρεκτίβας alias (ψευδώνυμο)


**Προσωπικά βρίσκω πιο εύκολο το 2ο (Alias)**


Ανοίγουμε με την εξερεύνηση των Windows τον φάκελο ```C:\xampp\apache\conf```.

Δημιουργούμε ένα Νέο φάκελο με όνομα ```alias```.

Στον φάκελο ```C:\xampp\apache\conf\alias``` δημιουργούμε ένα αρχείο conf πχ: ```protocol.conf```. Ίσως είναι πιο εύκολο να αντιγράψουμε ένα άλλο αρχείο conf, να το μετονομάσουμε και να διαγράψουμε τα δεδομένα.

Εισάγετε στο αρχείο ```C:\xampp\apache\conf\alias\protocol.conf``` που μόλις δημιουργήσατε τα παρακάτω και αποθηκεύστε:
```
Alias /protocol "C:\protocol\public"

<Directory "C:\protocol\public">
        DirectoryIndex index.php
        AllowOverride All
        Options FollowSymlinks
        Require all granted
</Directory>
```
Ανοίξτε το αρχείο ```C:\xampp\apache\conf\httpd.conf``` και για να συμπεριλάβετε τις ρυθμίσεις προσθέστε στο τέλος:
```
Include "conf/alias/*"
```



### Ρυθμίσεις



#### Ρύθμιση php

Στο αρχείο **C:\xampp\php\php.ini** και στις γραμμές 915 και κάτω

ενεργοποιείστε τις ακόλουθες extensions διαγράφοντας το **;** μπροστά από την κάθε γραμμή

extension=gd

extension=imap

extension=mbstring


**Αν δουλέυουμε με sqlite σαν ΒΔ**

extension=pdo_sqlite

extension=sqlite3


**Αν δουλέυουμε με mysql σαν ΒΔ**

extension=pdo_mysql

extension=mysqli 



#### Ρύθμιση mysql

Εκκινήστε το Xampp και τους servers apache και mysql 

Ανοίξτε το πρόγραμμα  phpMyAdmin του Xampp και δημιουργήστε την βάση δεδομένων που μόλις ονομάσατε (πχ: protocol)



#### Εγκατάσταση βιβλιοθηκών

Ανοίξτε την κονσόλα των Windows: πρόγραμμα **Cmd**. Μεταβείτε στον φάκελο που έχετε βάλει το Ηλ.Πρωτόκολλο (πχ protocol).
```
cd C:\protocol
```
Eκτελέστε τις παρακάτω εντολές για να εγκατασταθούν τα απαραίτητα:
```
composer install --no-dev

php artisan key:generate
```
## με mysql
```
php artisan migrate:refresh --seed
php artisan db:seed --class=KeepvaluesTableSeeder
```
## με sqlite
Η βάση είναι ήδη έτοιμη


Κατευθύνετε τον φυλομετρητή σας στη σελίδα http://localhost/protocol


# 
# Ασφάλεια δεδομένων

Θα πρέπει να φροντίσετε να κρατάτε τακτικά backup των δεδομένων σας. Επίσης ο server στον οποίο θα εγκαταστήσετε την εφαρμογή να έχει τουλάχιστον συστοιχία δίσκων RAID1. 

#
# Μέγεθος συνημμένων αρχείων

Για να ορίσετε το μέγιστο μέγεθος των αρχείων που αποθηκεύονται πάνω από τα 2 ΜΒ που είναι η εξ ορισμού ρύθμιση της php πρέπει να αλλάξετε την τιμή της μεταβλητής **upload_max_filesize**  στο αρχείο ```php.ini``` σε  τιμή μεγαλύτερη από **2M**, πχ: 4Megabytes.

```upload_max_filesize = 4M```

Θα βρείτε αυτό το αρχείο ανάλογα με το λειτουργικό σύστημα σε
- Linux           =>  ```/etc/php/php.ini```
- Windows + Xampp =>  ```C:\xampp\php\php.ini```


#
# Ενημέρωση του Ηλ. Πρωτοκόλλου

Όταν υπάρξουν αλλαγές στον κώδικα στο github τότε το Ηλ. Πρωτόκολλο εμφανίζει ένα μήνυμα ότι υπάρχουν αλλαγές. Ο Διαχειριστής θα πρέπει να τις κάνει χειροκίνητα. Προσπαθώ όταν ανεβάζω τις αλλαγές να περιγράφω αναλυτικά τί αλλαγή έγινε και σε ποιά αρχεία. Αν δεν επηρεάστηκε ο κώδικας γράφω "Καμία αλλαγή στον κώδικα".

Σας προτείνω μια λύση που απλοποιεί τη δουλειά με το εργαλείο **git**. Στο παράδειγμα υποθέτουμε ότι έχει γίνει εγκατάσταση
σε **Debian 9 server**

Μπαίνουμε στο server ή συνδεόμαστε μέσω ssh από άλλο pc.

Πηγαίνουμε στον κατάλογο που εγκαταστήσαμε το Ηλ.Πρωτόκολλο πχ: protocol
```cd /usr/share/protocol```

Με την εγκατάσταση και την πρώτη εγγραφή χρήστη έχουν τροποποιηθεί κάποια αρχεία. Μπορούμε να τα δούμε αν πληκτρολογήσουμε ```git status```. Θα μας δώσει τα παρακάτω:
```
On branch master
Your branch is up-to-date with 'origin/master'.
Changes not staged for commit:
  (use "git add <file>..." to update what will be committed)
  (use "git checkout -- <file>..." to discard changes in working directory)

	modified:   .env
	modified:   config/database.php
	modified:   config/session.php
	modified:   public/.htaccess

Untracked files:
  (use "git add <file>..." to include in what will be committed)

	composer.lock
	storage/conf/.denyregister

no changes added to commit (use "git add" and/or "git commit -a")
```

Για να μη χάσουμε τις αλλαγές στα αρχεία κατά την ενημέρωση δίνουμε τις παρακάτω εντολές:
```
git stash
git pull
git stash pop
```
Κάντε κλικ στο menu: Διαχείριση->Ενημερώθηκε για να μην εμφανίζεται το μήνυμα που ειδοποιεί για ενημέρωση.

**Έτοιμοι!**


# 
# Ευχαριστίες
Το Ηλεκτρονικό Πρωτόκολλο χρησιμοποιεί με ευγνωμοσύνη τα:
- [Laravel](https://laravel.com/)
- [Bootstarp](http://getbootstrap.com/)
- [jQuery](https://jquery.com/)

Γεώργιος Θεοδώρου


### Εικόνες

<img src="https://cloud.githubusercontent.com/assets/23439035/22380174/282f5fa2-e4c5-11e6-9fc7-24fc2da66e06.png" width="99%">
<img src="https://user-images.githubusercontent.com/23439035/27009310-405d0402-4e93-11e7-9a40-309eb5720168.png" width="99%">
<img src="https://cloud.githubusercontent.com/assets/23439035/22380175/283495f8-e4c5-11e6-9190-1f674818a0b2.png" width="99%">
<img src="https://cloud.githubusercontent.com/assets/23439035/26480073/69b32634-41e0-11e7-9c22-d9f02d93acab.png" width="99%">
<img src="https://cloud.githubusercontent.com/assets/23439035/22380182/28599768-e4c5-11e6-9c98-4ee0b4d7bfb9.png" width="99%">
<img src="https://cloud.githubusercontent.com/assets/23439035/22380173/282c6144-e4c5-11e6-9011-4b469a3e8104.png" width="99%">
<img src="https://cloud.githubusercontent.com/assets/23439035/22380176/2839c35c-e4c5-11e6-985d-5b1dd0f81550.png" width="99%">
<img src="https://cloud.githubusercontent.com/assets/23439035/22380177/283cc8cc-e4c5-11e6-8ec4-dc65ffacecd9.png" width="99%">
<img src="https://cloud.githubusercontent.com/assets/23439035/22380179/284f0988-e4c5-11e6-9ae1-5b40bfac0ea3.png" width="99%">
<img src="https://cloud.githubusercontent.com/assets/23439035/22380178/284eef16-e4c5-11e6-97a7-22eeb612d65b.png" width="99%">
<img src="https://cloud.githubusercontent.com/assets/23439035/22380180/28553786-e4c5-11e6-97f3-efa7f3ac67da.png" width="99%">
<img src="https://cloud.githubusercontent.com/assets/23439035/22380181/2857e67a-e4c5-11e6-85e9-a41b940ac1ab.png" width="99%">
