# Plan de Refactorización y Mejora de Arquitectura API (Fase2)

Este plan detalla las acciones necesarias para evolucionar el sistema desde su estado actual (altamente acoplado en Controladores) hacia una arquitectura limpia, mantenible y escalable, utilizando buenas prácticas de Laravel y patrones de diseño modernos.

## User Review Required

> [!WARNING]
> **Cambio de Paradigma Estructural**
> Este plan propone una reestructuración significativa de cómo se organiza el código. Pasaremos de tener la lógica de negocio en los controladores a utilizar **Servicios** y **Eventos**. Esto requerirá mover mucho código existente, pero no cambiará el comportamiento funcional de la API. ¿Estás de acuerdo con avanzar con esta refactorización progresiva?

## Open Questions

> [!IMPORTANT]
> 1. ¿Deseas aplicar estos cambios primero en el dominio de **Ventas (`Sale`)** como prueba de concepto (PoC) antes de tocar **Compras (`Purchase`)**?
> 2. ¿Prefieres mantener la estructura actual de carpetas (`app/Models`, `app/Http/Controllers`) pero usando Servicios, o prefieres dar el salto a una estructura orientada por dominios (ej. `app/Domains/Sales/`)?

## Proposed Changes

La refactorización se dividirá en las siguientes fases estratégicas:

### 1. Implementación de Capa de Servicios (Service Layer)
Actualmente, métodos como `SaleController@add` tienen cientos de líneas de código manejando validación, inserción, cálculo de descuentos y control de inventario.

- Extraeremos la lógica de negocio a clases dedicadas (ej. `SaleService`).
- El Controlador solo se encargará de recibir el `Request`, enviarlo al `SaleService` y devolver un `JsonResponse`.

#### [NEW] [SaleService.php](file:///Users/ruravi/workspace/fase2spa/api.fase2/app/Services/SaleService.php)
#### [NEW] [InventoryService.php](file:///Users/ruravi/workspace/fase2spa/api.fase2/app/Services/InventoryService.php)
#### [MODIFY] [SaleController.php](file:///Users/ruravi/workspace/fase2spa/api.fase2/app/Http/Controllers/SaleController.php)

---

### 2. Arquitectura Orientada a Eventos (Desacoplamiento de Inventario)
En `SaleController` se altera el inventario (`PillInventory::decrement`, etc.) y se registra en el Log directamente en el flujo de cancelación o venta. Esto genera un acoplamiento fuerte.

- Crearemos Eventos (`SaleCreated`, `SaleCancelled`).
- Crearemos Listeners (`UpdateInventoryOnSale`, `LogSaleAction`) que escucharán estos eventos de manera síncrona o asíncrona.
- Esto limpiará el flujo principal y delegará responsabilidades.

#### [NEW] [SaleCreated.php](file:///Users/ruravi/workspace/fase2spa/api.fase2/app/Events/SaleCreated.php)
#### [NEW] [SaleCancelled.php](file:///Users/ruravi/workspace/fase2spa/api.fase2/app/Events/SaleCancelled.php)
#### [NEW] [UpdateInventoryOnSale.php](file:///Users/ruravi/workspace/fase2spa/api.fase2/app/Listeners/UpdateInventoryOnSale.php)

---

### 3. Query Objects o Repositorios para Consultas Complejas
Consultas masivas como `getCuteSales` o `getPaginate` ensucian el controlador.

- Moveremos estos constructores de consultas (Eloquent queries masivas) a clases que encapsulen la lógica de consulta (Ej. `GetCuteSalesQuery`).

#### [NEW] [GetCuteSalesQuery.php](file:///Users/ruravi/workspace/fase2spa/api.fase2/app/Queries/GetCuteSalesQuery.php)

---

### 4. Organización Orientada a Dominios (Opcional pero Recomendado)
Dado que Graphify detectó baja cohesión en la Comunidad 0 (FormRequests agrupados por tipo, no por funcionalidad), sugerimos mover archivos para que vivan cerca de donde se usan.

- Mover `SalePostRequest` y `SaleController` y `Sale` model a un módulo cohesivo.

## Verification Plan

Para garantizar que esta refactorización no rompa la lógica de negocio existente, seguiremos este plan:

### Automated Tests
- Antes de mover lógica, ejecutaremos la suite de pruebas actual (Pest/PHPUnit).
- Escribiremos pruebas de integración para los flujos críticos (`SaleController@add` y `SaleController@cancel`) para asegurar que el inventario siga cuadrando perfectamente.

### Manual Verification
- Utilizaremos Postman (o la UI del frontend Vue/React) para crear una venta completa con adicionales, paquetes y pastillas.
- Verificaremos manualmente en la base de datos que la transacción atómica sigue funcionando, el inventario se descuenta correctamente y los logs se generan.
