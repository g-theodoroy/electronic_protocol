# electronic_protocol
# Ηλεκτρονικό Πρωτόκολλο Σχολείου
- **Πρωτοκόλληση** Εισερχομένων και Εξερχομένων εγγράφων
- **Επισύναψη** Αρχείων και **καθορισμός Ημερομηνίας Διατήρησης - Καταστροφής** ανάλογα με το Φάκελο Αρχειοθέτησης **Φ.**
- **Φάκελοι Αρχειοθέτησης** και **Χρόνος Διατήρησης Εγγράφων**. Δυνατότητα τροποποίησης ανάλογα με τις εκάστοτε εγκυκλίους
- **Αναζήτηση** Πρωτοκόλλου, **Ανάκτηση - Διαγραφή** συνημμένων αρχείων
- **Εκτύπωση Απόδειξης** Κατάθεσης Πρωτοκόλλου
- Εκτύπωση Ηλεκτρονικού Πρωτοκόλλου για **βιβλιοδέτηση**
- Εκτύπωση λίστας εγγράφων για **Εκκαθάριση Αρχείου** μετά τη λήξη Διατήρησής αυτών
- **Backup** βάσης δεδομένων, εύκολο κατέβασμα για φύλαξη αυτών
- **Διαχείριση Χρηστών** με ρόλους "Διαχειριστής", "Συγγραφέας", "Αναγνώστης" και ανάλογη πρόσβαση

## Εγκατάσταση

### Προτεινόμενη μέθοδος

#### Εγκατάσταση σε φυσικό server του σχολείου όπου έχουμε πρόσβαση.

Λειτουργικό σύστημα Linux (Debian, Centos, Ubuntu, ...) με εγκατεστημένο Lamp (Linux, Apache, Mysql, Php).

Μπείτε στον server ή συνδεθείτε με ssh μέσω terminal
```
ssh {username}@{ip_server}
```
Γίνετε superuser
```su``` ή ```sudo su```


Κατεβάστε ένα από τα ακόλουθα script που ταιριάζει με τη διανομή σας
- Debian με Php5
```
wget http://okiroi.no-ip.org/downloads/laravelprotocol_debian_php5.sh
```
- Debian με Php7
```
wget http://okiroi.no-ip.org/downloads/laravelprotocol_debian_php7.sh
```
- Ubuntu
```
wget http://okiroi.no-ip.org/downloads/laravelprotocol_ubuntu_install.sh
```
- Centos
```
wget http://okiroi.no-ip.org/downloads/laravelprotocol_install_centos.sh
```

Αν έχετε άλλη διανομή τροποποιήστε ένα συγγενές script σύμφωνα με τις ανάγκες της διανομής



## Ευχαριστίες
Το Ηλεκτρονικό Πρωτόκολλο χρησιμοποιεί με ευγνωμοσύνη τα:
- [Laravel](https://laravel.com/)
- [Bootstarp](http://getbootstrap.com/)
- [jQuery](https://jquery.com/)

Γεώργιος Θεοδώρου
