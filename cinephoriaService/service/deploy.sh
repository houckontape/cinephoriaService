#!/bin/bash

# Couleurs pour des messages lisibles
GREEN="\033[0;32m"
RED="\033[0;31m"
NC="\033[0m" # Pas de couleur

# Variable pour suivre l'état
ERROR_COUNT=0

echo -e "${GREEN}Vérification de la configuration du serveur pour l'application...${NC}"

# 1. Vérifier la version de PHP
echo -e "\n[1] Vérification de la version de PHP..."
REQUIRED_PHP_VERSION="8.2"
INSTALLED_PHP_VERSION=$(php -r "echo PHP_VERSION;")
if php -v >/dev/null 2>&1; then
  if [[ $(php -r "echo version_compare('$INSTALLED_PHP_VERSION', '$REQUIRED_PHP_VERSION', '>=');") -eq 1 ]]; then
    echo -e "${GREEN}PHP $INSTALLED_PHP_VERSION (OK)${NC}"
  else
    echo -e "${RED}Erreur : PHP $INSTALLED_PHP_VERSION trouvé. Version $REQUIRED_PHP_VERSION ou supérieure est requise.${NC}"
    ((ERROR_COUNT++))
  fi
else
  echo -e "${RED}PHP n'est pas installé ou non accessible.${NC}"
  ((ERROR_COUNT++))
fi

# 2. Vérifier les extensions PHP nécessaires
echo -e "\n[2] Vérification des extensions PHP..."
# ajouter les extension necessaire php ici pour passé les test
REQUIRED_EXTENSIONS=("pdo_mysql" "mbstring" "json" "xml" "curl")
for EXT in "${REQUIRED_EXTENSIONS[@]}"; do
  if php -m | grep -q "$EXT"; then
    echo -e "${GREEN}Extension $EXT : chargée (OK)${NC}"
  else
    echo -e "${RED}Erreur : L'extension $EXT n'est pas installée ou activée.${NC}"
    ((ERROR_COUNT++))
  fi
done

# 3. Vérifier la version de MySQL/MariaDB
echo -e "\n[3] Vérification de la version MySQL/MariaDB..."
REQUIRED_MYSQL_VERSION="5.7"
if command -v mysql >/dev/null 2>&1; then
  INSTALLED_MYSQL_VERSION=$(mysql -V | grep -oP '\d+\.\d+\.\d+')
  if [[ $(php -r "echo version_compare('$INSTALLED_MYSQL_VERSION', '$REQUIRED_MYSQL_VERSION', '>=');") -eq 1 ]]; then
    echo -e "${GREEN}MySQL/MariaDB $INSTALLED_MYSQL_VERSION (OK)${NC}"
  else
    echo -e "${RED}Erreur : MySQL/MariaDB $INSTALLED_MYSQL_VERSION trouvé. Version $REQUIRED_MYSQL_VERSION ou supérieure est requise.${NC}"
    ((ERROR_COUNT++))
  fi
else
  echo -e "${RED}Erreur : MySQL/MariaDB n'est pas installé ou non accessible.${NC}"
  ((ERROR_COUNT++))
fi

# 4. Vérifier les permissions sur les dossiers critiques
echo -e "\n[4] Vérification des permissions des dossiers..."
DIRECTORIES=("./public")
for DIR in "${DIRECTORIES[@]}"; do
  if [ -d "$DIR" ]; then
    if [ -w "$DIR" ]; then
      echo -e "${GREEN}Permissions OK sur le dossier $DIR${NC}"
    else
      echo -e "${RED}Erreur : Le dossier $DIR n'a pas les permissions en écriture.${NC}"
      ((ERROR_COUNT++))
    fi
  else
    echo -e "${RED}Erreur : Le dossier $DIR est manquant.${NC}"
    ((ERROR_COUNT++))
  fi
done

# 5. Vérification des variables d'environnement nécessaires
echo -e "\n[5] Vérification des variables d'environnement..."
REQUIRED_ENV_VARS=("APP_ENV" "DB_HOST" "DB_USER" "DB_PASSWORD" "DB_NAME")
for ENV_VAR in "${REQUIRED_ENV_VARS[@]}"; do
  if [[ -z "${!ENV_VAR}" ]]; then
    echo -e "${RED}Erreur : La variable d'environnement $ENV_VAR n'est pas définie.${NC}"
    ((ERROR_COUNT++))
  else
    echo -e "${GREEN}Variable $ENV_VAR définie (OK)${NC}"
  fi
done

# 6. Vérification de l'accès internet (via ping)
echo -e "\n[6] Tests d'accès réseau..."
PING_HOST="google.com"
if ping -c 1 "$PING_HOST" &>/dev/null; then
  echo -e "${GREEN}Accès réseau (ping $PING_HOST) : OK${NC}"
else
  echo -e "${RED}Erreur : Pas d'accès réseau (ping $PING_HOST).${NC}"
  ((ERROR_COUNT++))
fi

# 7. Vérifier si le serveur web (Apache ou Nginx) est actif
echo -e "\n[7] Vérification du serveur web (Apache ou Nginx)..."
if systemctl is-active --quiet apache2; then
  echo -e "${GREEN}Apache est actif${NC}"
elif systemctl is-active --quiet nginx; then
  echo -e "${GREEN}Nginx est actif${NC}"
else
  echo -e "${RED}Erreur : Aucun serveur web actif détecté (Apache/Nginx).${NC}"
  ((ERROR_COUNT++))
fi

# 8. Lancer le script createTable.php
echo -e "\n[8] Lancement du script createTable.php..."
CREATE_TABLE_SCRIPT="./createTable.php"

if [ -f "$CREATE_TABLE_SCRIPT" ]; then
  if php "$CREATE_TABLE_SCRIPT"; then
    echo -e "${GREEN}Script createTable.php exécuté avec succès.${NC}"
  else
    echo -e "${RED}Erreur lors de l'exécution du script createTable.php.${NC}"
    ((ERROR_COUNT++))
  fi
else
  echo -e "${RED}Erreur : Le script createTable.php est introuvable.${NC}"
  ((ERROR_COUNT++))
fi

# 9. Résultat final
echo -e "\n${GREEN}--- Résultat final ---${NC}"
if [ "$ERROR_COUNT" -eq 0 ]; then
  echo -e "${GREEN}Toutes les vérifications sont passées avec succès.${NC}"
else
  echo -e "${RED}Nombre total d'erreurs : $ERROR_COUNT.${NC}"
  echo -e "${RED}Veuillez corriger les erreurs ci-dessus avant d'utiliser l'application.${NC}"
fi
