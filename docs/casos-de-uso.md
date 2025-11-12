# Casos de Uso

## 1. Autenticación de Usuario
**Actor**: Usuario/Administrador  
**Descripción**: Permite al usuario autenticarse en el sistema.  
**Precondiciones**: Usuario registrado en el sistema  
**Flujo Principal**:
1. El usuario accede a la página de login
2. Ingresa email y contraseña
3. Symfony Security valida las credenciales contra la base de datos
4. Se genera un token JWT y se establece una sesión segura
5. Se redirige al dashboard correspondiente según el rol

**Flujos Alternativos**:
- Credenciales inválidas: Mostrar mensaje de error
- Cuenta deshabilitada: Notificar al administrador
- Contraseña expirada: Forzar cambio de contraseña

## 2. Gestión de Activos

### 2.1 Registrar Nuevo Activo
**Actor**: Usuario con permisos de escritura  
**Descripción**: Registrar un nuevo activo en el sistema.  
**Precondiciones**: Usuario autenticado con rol adecuado  
**Flujo Principal**:
1. El usuario accede al formulario de registro de activos
2. Completa los campos obligatorios:
   - Nombre del activo
   - Categoría
   - Ubicación
   - Estado
   - Información adicional
3. Opcionalmente, adjunta documentación o imágenes
4. El sistema valida los datos mediante Symfony Forms
5. Se genera automáticamente un código QR único
6. Se guarda el registro en la base de datos mediante Doctrine
7. Se muestra mensaje de confirmación

**Flujos Alternativos**:
- Datos inválidos: Mostrar errores de validación
- Archivo muy grande: Notificar límite de tamaño
- Error de conexión: Reintentar o guardar borrador

### 2.2 Buscar Activo
**Actor**: Usuario autenticado  
**Descripción**: Buscar activos por diferentes criterios  
**Flujo Principal**:
1. El usuario ingresa términos de búsqueda
2. El sistema realiza búsqueda con filtros avanzados
3. Muestra resultados paginados con KnpPaginator
4. Permite ordenar y filtrar resultados

## 3. Mantenimiento de Activos

### 3.1 Programar Mantenimiento
**Actor**: Técnico/Administrador  
**Descripción**: Programar mantenimiento preventivo  
**Flujo Principal**:
1. Seleccionar activo(s) a mantener
2. Especificar tipo de mantenimiento
3. Asignar técnico responsable
4. Establecer fecha/hora programada
5. Configurar recordatorios

### 3.2 Registrar Mantenimiento Correctivo
**Actor**: Técnico  
**Descripción**: Registrar mantenimiento realizado  
**Flujo Principal**:
1. Buscar activo
2. Registrar detalles de la falla
3. Documentar solución aplicada
4. Adjuntar evidencia (fotos, facturas)
5. Actualizar estado del activo

## 4. Reportes

### 4.1 Generar Reporte de Inventario
**Actor**: Usuario con permisos de reportes  
**Descripción**: Generar reporte PDF del inventario  
**Flujo Principal**:
1. Seleccionar criterios de filtrado
2. Configurar columnas a mostrar
3. Generar PDF con KnpSnappy
4. Opciones: Ver, descargar o enviar por email

### 4.2 Reporte de Mantenimientos
**Actor**: Administrador  
**Descripción**: Generar reporte de actividades de mantenimiento  
**Flujo Principal**:
1. Definir rango de fechas
2. Filtrar por tipo de mantenimiento
3. Agrupar por ubicación/técnico
4. Exportar a PDF/Excel

## 5. Administración del Sistema

### 5.1 Gestión de Usuarios
**Actor**: Administrador  
**Descripción**: Administrar usuarios y permisos  
**Funcionalidades**:
- Crear/editar/deshabilitar usuarios
- Asignar roles y permisos
- Restablecer contraseñas
- Ver historial de actividad

### 5.2 Configuración del Sistema
**Actor**: Administrador  
**Descripción**: Configurar parámetros del sistema  
**Ajustes comunes**:
- Parámetros de la empresa
- Plantillas de documentos
- Configuración de correo
- Integraciones con otros sistemas
4. El sistema guarda el equipo en la base de datos
5. El sistema muestra el código QR generado

## 3. Generar Código QR
**Actor**: Usuario/Administrador  
**Descripción**: Genera un código QR para un equipo existente.  
**Precondiciones**: Equipo existente en el sistema  
**Flujo Principal**:
1. El usuario selecciona un equipo
2. El sistema genera/recupera el código QR
3. El sistema muestra el código QR para imprimir

## 4. Consultar Inventario
**Actor**: Usuario/Administrador  
**Descripción**: Permite consultar el inventario de equipos.  
**Flujo Principal**:
1. El usuario accede a la sección de inventario
2. El sistema muestra la lista de equipos
3. El usuario puede filtrar y ordenar los resultados

## 5. Actualizar Datos de Equipo
**Actor**: Usuario/Administrador  
**Descripción**: Permite actualizar los datos de un equipo existente.  
**Precondiciones**: Equipo existente en el sistema  
**Flujo Principal**:
1. El usuario selecciona un equipo
2. El sistema muestra el formulario con los datos actuales
3. El usuario realiza las modificaciones necesarias
4. El sistema valida y guarda los cambios

## 6. Dar de Baja Equipo
**Actor**: Administrador  
**Descripción**: Permite dar de baja lógica a un equipo.  
**Precondiciones**: Usuario con rol de administrador  
**Flujo Principal**:
1. El administrador selecciona un equipo
2. El sistema solicita confirmación
3. El sistema actualiza el estado del equipo a "Dado de baja"
4. El sistema registra la fecha de baja