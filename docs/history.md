# Historia de Usuario — Patrimonio (Inventario y QR)

**Como** responsable de Patrimonio  
**quiero** registrar cada activo del edificio con los campos **exclusivos**: *Inventario (código interno), Cantidad, Detalle, Estado (siempre “Nuevo”), Fecha de alta, Fecha de baja, Acta de recepción, Orden de provisión, Costo (AR$), Destino, Expte de la compra* y **generar un QR único** por inventario,  
**para** identificarlo, rastrearlo y reimprimir su código cuando sea necesario.

---

## Alcance
- Crear activo y **generar su QR**.
- El QR es **único y estable** por inventario (reimpresión mantiene el mismo contenido).
- **Estado** permitido: únicamente **“Nuevo”**.
- **No** existen otros campos ni estados en la UI/APIs.

---

## Reglas de negocio y validaciones (solo estos campos)
- **Inventario (código interno)**: requerido; **único**; alfanumérico; normalizar a MAYÚSCULAS; sin espacios extremos.  
- **Cantidad**: requerida; entero **≥ 1**.  
- **Detalle**: opcional; texto hasta **500** caracteres.  
- **Estado**: valor fijo **“Nuevo”**.  
- **Fecha de alta**: requerida; formato ISO `YYYY-MM-DD`; preferentemente **no futura**.  
- **Fecha de baja**: opcional; si se informa, **≥ Fecha de alta**.  
- **Acta de recepción**: opcional; string corto (ej. `ACTA-123`).  
- **Orden de provisión**: opcional; string corto (ej. `OP-456`).  
- **Costo (AR$)**: requerido; número **≥ 0**; hasta 2 decimales.  
- **Destino**: requerido; texto corto (área/dependencia).  
- **Expte de la compra**: requerido; string (ej. `EXP-2025-000123`).  
- **QR único por inventario**: contenido **determinístico**: `inv://v1/asset?inv=<INVENTARIO>&sig=<FIRMA>`; **firma HMAC** generada en backend; reimpresión **no cambia** el QR.

> Si el inventario ya existe al crear: **rechazar** y ofrecer **reimprimir QR**.

---

## Criterios de aceptación (Gherkin)

### CA1 — Registrar activo válido
```
Dado que ingreso Inventario "INV-0001"
Y Cantidad "3"
Y Estado "Nuevo"
Y Fecha de alta "2025-10-09"
Y Costo "150000.00"
Y Destino "Mantenimiento"
Y Expte "EXP-2025-000123"
Cuando guardo el activo
Entonces el sistema confirma el alta
Y el activo queda disponible para generar o reimprimir su QR
```

### CA2 — Inventario requerido y único
```
Dado que intento guardar sin Inventario
Cuando confirmo
Entonces veo "Inventario es requerido"

Dado un activo existente con Inventario "INV-0001"
Cuando intento crear otro con el mismo Inventario
Entonces veo "El inventario ya existe"
```

### CA3 — Cantidad y costo válidos
```
Dado que ingreso Cantidad "0"
Cuando confirmo
Entonces veo "La cantidad debe ser un entero mayor o igual a 1"

Dado que ingreso Costo "-1"
Cuando confirmo
Entonces veo "El costo debe ser mayor o igual a 0"
```

### CA4 — Reglas de fechas
```
Dado Fecha de alta "2025-10-09"
Y Fecha de baja "2025-10-08"
Cuando confirmo
Entonces veo "La fecha de baja no puede ser anterior a la fecha de alta"
```

### CA5 — Estado fijo "Nuevo"
```
Dado que estoy en el formulario
Cuando despliego Estado
Entonces solo veo la opción "Nuevo" seleccionada
```

### CA6 — QR único y estable
```
Dado un activo guardado con Inventario "INV-0001"
Cuando presiono "Generar QR"
Entonces se genera un QR con URI que contiene inv=INV-0001 y una firma válida
Y si vuelvo a presionar "Generar QR" obtengo el mismo contenido (misma URI)
```

### CA7 — Reimpresión del QR
```
Dado un activo existente "INV-0001"
Cuando elijo "Ver/Imprimir QR"
Entonces veo el mismo QR previamente generado para ese inventario
```

---

## Flujo principal
1. Abrir “Nuevo activo”.
2. Completar **Inventario**, **Cantidad**, **Estado (Nuevo)**, **Detalle**, **Fecha de alta**, (opcional **Fecha de baja**), **Acta**, **OP**, **Costo**, **Destino**, **Expte**.
3. Guardar → validaciones → alta exitosa.
4. Presionar **Generar QR** → se obtiene **QR determinístico y firmado** (desde backend).  
5. **Ver/Reimprimir** QR cuando sea necesario.

---

## Definición de Hecho (DoD)
- Todos los criterios de aceptación cumplen.  
- Validaciones y mensajes de error claros (frontend y backend).  
- Persistencia exacta de los **10 campos**.  
- Generación de **QR determinístico** y **HMAC** basado en `Inventario`.  
- Reimpresión entrega el **mismo QR**.  
- Pruebas unitarias: unicidad de inventario, fechas, costo/cantidad.  
- No existen campos/estados adicionales en UI ni API.

---

## Casos borde
- **Normalización de Inventario**: convertir a MAYÚSCULAS y recortar espacios.  
- **Detalle con caracteres especiales**: escapar para visualización segura.  
- **Fecha de baja vacía**: permitida; no bloquea QR.  
- **Reintento de creación** con Inventario existente: ofrecer **reimpresión** del QR en vez de duplicar.

