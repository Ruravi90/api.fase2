# Walkthrough: Refactorización a Domain-Driven Design (Ventas)

A continuación se detalla el progreso y los resultados de la implementación de una arquitectura limpia en el módulo de Ventas de la API. Este trabajo partió de un análisis inicial usando Graphify, el cual reveló a `SaleController` como un "God Node" fuertemente acoplado.

## ¿Qué se logró?

Se transformó la lógica monolítica de Ventas en una arquitectura orientada a dominios (DDD) altamente escalable, utilizando Servicios, Eventos y Query Objects de Laravel.

### 1. Migración a Domain-Driven Design (DDD)
Se abandonó parcialmente la estructura genérica (`app/Http/Controllers`, `app/Http/Requests`) en favor de una estructura que agrupa por dominio de negocio:

- **Se creó la estructura:** `app/Domains/Sales/`
- **Componentes movidos:** `SaleController` y `SalePostRequest` ahora viven dentro de este dominio con sus respectivos *namespaces*.
- **Rutas actualizadas:** `routes/api.php` fue ajustado para resolver correctamente el nuevo namespace.

### 2. Implementación de Capa de Servicios (Service Layer)
El método `SaleController@add` tenía cientos de líneas manejando creación, validación, pagos y descuentos.
- Se creó `App\Domains\Sales\Services\SaleService`.
- Toda la lógica de creación transaccional se encapsuló en el método `createSale()`. El controlador ahora solo inyecta el servicio y devuelve una respuesta HTTP limpia.

### 3. Arquitectura Orientada a Eventos (Desacoplamiento de Inventario)
Anteriormente, el flujo de ventas estaba rígidamente atado al inventario (`PillInventory`, `ProductInventory`).
- **Evento:** Se creó `SaleCreated`.
- **Listener:** Se creó `UpdateInventoryOnSale`.
- **Resultado:** Cuando `SaleService` termina de registrar la venta en la base de datos, dispara el evento. El *Listener* lo atrapa de forma independiente y se encarga de reducir el stock. Esto facilita que a futuro otros módulos (ej. contabilidad, notificaciones) puedan escuchar a la venta sin ensuciar la lógica principal.

### 4. Query Objects
Las consultas masivas de Eloquent para reportes y paginación ensuciaban la legibilidad del código.
- Se crearon `GetPaginatedSalesQuery` y `GetCuteSalesQuery` dentro de `app/Domains/Sales/Queries/`.
- Estas clases encapsulan consultas extremadamente largas que ahora el controlador simplemente "ejecuta" en 1 o 2 líneas.

---

## Resultados y Validación

> [!TIP]
> **Impacto en el Código:** El controlador principal de ventas pasó de ser un monstruo inmanejable de más de 600 líneas a un orquestador HTTP liviano, delegando la carga a Servicios y Consultas. 

### Siguientes pasos (Para el Usuario)
- Validar las transacciones y recortes en el frontend (SPA) para asegurar que el inventario siga cuadrando exactamente igual que antes.
- Realizar este mismo proceso de refactorización (PoC) sobre el dominio de **Compras (`Purchase`)**, el cual es el segundo "God Node" detectado.
