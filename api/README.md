# Sistema de Geoelec - API
Requerimientos del sistema:
	Se requiere un servidor apache.
	Se requiere usar composer para instalar las dependencias.
	Se requiere usar una versión de PHP superior a la 7.4

## Endpoints a consumir
Requerimientos para algunos endpoints.
	 Requieren un **Token** que se envia por **Header** con la **KEY** **Authorization** que es bearer

### Auth
**Iniciar sesión** - **POST **- **{{url}}/auth/login/**
- Se requieren los siguientes datos
```JSON 
{
  "usuario":"Usuario",
  "password":"Password",
  "token": "Token de HCAPTCHA"
}
```

----

**Registrar un usuario.**- **POST**-  **{{url}}/auth/register/**
- Requiere Token.
- Se requieren los siguientes datos.
```JSON
    {
    "usuario":"Admin",
    "password":"Password123*",
    "nombre":"Administrador",
    "id_perfil": 1,
    }
```

----

**Comprobar validacion del token. ** - **GET** - **{{url}}/auth/verify/**
- Requiere Token
- Devuelve los siguientes datos.
```JSON 
{
    "status": "ok",
    "message": {
        "usuario": "admin",
        "id_usuario": "21"
    }
}
```

---
**Actualizar Contraseña ** - **POST** - **{{url}}/auth/password/**
- Requiere Token
```JSON 
{
    "oldPassword": "Contraseña Antigua",
    "newPassword": "Contraseña Nueva"
}
```

---

### Categorias
**Registrar categoria **-  **POST** -  **{{url}}/categorias/register/**
- Requiere Token 
- Se requieren los siguientes datos:
```JSON 
{
    "nombre": "Nombre de una categoria"
}
```

----
**Obtener Categorias ** -  **GET** - **{{url}}/categorias/**
- Obtiene las categorias

----

**Actualizar categoria ** -  **PUT** - **{{url}}/categorias/**
- Requiere Token 
- Requiere el **id_categoria**
```JSON 
{
    "id_categoria": "1",
    "nombre": "Nombre de la categoria"
}
```

----

**Borrar categoria** - **DELETE** - **{{url}}/categorias/${id_categoria}/**
- Requiere Token
- Requieren un id_categoria en la ruta
- Requiere que no existan subcategorias con id_categoria

----

### Subcategorias
**Registrar subcategoria** - **POST** -  **{{url}}/subcategorias/register/**
- Requiere Token.
- Se requieren los siguientes datos:
```JSON 
{
	"id_categoria": 1,
    "nombre": "Nombre de la subcategoria",
	"descripcion": "Descripcion de la subcategoria"
}
```

----

**Obtener subcategorias** -  **GET** - **{{url}}/subcategorias/**
- Obtiene las subcategorias

----

**Actualizar subcategoria** -  **PUT** - **{{url}}/subcategorias/**
- Requiere Token
- Requiere el **id_subcategoria**
```JSON 
{
    "id_categoria": 1,
    "id_subcategoria": 1,
    "nombre": "Nombre de la subcategoria",
	"descripcion": "Descripcion de la subcategoria"
}
```

----
**Borrar subcategoria** - **DELETE** - **{{url}}/subcategorias/${id_subcategoria}/**
- Requiere Token
- Requiere un id_subcategoria en la ruta

**Ordenar Subcategoria** - **POST** - **{{url}}/subcategorias/order/**
- Requiere token
- Requiere un array con las subcategorias ya ordenadas.

```JSON 
{
	"data": [
		{
			"id_subcategoria" 1,
		}
		...
	]
}
```
----

### Productos
**Registrar producto** -  **POST** -  **{{url}}/productos/register/**
- Requiere Token 
- Requieren los siguientes datos:
```JSON 
{
    "id_subcategoria": 1,
    "marca": "Marca",
    "modelo": "Modelo",
    "descripcion": "Descripcion",
    "caracteristicas": "Caracteristicas..."
}
```

----

**Clonar producto** -  **POST** -  **{{url}}/productos/clone/**
- Requiere Token 
- Requieren los siguientes datos:
```JSON 
{
    "id_producto": 1,
}
```

----

**Obtener productos ** -  **GET** - **{{url}}/productos/**
- Obtiene los productos

----

**Obtener productos por ID** - **GET** - **{{url}}/productos/${id_producto}/**
- Obtiene un producto con el id_producto

----

**Obtener productos por id de subcategorias ** - **GET** - **{{url}}/productos/subcategoria/${id_subcategoria}/**
- Obtiene los productos con id_subcategoria

----

**Obtener productos por palabra o palabras buscando por marca, modelo o descripcion.** - **POST** - **{{url}}/productos/search/**
- Obtiene los productos con palabras que coincidan con la marca, modelo o descripcion.
- Requiere search
```JSON 
{
    "search": "Palabra a buscar",
}
```

----

**Actualizar categoria** - **PUT** - **{{url}}/productos/**
- Requiere Token 
- Requiere el **id_producto**
```JSON 
{
    "id_producto": "3",
    "id_categoria": 1,
    "marca": "Marca",
    "modelo": "Modelo",
    "descripcion": "Descripcion",
    "caracteristicas": "Caracteristicas",
}
```

----

**Borrar producto** - **DELETE** - **{{url}}/productos/${id_producto}/**
- Requiere Token
- Requiere un id_producto en la ruta

----

**Agregar producto relacionado** - **POST** - **{{url}}/productos/relacionados/**
- Requiere Token
- Requiere los siguientes datos:
```JSON 
{
    "id_producto": 1,
    "id_producto_relacionado": 2
}
```

----

**Borrar producto relacionado** - **DELETE** - **{{url}}/productos/relacionados/{{id_producto_relacionado}}/**
- Requiere Token
- Requiere un id_producto_relacionado en la ruta

----

### Imagenes
**Subir Imagen** - **POST** - **{{url}}/imagenes/subir/**
- Requiere Token
- Requiere los siguientes datos:
```JSON 
{
    "id_producto": 1,
    "imagen": FILE
}
```

----

**Actualizar Imagen** - **POST** - **{{url}}/imagenes/**
- Requiere Token
- Requiere los siguientes datos:
```JSON 
{
    "id_producto": 1,
    "id_imagen": 5,
    "imagen": FILE
}
```

----

**Obtener imagenes de un producto** - **GET** - **{{url}}/imagenes/${id_producto}/**
- Obtiene las imagenes

----

**Borrar imagen** - **DELETE** - **{{url}}/imagenes/${id_imagen}/**
- Requiere Token
- Requiere un id_imagen en la ruta

----

### Ficha Tecnica

**Agrega o actualiza Ficha Tecnica Español ** - **POST** - **{{url}}/productos/ficha_tecnica/es/**
- Requiere Token
- Requiere los siguientes datos:
```JSON 
{
    "id_producto": 1,
    "ficha_tecnica_es": FILE type pdf
}
```

----

**Agrega o actualiza Ficha Tecnica Ingles ** - **POST** - **{{url}}/productos/ficha_tecnica/in/**
- Requiere Token
- Requiere los siguientes datos:
```JSON 
{
    "id_producto": 1,
    "ficha_tecnica_in": FILE type pdf
}
```

---

**Elimina Ficha Tecnica Español ** - **DELETE** - **{{url}}/productos/ficha_tecnica/es/${ficha_tecnica_es}/**
- Requiere Token
- Requiere la ruta de la ficha tecnica ficha_tecnica_es

---

**Elimina Ficha Tecnica Ingles ** - **DELETE** - **{{url}}/productos/ficha_tecnica/in/${ficha_tecnica_in}/**
- Requiere Token
- Requiere la ruta de la ficha tecnica ficha_tecnica_in

---

### Envio de correo electronico de contacto

**Envio de email ** - **POST** - **{{url}}/email/send/**
- Requiere los siguientes datos:
```JSON 
{
	"nombre": "Nombre de la persona"
    "telefono": '4491231231',
    "email": "robertofriber@solutionsnamzug.com"
	"consulta": "Campo de consulta del email"
}
```

----

**Obtener Email Destinatario ** - **GET** - **{{url}}/email/get/**
- Requiere Token

---

**Actualzia Email Destinatario ** - **PUT** - ** {{url}}/email/update/ **
- Requiere Token
- Requiere los siguientes datos
```JSON 
{
    "email": "robertofriber@solutionsnamzug.com"
}
```

---

## Developers
### Variables de entorno
Se utilizan las siguientes variables de entorno que se deben configurar en el .env del api

***.env generales***
- **SECRET_KEY** = "Clave secreta para el Token"
- **RUTA_FRONTEND** = "Ruta para proteger peticiones mediante cors" 
- **HCAPTCHA_SECRET_KEY** = "KEY secreta servicio de HCAPTCHA"
- ** HCAPTCHA_SITE_KEY** = "KEY del sitio del servicio de HCAPTCHA"
---

***.env de la base de datos***

- **DB_USER** = "Usuario de la base de datos"
- **DB_PASSWORD** = "Contraseña de la base de datos"
- **DB_DATABASE** = "Nombre de la base de datos"
- **DB_HOST** = "Host de la base de datos"
- **DB_PORT** = "Puerto de la base de datos"

### Librerias de composer utilizadas

        "vlucas/phpdotenv": "5.3",
        "firebase/php-jwt": "5.4",
        "rakit/validation": "1.4",
        "dompdf/dompdf": "2.0"





Manejo de los estados.
* 1 = habilitado
* 0 = eliminado