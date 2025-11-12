# Entity Documentation

## Core Entities

### User
- **Description**: System users with authentication credentials
- **Table**: `user`
- **Relations**:
  - One-to-Many: `Asset` (createdBy, updatedBy)
  - One-to-Many: `Document` (uploadedBy)
- **Fields**:
  - `id`: integer, primary key, auto-increment
  - `username`: string(50), unique, not null
  - `password`: string, not null
  - `email`: string(180), unique, not null
  - `is_active`: boolean, default: true
  - `last_login`: datetime, nullable
  - `created_at`: datetime, not null
  - `updated_at`: datetime, nullable
  - `deleted_at`: datetime, nullable (soft delete)
- **Indexes**:
  - `idx_user_username` (username)
  - `idx_user_email` (email)
- **Validation**:
  - Username: @Assert\NotBlank, @Assert\Length(min=3, max=50)
  - Email: @Assert\Email, @Assert\NotBlank, @Assert\Length(max=180)
  - Password: @Assert\NotBlank, @Assert\Length(min=8)

### Asset
- **Description**: Tracked physical assets in the heritage system
- **Table**: `asset`
- **Relations**:
  - Many-to-One: `User` (createdBy, updatedBy)
  - One-to-Many: `Document` (asset)
- **Fields**:
  - `id`: integer, primary key, auto-increment
  - `inventario`: string(50), unique, not null (código interno)
  - `cantidad`: integer, not null, default: 1
  - `detalle`: text, nullable, max: 500 characters
  - `estado`: enum('Nuevo'), not null, default: 'Nuevo'
  - `fecha_alta`: date, not null
  - `fecha_baja`: date, nullable
  - `acta_recepcion`: string(50), nullable (e.g., 'ACTA-123')
  - `orden_provision`: string(50), nullable (e.g., 'OP-456')
  - `costo`: decimal(12,2), not null
  - `destino`: string(100), not null
  - `expediente_compra`: string(50), not null (e.g., 'EXP-2025-000123')
  - `qr_code_uri`: string(255), nullable (e.g., 'inv://v1/asset?inv=INV-001&sig=abc123')
  - `created_by_id`: integer, foreign key to User, not null
  - `updated_by_id`: integer, foreign key to User, nullable
  - `created_at`: datetime, not null
  - `updated_at`: datetime, nullable
  - `deleted_at`: datetime, nullable (soft delete)
- **Indexes**:
  - `idx_asset_inventario` (inventario)
  - `idx_asset_estado` (estado)
  - `idx_asset_fechas` (fecha_alta, fecha_baja)
  - `fk_asset_created_by` (created_by_id)
  - `fk_asset_updated_by` (updated_by_id)
- **Validation**:
  - Inventario: 
    - @Assert\NotBlank
    - @Assert\Length(max=50)
    - @Assert\Regex(pattern="/^[A-Z0-9-]+$/")
    - Must be unique
  - Cantidad: 
    - @Assert\NotBlank
    - @Assert\Positive
  - Estado: 
    - @Assert\Choice({"Nuevo"})
  - Fecha Alta: 
    - @Assert\NotBlank
    - @Assert\Date
  - Fecha Baja: 
    - @Assert\GreaterThanOrEqual(propertyPath="fecha_alta")
  - Costo: 
    - @Assert\NotBlank
    - @Assert\PositiveOrZero
  - Destino: 
    - @Assert\NotBlank
    - @Assert\Length(max=100)
  - Expediente Compra: 
    - @Assert\NotBlank
    - @Assert\Length(max=50)

### Document
- **Description**: Supporting documents for assets
- **Table**: `document`
- **Relations**:
  - Many-to-One: `Asset` (asset), nullable
  - Many-to-One: `User` (uploadedBy), not null
- **Fields**:
  - `id`: integer, primary key, auto-increment
  - `tipo`: enum('acta_recepcion', 'orden_compra', 'factura', 'otro'), not null
  - `nombre_archivo`: string(255), not null
  - `ruta_archivo`: string(500), not null
  - `tamanio`: integer, not null (in bytes)
  - `mime_type`: string(100), not null
  - `hash_archivo`: string(64), not null (SHA-256)
  - `comentarios`: text, nullable
  - `uploaded_by_id`: integer, foreign key to User, not null
  - `created_at`: datetime, not null
  - `updated_at`: datetime, nullable
  - `deleted_at`: datetime, nullable (soft delete)
