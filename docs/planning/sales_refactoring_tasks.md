# Tareas de Refactorización a Domain-Driven Design (Ventas)

- [x] 1. Crear estructura de carpetas para el dominio `Sales` en `app/Domains/Sales/`
  - `Controllers`, `Requests`, `Services`, `Events`, `Listeners`, `Queries`
- [x] 2. Mover o crear clases relacionadas al flujo principal
  - Mover `SaleController` al nuevo namespace `App\Domains\Sales\Controllers`
  - Mover `SalePostRequest` a `App\Domains\Sales\Requests`
- [x] 3. Refactorizar el controlador `SaleController`
  - Extraer lógica de `add` a `SaleService`
  - Extraer lógica de consultas complejas (`getCuteSales`, `getPaginate`) a `GetCuteSalesQuery` y `GetPaginatedSalesQuery`
- [x] 4. Implementar Eventos y Listeners para Inventario
  - Crear `SaleCreated` y `SaleCancelled`
  - Crear `UpdateInventoryOnSale` para manejar el descuento/incremento de `PillInventory` y `ProductInventory`
- [x] 5. Actualizar Rutas (`routes/api.php` o equivalente) para apuntar al nuevo Namespace del controlador de ventas.
- [x] 6. Validar que la API pueda cargar y no tenga errores de sintaxis o clases no encontradas. (Walkthrough generado, fin del plan).
