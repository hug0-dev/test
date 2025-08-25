BTP_Manager est une application Symfony permettant la gestion des chantiers, équipes, ouvriers et affectations dans une entreprise de BTP. pour run le projet 
git clone https://github.com/ton-repo/BTP_Manager.git
cd BTP_Manager
composer install
Configure ensuite la base de données dans .env :  DATABASE_URL="mysql://user:password@127.0.0.1:3306/BTP_management?serverVersion=8.0"
Crée la base de données et applique les migrations : php bin/console doctrine:database:create et php bin/console doctrine:migrations:migrate
symfony server:start
Les documents relatifs au projet, notamment la base de données et les maquettes Figma, sont disponibles dans le dossier documentations.