- **Indexes**:
  - `idx_document_tipo` (tipo)
  - `idx_document_asset` (asset_id)
  - `fk_document_uploaded_by` (uploaded_by_id)
- **Validation**:
  - Nombre Archivo: @Assert\NotBlank, @Assert\Length(max=255)
  - Ruta Archivo: @Assert\NotBlank, @Assert\Length(max=500)
  - Tamaño: @Assert\Positive
  - MIME Type: @Assert\NotBlank, @Assert\Length(max=100)
  - Hash Archivo: @Assert\NotBlank, @Assert\Length(exact=64)

### QRCodeLog
- **Description**: Audit log for QR code generation and access
- **Table**: `qrcode_log`
- **Relations**:
  - Many-to-One: `Asset` (asset), not null
  - Many-to-One: `User` (generatedBy), nullable
- **Fields**:
  - `id`: integer, primary key, auto-increment
  - `asset_id`: integer, foreign key to Asset, not null
  - `qr_code_uri`: string(255), not null
  - `firma_hmac`: string(64), not null
  - `ip_address`: string(45), nullable
  - `user_agent`: string(255), nullable
  - `generated_by_id`: integer, foreign key to User, nullable
  - `created_at`: datetime, not null
- **Indexes**:
  - `idx_qrcode_asset` (asset_id)
  - `idx_qrcode_created` (created_at)
  - `fk_qrcode_generated_by` (generated_by_id)
- **Validation**:
  - QR Code URI: @Assert\NotBlank, @Assert\Url
  - Firma HMAC: @Assert\NotBlank, @Assert\Length(exact=64)

### Location
- **Description**: Hierarchical physical or logical locations of assets
- **Table**: `location`
- **Relations**:
  - One-to-Many: `Asset` (assets)
  - Many-to-One: `Location` (parent), nullable
  - One-to-Many: `Location` (children)
  - Many-to-One: `User` (manager), nullable
- **Fields**:
  - `id`: integer, primary key, auto-increment
  - `name`: string(100), not null
  - `code`: string(50), unique, nullable (e.g., 'BLDG-A-01')
  - `description`: text, nullable
  - `address`: text, nullable
  - `contact_person`: string(100), nullable
  - `contact_phone`: string(20), nullable
  - `contact_email`: string(180), nullable
  - `parent_id`: integer, foreign key to Location, nullable
  - `manager_id`: integer, foreign key to User, nullable
  - `is_active`: boolean, default: true
  - `created_at`: datetime, not null
  - `updated_at`: datetime, nullable
  - `deleted_at`: datetime, nullable (soft delete)
- **Indexes**:
  - `idx_location_name` (name)
  - `idx_location_code` (code)
  - `fk_location_parent` (parent_id)
  - `fk_location_manager` (manager_id)
- **Validation**:
  - Name: @Assert\NotBlank, @Assert\Length(max=100)
  - Code: @Assert\Regex(pattern="/^[A-Z0-9\-]+$/")
  - Contact Email: @Assert\Email, @Assert\Length(max=180)

### Maintenance
- **Description**: Maintenance and service records for assets
- **Table**: `maintenance`
- **Relations**:
  - Many-to-One: `Asset` (asset), not null
  - Many-to-One: `User` (assignedTo), nullable
  - Many-to-One: `User` (createdBy), not null
  - One-to-Many: `MaintenanceTask` (tasks)
  - One-to-Many: `Document` (documents)
- **Fields**:
  - `id`: integer, primary key, auto-increment
  - `title`: string(255), not null
  - `description`: text, nullable
  - `type`: enum('preventive', 'corrective', 'inspection', 'upgrade'), not null
  - `priority`: enum('low', 'medium', 'high', 'critical'), default: 'medium'
  - `status`: enum('pending', 'in_progress', 'on_hold', 'completed', 'cancelled'), default: 'pending'
  - `scheduled_date`: datetime, nullable
  - `start_date`: datetime, nullable
  - `end_date`: datetime, nullable
  - `estimated_hours`: decimal(5,2), nullable
  - `actual_hours`: decimal(5,2), nullable
  - `cost`: decimal(10,2), nullable
  - `notes`: text, nullable
  - `created_at`: datetime, not null
  - `updated_at`: datetime, nullable
  - `deleted_at`: datetime, nullable (soft delete)
