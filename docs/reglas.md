# Reglas de Negocio

## 1. Gestión de Usuarios

### 1.1 Registro de Usuarios
- Solo los administradores pueden crear nuevas cuentas de usuario
- El correo electrónico debe ser único en el sistema
- La contraseña debe tener al menos 8 caracteres
- Los usuarios nuevos tienen rol 'usuario' por defecto

### 1.2 Autenticación
- Las sesiones expiran después de 24 horas de inactividad
- Después de 3 intentos fallidos, la cuenta se bloquea por 30 minutos
- Se requiere autenticación para acceder a cualquier funcionalidad del sistema

## 2. Gestión de Equipos

### 2.1 Creación de Equipos
- El número de inventario es obligatorio y único
- El estado por defecto es "Nuevo"
- La cantidad debe ser un número entero mayor o igual a 1
- El costo no puede ser negativo
- La fecha de alta no puede ser futura
- La fecha de baja (si existe) no puede ser anterior a la fecha de alta

### 2.2 Códigos QR
- Cada equipo tiene un código QR único
- El contenido del QR sigue el formato: `inv://v1/asset?inv=<NUMERO_INVENTARIO>&sig=<FIRMA>`
- La firma se genera usando HMAC-SHA256 con una clave secreta
- La reimpresión del QR genera el mismo código

### 2.3 Validaciones de Datos

#### Número de Inventario
- Requerido
- Único en el sistema
- Máximo 50 caracteres
- Se normaliza a mayúsculas
- Sin espacios al inicio o final

#### Cantidad
- Requerida
- Número entero
- Mínimo: 1
- Máximo: 999,999

#### Costo
- Requerido
- Número decimal positivo
- Mínimo: 0
- Máximo: 9,999,999.99
- 2 decimales

#### Fechas
- Formato: YYYY-MM-DD
- Fecha de alta: requerida, no puede ser futura
- Fecha de baja: opcional, debe ser posterior a la fecha de alta

## 3. Reglas de Acceso

### 3.1 Roles de Usuario
- **Administrador**: Acceso completo al sistema
- **Usuario**: Solo puede ver y gestionar sus propios registros

### 3.2 Permisos
- Solo los administradores pueden dar de baja equipos
- Cualquier usuario puede generar códigos QR
- Solo el propietario o un administrador puede editar un registro

## 4. Reglas de Negocio Adicionales

### 4.1 Auditoría
- Todos los cambios importantes se registran con:
  - Usuario que realizó el cambio
  - Fecha y hora
  - Tipo de operación
  - Datos modificados

### 4.2 Exportación de Datos
- Solo disponible para administradores
- Formato: Excel o PDF
- Incluye marca de agua con el usuario que generó el reporte

### 4.3 Rendimiento
- Las búsquedas deben responder en menos de 2 segundos
- La generación de códigos QR debe tomar menos de 1 segundo
- El sistema debe soportar al menos 100 usuarios concurrentes

## 5. Validaciones de Negocio

### 5.1 Validación de Código QR
1. Verificar que el número de inventario existe
2. Validar la firma HMAC
3. Verificar que el equipo no esté dado de baja
4. Registrar el escaneo para auditoría

### 5.2 Validación de Bajas
1. Verificar permisos de administrador
2. No permitir baja si el equipo tiene movimientos recientes
3. Registrar motivo de la baja
4. Enviar notificación al área correspondiente