# Envíos Express - Sistema de Gestión de Envíos

Aplicativo web profesional desarrollado en **PHP + MySQL** para gestionar envíos.

## Características

- ✅ Crear nuevos envíos (destinatario, dirección, descripción)
- ✅ Listar todos los envíos ordenados por fecha
- ✅ Editar envíos existentes
- ✅ Eliminar envíos
- ✅ Creación automática de la tabla `envios` si no existe
- ✅ Interfaz moderna y profesional (estilo plataforma de logística)
- ✅ Diseño responsive (móvil y escritorio)
- ✅ Validación de campos obligatorios
- ✅ Mensajes de confirmación y alertas

## Requisitos

- PHP 7.4 o superior (con extensión `mysqli`)
- Servidor web (Apache, Nginx, XAMPP, Laragon, etc.)
- Acceso a la base de datos MySQL configurada

## Credenciales de base de datos

Las credenciales ya están configuradas en `config.php`:

- **Host:** mysql-vinasco.alwaysdata.net
- **Usuario:** vinasco
- **Clave:** clase12345
- **Base de datos:** vinasco_repoenvios

## Instalación

1. Sube la carpeta `envios_app` a tu servidor web (o colócala en `htdocs` / `www`).
2. Asegúrate de que el servidor tenga acceso a internet (para conectar con la BD remota).
3. Abre en el navegador: `http://tu-servidor/envios_app/`

La tabla se crea automáticamente la primera vez que se accede a la aplicación.

## Estructura de archivos

```
envios_app/
├── config.php          # Conexión a BD + creación de tabla
├── index.php           # Listado + formulario de nuevo envío
├── edit.php            # Edición de envíos
├── assets/
│   └── style.css       # Estilos personalizados
└── README.md
```

## Tabla creada automáticamente

```sql
CREATE TABLE IF NOT EXISTS envios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destinatario VARCHAR(255) NOT NULL,
    direccion TEXT NOT NULL,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---
Desarrollado para uso educativo / demostración.
