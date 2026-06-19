# Plan de Implementación: Ventas Rápida de Paquetes en Agenda

De acuerdo con tu retroalimentación, implementaremos un **Modal Simplificado** directamente en la Agenda enfocado 100% en la venta de paquetes, con soporte nativo para **pagos parciales** (dejar pagos pendientes).

## Escenarios de Venta Cubiertos

El sistema debe manejar **tres escenarios** reales según cómo se originó la venta:

| Escenario | Descripción | `amount` enviado | `is_paid` resultante |
|-----------|-------------|-----------------|----------------------|
| **Pago completo en sucursal** | El cliente paga el total al momento. | = `price` | `1` (pagado) |
| **Pago parcial / anticipo** | El cliente deja un enganche y pagará el resto después. | < `price` | `0` (pendiente) |
| **Sin pago (venta por teléfono/WhatsApp)** | El cliente separó su lugar de forma remota y pagará al llegar. | `0` | `0` (pendiente) |

> [!IMPORTANT]
> **Venta sin pago inmediato (Teléfono / WhatsApp)**
> Cuando la venta se gestiona de forma remota, la recepcionista necesita poder **registrar el paquete y agendar la cita sin capturar ningún pago en ese momento**. El campo "Monto a Pagar Hoy" se dejará en **$0** y el método de pago debe ser **opcional** (no requerido) en este caso. El cliente liquida al llegar a su cita.

## Propuesta de Interfaz y Flujo

### 1. Registrar/Cobrar un Paquete desde la Agenda (Quick Sale)
Dentro del modal de la agenda (cuando seleccionas a un cliente), agregaremos un botón: **"📦 Vender Nuevo Paquete"**.

Al hacer clic, se abrirá un "sub-modal" simplificado con los siguientes campos clave:
- **Paquete a Vender:** (Selector del catálogo de paquetes). Al seleccionarlo, traerá el precio total oficial.
- **Responsable:** (Selector del empleado que realiza la venta/terapeuta).
- **Precio Total del Paquete:** (Ej. $1,500).
- **Monto a Pagar Hoy (Abono):** (Input numérico, **default: $0**). Tres casos posibles:
  - `$1,500` → Pago total, `is_paid = 1`.
  - `$500` → Anticipo, saldo pendiente de $1,000, `is_paid = 0`.
  - `$0` → Sin pago (venta remota por tel/WhatsApp), saldo total pendiente, `is_paid = 0`.
- **Método de Pago:** (Efectivo, Tarjeta, Transferencia, etc.). **Campo opcional si el abono es $0.**

**Acción Backend:** Al guardar, el Frontend enviará esta estructura al endpoint existente de Ventas (`SaleController@add`). El `SaleService` de Laravel ya está programado para calcular el `balance` (saldo) restando el `amount` (abono) del `total`, creando el paquete y dejándolo disponible para el cliente.
Al terminar el registro, el sub-modal se cierra y el paquete **aparecerá seleccionado automáticamente** en el modal de la agenda, listo para agendar la primera sesión.

### 2. De Ventas a Agenda (Agendar rápido)
Para el flujo inverso: Si un recepcionista está en el módulo principal de **Ventas**, cobra un paquete y le sale la alerta de "Venta Exitosa":
- Agregaremos un botón a la alerta que diga: **"🗓️ Agendar 1ra Sesión"**.
- Al presionarlo, el sistema saltará a la pantalla de Agenda (`/schedule?client_id=123&package_id=456`).
- La agenda detectará estos parámetros en la URL, abrirá automáticamente el modal de "Nueva Cita" con el cliente y el paquete ya seleccionados, ahorrando todos los clics manuales.

## Pasos de Ejecución (Tasks)

1. **Frontend (Agenda):** 
   - Crear el UI del sub-modal "Venta Rápida de Paquete".
   - Consumir los catálogos necesarios (`CatPackage`, `Users` para responsables, `TypeSale` para métodos de pago).
   - Hacer el campo **Método de Pago** opcional cuando `amount = 0` (venta sin cobro inmediato).
   - Crear la función que arme el JSON compatible con `SaleController@add` enviando el `price` total y el `amount` abonado (puede ser `0`).
2. **Frontend (Agenda Routing):**
   - Leer parámetros de URL (`client_id`, `package_id`) en el `ngOnInit` de la agenda para auto-abrir el modal de citas.
3. **Frontend (Ventas):**
   - Modificar la alerta de "Venta Exitosa" en `sales.component.ts` para detectar si se vendió un paquete y mostrar el botón de salto a la agenda.
4. **Backend (Validación):**
   - Confirmar que `SaleController@add` acepte `amount = 0` sin error de validación (actualmente puede requerir `amount > 0`). De ser necesario, ajustar la regla a `amount >= 0`.
   - Asegurarnos de que el endpoint `activeForClient` retorne paquetes con `is_paid = 0` para poder agendar aunque el cliente aún no haya pagado.

> [!TIP]
> **Abonos Posteriores (incluye venta por tel/WhatsApp)**
> Si el cliente dejó un saldo pendiente (parcial o total), los pagos posteriores se gestionarán desde el módulo de **Ventas → Cuentas por Cobrar** o desde el **módulo de Paquetes**. El modal rápido en la agenda es únicamente para la *creación inicial* de la venta y el agendamiento.

¿Te parece correcto este diseño considerando los tres escenarios? Si me das luz verde, comienzo con la ejecución del código.
