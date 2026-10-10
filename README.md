# UNO — Symfony

Jeu de UNO dans le navigateur : une partie à quatre joueurs, toi contre trois ordinateurs. Toute la logique du jeu tourne côté serveur en PHP.

## Stack

PHP 8.4 · Symfony 8 · Twig

## Règles implémentées

- Paquet de 104 cartes : 4 couleurs, chiffres de 0 à 9 et trois cartes spéciales, chacune en double
- On joue une carte de la même couleur ou de la même valeur que celle du dessus de la défausse
- **X** : le joueur suivant passe son tour
- **S** : le sens du jeu s'inverse
- **+2** : le joueur suivant pioche deux cartes, sauf s'il pose lui aussi un +2 (les pénalités se cumulent)
- La première carte retournée n'est jamais une carte spéciale

## Choix techniques

- **Logique séparée des contrôleurs** : les classes `Card`, `Player` et `GameState` (dans `src/Game/Model`) décrivent la partie ; les contrôleurs ne font qu'appliquer les coups.
- **Partie stockée en session** : `GameState` est converti en tableau (`toArray` / `fromArray`) à chaque requête, sans base de données.
- **Coups vérifiés par le serveur** : l'identifiant du joueur est validé avec `filter_var`, et un coup joué hors de son tour renvoie à la partie.
- **Adversaires automatiques** : `EnemyController` choisit une carte jouable pour chaque ordinateur.

## Lancer le projet

Prérequis : PHP 8.4, Composer, et la [CLI Symfony](https://symfony.com/download) (facultative).

```bash
git clone https://github.com/marylineb/UNO_maryline.git
cd UNO_maryline
composer install
symfony serve   # ou : php -S localhost:8000 -t public
```

Aucune base de données n'est nécessaire.

## À propos des fichiers `.env`

Les fichiers `.env`, `.env.dev` et `.env.test` sont versionnés volontairement : c'est la convention de Symfony. Ils ne contiennent que des valeurs par défaut pour le développement, aucun secret. Les vraies valeurs vont dans `.env.local` (ignoré par Git) en local, et dans les variables d'environnement de l'hébergeur en production.

## Auteure

Maryline Biloa · [Portfolio](https://marylinebiloa.com)
