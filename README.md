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


## Instalación
```bash
git clone https://github.com/tomichGIT/quasar-notes-app
cd quasar-notes-app
composer install
```

## Correr el App
```bash
php bin/console about
symfony server:start
```

Acceder a `http://127.0.0.1:8000`

## Lista de Tareas

- [x] cambio a DB sqlite
- [x] creación de Rutas de prueba con REST Client
- [x] Cambio de rutas a server/API/v1/endpoint
- [x] Listar Notas de un usuario específico
- [x] Obtener Notas mas antiguas a una semana
- [x] Mostrar antiguedad en el response
- [x] SoftDelete de Usuarios (TestingController)
- [ ] Si hay una categoría con el mismo nombre, no crear una nueva RegEX[a-zA-Záéíóú]
- [ ] JWT API token
- [ ] Auth

## Stack

<div align="center">
<a href="Symfony">
    <img src= "https://img.shields.io/badge/React-20232A?style=for-the-badge&logo=react&logoColor=61DAFB"/>
</a>
<a href="PHP">
    <img src= "https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white"/>
</a>
 <a href="VSC">
    <img src= "https://img.shields.io/badge/Visual%20Studio%20Code-0078d7.svg?style=for-the-badge&logo=visual-studio-code&logoColor=white"/>
</a>
<a href="JWT">
    <img src= "https://img.shields.io/badge/JWT-black?style=for-the-badge&logo=JSON%20web%20tokens"/>
</a>
</div>

## API Endpoints:

Se pueden testear los endpoints con POSTMAN o si lo deseas ya tienes las rutas creadas en `TEST_REQUESTS.rest` (requiere la extensión de VSC `REST Client`)

prepend: http://localhost:8000/API/v1/ + URI

### Categorias

| Method     | URI                               | Action                                                  |
|------------|-----------------------------------|---------------------------------------------------------|
| `GET/HEAD` | `list_categorias`                 | `src\Controller\CategoriasController@listCategoria`     |
| `POST`     | `save_categorias`                 | `src\Controller\CategoriasController@saveCategoria`     |
| `PUT`      | `save_categorias/{idCategoria}`   | `src\Controller\CategoriasController@saveCategoria`     |
| `DELETE`   | `delete_categoria/{idCategoria}`  | `src\Controller\CategoriasController@deleteCategoria`   |

### Notas

| Method     | URI                               | Action                                                  |
|------------|-----------------------------------|---------------------------------------------------------|
| `GET/HEAD` | `lista_notas`                     | `src\Controller\NotasController@listNotas`              |
| `GET/HEAD` | `lista_notas/{idUsuario}`         | `src\Controller\NotasController@listNotasUsuario`       |
| `GET/HEAD` | `list_notas/{idUsuario}/antiguas` | `src\Controller\NotasController@listaNotasAntiguas`     |
| `POST`     | `save_nota/{idUsuario}/`          | `src\Controller\NotasController@saveNota`               |
| `PUT`      | `save_nota/{idUsuario}/{idNota?}` | `src\Controller\NotasController@saveNota`               |
| `DELETE`   | `delete_nota/{idNota}`            | `src\Controller\NotasController@deleteNota`             |

### Usuarios

| Method     | URI                               | Action                                                  |
|------------|-----------------------------------|---------------------------------------------------------|
| `GET/HEAD` | `list_usuarios`                   | `src\Controller\UsuariosController@listUsuarios`        |
| `POST`     | `save_usuario`                    | `src\Controller\UsuariosController@saveUsuario`         |
| `PUT`      | `save_usuario/{idUsuario}`        | `src\Controller\UsuariosController@saveUsuario`         |
| `DELETE`   | `delete_usuario/{idUsuario}`      | `src\Controller\UsuariosController@deleteUsuario`       |
