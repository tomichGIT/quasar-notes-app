# REST API de Notas para Quasar

CRUD en formato REST para las entidades Usuario, Nota y Categoría.
Las entidades y sus relaciones son las siguientes:
1. Usuario:
- Representa a un usuario en el sistema.
- Puede tener varias notas asociadas.
2. Nota:
- Representa una nota en el sistema.
- Pertenece a un único usuario.
- Puede tener varias categorías asociadas.
3. Categoría:
- Representa una categoría en el sistema.
- Puede tener varias notas asociadas.

Además, se requiere implementar un método adicional que permita mostrar las
notas pasadas, es decir, aquellas notas que hayan sido creadas hace más de 7 días.

## Setup BoilerPlate:

```bash
symfony new quasar-notes-app
cd .\quasar-notes-app\
composer require annotations
git init
composer require symfony/orm-pack --with-all-dependencies
composer require --dev symfony/maker-bundle
composer require logger
symfony check:security
```

Controllers:
```bash
php bin/console make:controller
```

Database:
Update .env file with database_url
Create entities including updatedAt, createdAt (id is auto)
```bash
php bin/console doctrine:database:create
php bin/console make:entity
php bin/console make:migration
```

Craeción de Relaciones ManyToMany, OneToMany, ManyToOne.
Una vez creadas las tablas por separadas, puedo volver a ejecutar `php bin/console make:entity` para editar una de las tablas.
En nuevo "property" seleccionar el nombre del item de la tabla a relacionar.
en FieldType usar "relation", luego elegir el tipo de relación y con que Clase se relaciona.

en cambios del schema ejecutar:
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## Instalación:
```bash
git clone https://github.com/quasar-notes-app
cd quasar-notes-app
composer install
```

Info and run the project
```bash
php bin/console about
symfony server:start
```

Acceder a `https://127.0.0.1:8000`

## API Endpoints:

Se pueden testear los endpoints con POSTMAN o si lo deseas ya tienes las rutas creadas en TEST_REQUESTS.rest (requierela Extensión de VisualStudioCode `REST Client`)

## Lista de Tareas:

- [x] cambio a DB sqlite
- [x] creación de Rutas de prueba con REST Client
- [x] Cambio de rutas a host/API/v1/endpoint
- [x] Listar Notas de un usuario específico
- [x] Obtener Notas mas antiguas a una semana
- [x] Mostrar antiguedad
- [ ] Si hay una categoría con el mismo nombre, no crear una nueva [a-zA-Záéíóú]
- [ ] JWT API token
- [ ] Auth