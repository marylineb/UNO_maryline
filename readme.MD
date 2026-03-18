uno/
│
├── public/
│   ├── index.php                ← point d’entrée (route "/")
│   └── assets/
│       ├── css/
│       │   └── style.css
│       └── images/
│
├── src/
│
│   ├── Controller/
│   │   ├── HomeController.php       ← "/" (initialiser la partie)
│   │   ├── PlayController.php       ← "/play"
│   │   ├── PlayerController.php     ← "/player"
│   │   ├── EnemyController.php      ← "/ennemy"
│   │   └── EndController.php        ← "/end"
│
│   ├── Game/
│   │
│   │   ├── Model/
│   │       ├── Card.php             ← classe Card (OBLIGATOIRE)
│   │       ├── Player.php           ← classe Player (OBLIGATOIRE)
│   │       └── GameState.php        ← état global du jeu
│
├── templates/
│   ├── home/
│   │   └── index.html.twig         ← accueil
│   │
│   ├── game/
│   │   └── play.html.twig          ← jeu principal
│   │
│   └── end/
│       └── end.html.twig           ← victoire / défaite
│
├── config/
│   ├── routes.yaml
│   └── services.yaml
│
├── var/
├── vendor/
│
├── .env
├── composer.json
└── symfony.lock