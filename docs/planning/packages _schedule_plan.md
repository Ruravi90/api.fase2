# Integración de Paquetes con Agenda

Actualmente los Paquetes (`Package` y `PackageTracking`) y la Agenda (`Schedule`) funcionan de forma separada. Cuando un cliente reserva una cita para una sesión de su paquete, el recepcionista tiene que anotarlo en la Agenda y, por separado, ir al módulo de Paquetes a registrar la sesión consumida.

Para integrar esto de forma fluida y automática, he preparado la siguiente propuesta arquitectónica y de experiencia de usuario.

## User Review Required

Antes de comenzar a programar, necesito tu validación sobre un proceso clave del negocio:

> [!IMPORTANT]
> **¿Cuándo se descuenta la sesión y el inventario?**
> Actualmente, al registrar una sesión de paquete, el sistema **descuenta inmediatamente** el inventario de los complementos (ampolletas, etc). 
> Al integrar esto a la Agenda, si un cliente llama hoy para agendar una sesión de su paquete para el *viernes*, tenemos dos opciones:
> **Opción A (Automático al Agendar):** Descontar la sesión y el inventario en el momento en que se guarda la cita en el calendario.
> **Opción B (Check-in manual):** La cita solo queda reservada en la agenda vinculada al paquete, pero no descuenta la sesión ni el inventario hasta que el día de la cita marquen un botón de "Sesión Tomada/Completada".
> *¿Cuál flujo prefieres que implementemos?*

> [!WARNING]
> **Datos Históricos:**
> Esta nueva funcionalidad aplicará para las citas y paquetes nuevos. El historial anterior (sesiones ya descontadas manualmente) se mantendrá intacto pero sin el vínculo fuerte de base de datos con la agenda.

## Proposed Changes

### 1. Base de Datos (Migraciones)
#### [NEW] `database/migrations/xxxx_add_package_tracking_to_schedule.php`
- Agregar la columna `package_id` (nullable) a la tabla `schedule` para saber de qué paquete viene la cita.
- Agregar la columna `schedule_id` (nullable) a la tabla `package_tracking` para saber qué cita generó el consumo de esta sesión.

### 2. Backend (API Laravel)
#### [NEW] Endpoint de Paquetes Activos
- Crear un endpoint `GET /clients/{id}/active-packages` que reciba el ID de un cliente y retorne únicamente los paquetes pagados que todavía tienen sesiones disponibles (es decir, `session_count > sesiones_consumidas`).

#### [MODIFY] `ScheduleController.php`
- **Al Agendar (`add` / `update`):** Si en el request viene un `package_id`, se crea automáticamente un registro interno usando la lógica de `PackageTracking`, lo cual descuenta la sesión (y el inventario dependiendo de tu decisión arriba).
- **Al Cancelar/Eliminar (`delete`):** Si se elimina una cita de la agenda que estaba ligada a un paquete, se debe hacer *rollback*: eliminar el `PackageTracking` asociado y regresar el inventario.

### 3. Frontend (Angular UI)
#### [MODIFY] Modal de Agenda (`schedule.component.html` / `ts`)
- Al seleccionar un "Cliente" en el modal de Nueva Cita, el sistema hará una petición en segundo plano para revisar si ese cliente tiene paquetes activos.
- Si tiene paquetes activos, aparecerá un nuevo selector desplegable (Dropdown) llamado **"Aplicar a Paquete (Opcional)"**.
- Si el usuario selecciona un paquete de esa lista, el campo de "Servicio / Título" se puede autocompletar (Ej. *Sesión de Masaje Relajante*).

#### [MODIFY] Vista del Calendario
- A las citas que estén vinculadas a un paquete se les agregará un icono visual (por ejemplo, un pequeño icono de un paquete o una cajita 📦) en el calendario para distinguirlas fácilmente de las citas de venta regular.