- **Indexes**:
  - `idx_maintenance_status` (status)
  - `idx_maintenance_type` (type)
  - `idx_maintenance_dates` (scheduled_date, start_date, end_date)
  - `fk_maintenance_asset` (asset_id)
  - `fk_maintenance_assigned` (assigned_to_id)
  - `fk_maintenance_creator` (created_by_id)
- **Validation**:
  - Title: @Assert\NotBlank, @Assert\Length(max=255)
  - Scheduled/Start/End Dates: @Assert\GreaterThan("yesterday")
  - Cost/Estimated Hours/Actual Hours: @Assert\PositiveOrZero

## Supporting Entities

### Role
- **Description**: User roles and permissions
- **Table**: `role`
- **Relations**:
  - Many-to-Many: `User` (users)
  - Many-to-Many: `Permission` (permissions)
- **Fields**:
  - `id`: integer, primary key, auto-increment
  - `name`: string(50), unique, not null (e.g., 'ROLE_ADMIN', 'ROLE_TECHNICIAN')
  - `description`: text, nullable
  - `is_system`: boolean, default: false
  - `created_at`: datetime, not null
  - `updated_at`: datetime, nullable
- **Indexes**:
  - `idx_role_name` (name)
- **Validation**:
  - Name: @Assert\NotBlank, @Assert\Length(max=50), @Assert\Regex(pattern="/^ROLE_[A-Z]+$/")
  - Description: @Assert\Length(max=500)

### Permission
- **Description**: Granular permissions for access control
- **Table**: `permission`
- **Relations**:
  - Many-to-Many: `Role` (roles)
- **Fields**:
  - `id`: integer, primary key, auto-increment
  - `name`: string(100), unique, not null (e.g., 'ASSET_CREATE', 'USER_EDIT')
  - `description`: text, nullable
  - `module`: string(50), not null (e.g., 'Asset', 'User', 'Report')
  - `created_at`: datetime, not null
- **Indexes**:
  - `idx_permission_name` (name)
  - `idx_permission_module` (module)
- **Validation**:
  - Name: @Assert\NotBlank, @Assert\Length(max=100), @Assert\Regex(pattern="/^[A-Z_]+$/")
  - Module: @Assert\NotBlank, @Assert\Length(max=50)

### AuditLog
- **Description**: System activity tracking
- **Table**: `audit_log`
- **Relations**:
  - Many-to-One: `User` (user), nullable
- **Fields**:
  - `id`: integer, primary key, auto-increment
  - `action`: string(50), not null (e.g., 'CREATE', 'UPDATE', 'DELETE', 'LOGIN')
  - `entity_type`: string(100), not null (e.g., 'App\\Entity\\User')
  - `entity_id`: string(50), nullable
  - `entity_label`: string(255), nullable
  - `changed_data`: json, nullable
  - `ip_address`: string(45), nullable
  - `user_agent`: string(255), nullable
  - `created_at`: datetime, not null
- **Indexes**:
  - `idx_audit_action` (action)
  - `idx_audit_entity` (entity_type, entity_id)
  - `idx_audit_created` (created_at)
  - `fk_audit_user` (user_id)
- **Validation**:
  - Action: @Assert\NotBlank, @Assert\Length(max=50)
  - Entity Type: @Assert\NotBlank, @Assert\Length(max=100)

## Additional Entities

### Document
- **Description**: File attachments for assets and maintenance records
- **Table**: `document`
- **Relations**:
  - Many-to-One: `Asset` (asset), nullable
  - Many-to-One: `Maintenance` (maintenance), nullable
  - Many-to-One: `User` (uploadedBy), not null
- **Fields**:
  - `id`: integer, primary key, auto-increment
  - `name`: string(255), not null
  - `original_name`: string(255), not null
  - `mime_type`: string(100), not null
  - `size`: integer, not null (in bytes)
  - `path`: string(500), not null
  - `description`: text, nullable
  - `document_type`: enum('manual', 'warranty', 'invoice', 'other'), default: 'other'
  - `is_public`: boolean, default: false
  - `uploaded_at`: datetime, not null
  - `updated_at`: datetime, nullable
