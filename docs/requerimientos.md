# Requerimientos del Sistema

## Requerimientos Funcionales

### Gestión de Usuarios
- Registro de nuevos usuarios con rol 'usuario' o 'admin'
- Autenticación de usuarios
- Gestión de perfiles de usuario

### Gestión de Equipos
- Alta de nuevos equipos con los siguientes campos obligatorios:
  - Número de inventario (único)
  - Cantidad (entero ≥ 1)
  - Estado (siempre "Nuevo")
  - Fecha de alta
  - Costo (AR$ ≥ 0)
  - Destino
  - Expediente de compra
- Edición de equipos existentes
- Baja lógica de equipos
- Búsqueda y filtrado de equipos

### Generación de QR
- Generación de códigos QR únicos por inventario
- Reimpresión de códigos QR manteniendo el mismo contenido
- Validación de códigos QR mediante firma HMAC

### Reportes
- Listado de equipos con filtros
- Historial de movimientos
- Reporte de inventario actual

## Requerimientos No Funcionales

### Seguridad
- Autenticación mediante JWT
- Contraseñas almacenadas con hash bcrypt
- Validación de entrada en todos los formularios
- Control de acceso basado en roles

### Rendimiento
- Tiempo de respuesta máximo de 2 segundos por petición
- Soporte para al menos 100 usuarios concurrentes

### Usabilidad
- Interfaz intuitiva y responsiva
- Validación en tiempo real de formularios
- Mensajes de error claros y descriptivos

## Restricciones Técnicas
- Base de datos: PostgreSQL 16+
- Backend: Node.js con Prisma ORM
- Frontend: SvelteKit
- Autenticación: JWT
- Generación de QR: Librería QR Code
- Formato de fechas: ISO 8601 (YYYY-MM-DD)
- Codificación de caracteres: UTF-8