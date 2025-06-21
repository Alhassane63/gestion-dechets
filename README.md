# ♻️ Application de Gestion des Déchets Communautaire

## Description

Cette application Symfony permet de gérer les déchets dans une communauté locale à travers trois types d'utilisateurs :

- 👤 **Administrateur** : gère les utilisateurs, les points de collecte, les signalements et visualise des statistiques.
- 🧍‍♂️ **Citoyen** : signale des points de déchets à nettoyer, consulte l’historique des collectes, gagne des points.
- 🚛 **Agent de collecte** : visualise ses missions de collecte, les zones assignées et enregistre les interventions réalisées.

---

## 🛠️ Fonctionnalités

### 1. Interface Administrateur
- Gestion des utilisateurs (ajout, modification, suppression)
- Ajout et suivi des **zones** et **points de collecte**
- Visualisation des signalements citoyens
- Suivi des missions de collecte
- Statistiques (graphiques, cartes interactives)

### 2. Interface Citoyen
- Signalement d’un point de déchet avec localisation (Mapbox)
- Historique des signalements
- Système de points et classement des quartiers
- Suivi des collectes dans son quartier

### 3. Interface Agent de Collecte
- Liste des collectes à effectuer
- Validation des points de collecte traités
- Suivi des interventions réalisées

---

## ⚙️ Installation

### Prérequis
- PHP 8.1+
- Composer
- Symfony CLI
- MySQL / MariaDB
- Node.js (si assets à compiler)

### Étapes

```bash
# 1. Cloner le projet
git clone https://github.com/Alhassane63/gestion-dechets.git
cd gestion-dechets

# 2. Installer les dépendances PHP
composer install

# 3. Créer la base de données
php bin/console doctrine:database:create

# 4. Lancer les migrations
php bin/console doctrine:migrations:migrate

# 5. Lancer le serveur
symfony serve
