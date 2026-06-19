# Análisis: Flujo de Ventas + Paquetes + Agenda

Actualmente ya tenemos integrados los Paquetes con la Agenda (al agendar, puedes seleccionar un paquete existente). Ahora el desafío es hacer que la **venta (cobro) de un nuevo paquete** y la **creación de su primera cita** sucedan de forma fluida, sin importar en qué pantalla se encuentre el usuario.

A continuación presento cómo funcionaría la integración en los dos escenarios principales:

## Escenario 1: Vendiendo desde el Módulo de Ventas

**El problema actual:** Si generas la venta de un paquete en el módulo de Ventas, terminas el proceso y luego tienes que navegar manualmente al módulo de Agenda, buscar al cliente, buscar el paquete y agendar la cita.

**Cómo funcionaría integrado:**
1. El usuario realiza la venta del paquete de forma normal en la pantalla de Ventas.
2. Al terminar de cobrar y salir el mensaje de "Venta Exitosa", agregaremos un botón de acción rápida: **"🗓️ Agendar Primera Sesión"**.
3. Al hacer clic, el sistema redirigirá al módulo de Agenda, abriendo automáticamente el modal de "Nueva Cita".
4. El modal ya tendrá **pre-seleccionado al cliente** y **pre-seleccionado el nuevo paquete** que acaba de comprar.
5. El recepcionista solo tendrá que elegir la fecha/hora en el calendario y guardar.

## Escenario 2: Vendiendo desde la Agenda (Cobrar al momento)

**El problema actual:** Un cliente nuevo llama o llega para agendar una cita, y en ese momento decide comprar un paquete. El recepcionista está en la pantalla de Agenda, pero tiene que salirse, ir a Ventas, cobrar, y luego volver a la Agenda.

**Cómo funcionaría integrado:**
1. El recepcionista abre el modal de "Nueva Cita" en la Agenda y selecciona al cliente.
2. Al ver que no tiene paquetes activos (o si quiere comprar otro), habrá un botón directamente en el modal de la agenda que diga **"💰 Vender / Cobrar Paquete"**.
3. Al hacer clic, se abrirá una ventana emergente rápida (o se redirigirá a ventas) donde se podrá seleccionar el catálogo del paquete, el método de pago y procesar el cobro sin perder el contexto de la cita.
4. Al completarse el cobro, la ventana se cierra y el paquete recién comprado **aparece automáticamente seleccionado** en el modal de la cita original.
5. Se guarda la cita vinculada al nuevo paquete.

---

## User Review Required

> [!IMPORTANT]
> **Decisión sobre la complejidad de Ventas en la Agenda**
> Implementar el Escenario 1 (Ir de Ventas a Agenda) es muy directo y rápido.
> Sin embargo, para el **Escenario 2** (Cobrar desde la Agenda), el módulo de Ventas actual tiene mucha lógica (descuentos, seleccionar empleado responsable, propinas, forma de pago, etc.).
> 
> **Pregunta de Diseño:** 
> Para el Escenario 2, ¿prefieres que el botón en la Agenda abra un "Modal Simplificado" (solo para vender paquetes rápidos sin tantas opciones), o prefieres que el botón redirija a la pantalla completa de Ventas, y que al terminar la venta te devuelva a la Agenda para terminar de agendar?

Por favor revisa ambos escenarios y coméntame si la lógica de uso es la que esperabas, y qué opinas de la pregunta en la alerta amarilla.
