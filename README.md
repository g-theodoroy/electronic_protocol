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

Εγκαταστήστε αν δεν είναι ήδη εγκατεστημένο το **wget** 
Debian, Ubuntu: ``` apt-get -y install wget ``` , Centos: ``` yum -y install wget```

Κατεβάστε ένα από τα ακόλουθα script που ταιριάζει με τη διανομή σας
- Debian με Php5
```
wget -O install_electronic_protocol_Debian_php5.sh "https://drive.google.com/uc?export=download&id=0B2ACFOVDi2ORU3p3ZXl6ekJISW8"
```
- Debian με Php7
```
wget -O install_electronic_protocol_Debian_php7.sh "https://drive.google.com/uc?export=download&id=0B2ACFOVDi2ORLVJVazJtbmtUYzg"
```
- Ubuntu
```
wget -O install_electronic_protocol_Ubuntu.sh "https://drive.google.com/uc?export=download&id=0B2ACFOVDi2ORWmFmSVkwN2xqdjg"
```
- Centos
```
wget -O install_electronic_protocol_Centos.sh "https://drive.google.com/uc?export=download&id=0B2ACFOVDi2ORejZEVU1fQWJRSWc"
```


Αν έχετε άλλη διανομή τροποποιήστε ένα συγγενές script σύμφωνα με τις ανάγκες της διανομής



## Ευχαριστίες
Το Ηλεκτρονικό Πρωτόκολλο χρησιμοποιεί με ευγνωμοσύνη τα:
- [Laravel](https://laravel.com/)
- [Bootstarp](http://getbootstrap.com/)
- [jQuery](https://jquery.com/)

Γεώργιος Θεοδώρου