- **Indexes**:
  - `idx_document_name` (name)
  - `idx_document_type` (document_type)
  - `fk_document_asset` (asset_id)
  - `fk_document_maintenance` (maintenance_id)
  - `fk_document_uploader` (uploaded_by_id)
- **Validation**:
  - Name: @Assert\NotBlank, @Assert\Length(max=255)
  - File: @Vich\UploadableField, @Assert\File(maxSize="10M")

### MaintenanceTask
- **Description**: Individual tasks within a maintenance record
- **Table**: `maintenance_task`
- **Relations**:
  - Many-to-One: `Maintenance` (maintenance), not null
  - Many-to-One: `User` (assignedTo), nullable
- **Fields**:
  - `id`: integer, primary key, auto-increment
  - `description`: text, not null
  - `status`: enum('pending', 'in_progress', 'completed', 'skipped'), default: 'pending'
  - `priority`: enum('low', 'medium', 'high'), default: 'medium'
  - `estimated_hours`: decimal(4,2), nullable
  - `actual_hours`: decimal(4,2), nullable
  - `completed_at`: datetime, nullable
  - `notes`: text, nullable
  - `sort_order`: integer, default: 0
  - `created_at`: datetime, not null
  - `updated_at`: datetime, nullable
- **Indexes**:
  - `idx_maintenance_task_status` (status)
  - `fk_maintenance_task_maintenance` (maintenance_id)
  - `fk_maintenance_task_assignee` (assigned_to_id)
- **Validation**:
  - Description: @Assert\NotBlank
  - Estimated/Actual Hours: @Assert\PositiveOrZero

### Settings
- **Description**: System configuration settings
- **Table**: `setting`
- **Fields**:
  - `id`: integer, primary key, auto-increment
  - `name`: string(100), unique, not null
  - `value`: text, nullable
  - `type`: enum('string', 'integer', 'float', 'boolean', 'array', 'json'), default: 'string'
  - `description`: text, nullable
  - `is_public`: boolean, default: false
  - `created_at`: datetime, not null
  - `updated_at`: datetime, nullable
- **Indexes**:
  - `idx_setting_name` (name)
  - `idx_setting_public` (is_public)
- **Validation**:
  - Name: @Assert\NotBlank, @Assert\Length(max=100), @Assert\Regex(pattern="/^[a-z][a-z0-9_.]+$/")
  - Value: @Assert\NotBlank

## Entity Relationships

```mermaid
erDiagram
    USER ||--o{ ASSET : assigned_to
    USER ||--o{ MAINTENANCE : created_by
    USER ||--o{ MAINTENANCE : assigned_to
    USER ||--o{ DOCUMENT : uploaded
    USER ||--o{ MAINTENANCE_TASK : assigned_to
    USER }|--o{ ROLE : has
    ROLE }|--|{ PERMISSION : has
    
    ASSET }|--|| CATEGORY : category
    ASSET }|--|| LOCATION : location
    ASSET ||--o{ MAINTENANCE : maintenance_records
    ASSET ||--o{ DOCUMENT : documents
    
    CATEGORY ||--o{ ASSET : assets
    CATEGORY }|--o| CATEGORY : parent
    
    LOCATION ||--o{ ASSET : assets
    LOCATION }|--o| LOCATION : parent
    
    MAINTENANCE ||--o{ MAINTENANCE_TASK : tasks
    MAINTENANCE ||--o{ DOCUMENT : documents
    
    DOCUMENT }|--|| USER : uploaded_by
    DOCUMENT }o--|| ASSET : asset
    DOCUMENT }o--|| MAINTENANCE : maintenance
```

## Common Enums

### AssetStatus
- `available`: Available for assignment
- `assigned`: Currently assigned to a user
- `maintenance`: Under maintenance
- `disposed`: No longer in use

### MaintenanceStatus
- `pending`: Scheduled but not started
- `in_progress`: Currently being worked on
- `completed`: Successfully finished
- `cancelled`: Cancelled before completion

## Data Validation Rules

### Asset
- `name`: Required, max 255 chars
- `assetTag`: Required, unique, alphanumeric with dashes
- `serialNumber`: Required if physical asset
- `purchaseCost`: Numeric, min 0, max 1,000,000
- `status`: Must be one of defined enum values

### User
- `email`: Required, valid email format
- `username`: Required, alphanumeric with underscores
- `password`: Min 8 chars, requires uppercase, lowercase, number, and special char