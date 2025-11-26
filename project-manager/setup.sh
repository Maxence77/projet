#!/bin/bash

# Couleurs pour le texte
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}=== Installation des dépendances et configuration du projet ===${NC}"

# 1. Installation des paquets nécessaires
echo -e "${GREEN}[1/4] Installation de PHP et MySQL...${NC}"
apt-get update
apt-get install -y php php-mysql mariadb-server

# 2. Démarrage du service MySQL
echo -e "${GREEN}[2/4] Démarrage du service MySQL...${NC}"
service mysql start

# 3. Configuration de la base de données
echo -e "${GREEN}[3/4] Configuration de la base de données...${NC}"
# Création de la base et de l'utilisateur
mysql -u root <<EOF
CREATE DATABASE IF NOT EXISTS gestion_projet;
CREATE USER IF NOT EXISTS 'gestion_user'@'localhost' IDENTIFIED BY 'secret123';
GRANT ALL PRIVILEGES ON gestion_projet.* TO 'gestion_user'@'localhost';
FLUSH PRIVILEGES;
EOF

# Importation des tables
mysql -u root gestion_projet < sql/database.sql

if [ $? -eq 0 ]; then
    echo -e "${GREEN}Base de données configurée avec succès.${NC}"
else
    echo -e "${RED}Erreur lors de la configuration de la base de données.${NC}"
    exit 1
fi

# 4. Information pour le lancement
echo -e "${GREEN}[4/4] Installation terminée !${NC}"
echo ""
echo -e "Pour lancer le serveur, exécutez la commande suivante dans le dossier du projet :"
echo -e "${GREEN}php -S localhost:8000${NC}"
