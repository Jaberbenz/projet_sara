brew install httpd php@7.4

2. Configurer Apache
Vous devez configurer Apache pour utiliser PHP et définir le dossier racine de votre site web. Vous pouvez modifier le fichier de configuration d'Apache (généralement situé à /usr/local/etc/httpd/httpd.conf) en suivant ces étapes :

Chargez le module PHP en ajoutant la ligne suivante :

LoadModule php7_module /usr/local/opt/php@7.4/lib/httpd/modules/libphp7.so

Modifiez DocumentRoot et <Directory> pour pointer vers votre dossier de projet, par exemple /var/www/html/


sudo apachectl restart

brew install mariadb

mysql -u root

CREATE DATABASE covoiturage;
GRANT ALL PRIVILEGES ON covoiturage.* TO 'user'@'localhost' IDENTIFIED BY 'password';
FLUSH PRIVILEGES;
EXIT;

mysql -u user -p covoiturage < chemin_vers_le_fichier.sql

Modifiez votre fichier PHP pour utiliser les paramètres de connexion locaux. Assurez-vous que les constantes DB_HOST, DB_USER, DB_PASSWORD, et DB_DATABASE sont correctement configurées pour l'environnement local :

define("DB_HOST", "localhost");
define("DB_USER", "user");
define("DB_PASSWORD", "password");
define("DB_DATABASE", "covoiturage");
