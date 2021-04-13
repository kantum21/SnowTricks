# SnowTricks

Project 6 - OpenClassrooms

<a href="https://codeclimate.com/github/kantum21/SnowTricks/maintainability"><img src="https://api.codeclimate.com/v1/badges/2a3ccb4b3e4408b5c7df/maintainability" /></a>

SetUp Instructions

1. Get project : git clone https://github.com/kantum21/SnowTricks.git
2. Install dependencies : composer install
3. Configure environment : in .env file (BDD, SMTP...)
4. Create DataBase : php bin/console doctrine:database:create
5. Create Tables in DataBase : php bin/console doctrine:migrations:migrate
6. Load fixtures : php bin/console doctrine:fixtures:load
