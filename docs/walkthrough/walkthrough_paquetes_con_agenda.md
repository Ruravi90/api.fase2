# Walkthrough: Integración de Paquetes con Agenda

He completado la integración solicitada utilizando la **Opción B (Check-in manual)**. 

## ¿Qué ha cambiado?

### 1. Base de Datos Actualizada
Se han añadido las conexiones entre las citas de la agenda y el registro de sesiones de paquetes:
- Ahora cada cita (`schedule`) puede pertenecer a un paquete (`package_id`).
- Ahora cada registro de asistencia (`package_tracking`) sabe a qué cita corresponde (`schedule_id`).

### 2. Flujo en la Interfaz (Frontend)
- **Seleccionar Cliente:** Cuando creas o editas una cita y seleccionas a un cliente, el sistema busca automáticamente si tiene paquetes activos (paquetes pagados con sesiones disponibles).
- **Asignar Paquete:** Si el cliente tiene paquetes, aparecerá un nuevo menú desplegable debajo del cliente llamado **"Aplicar a Paquete (Opcional)"**.
- **Autocompletado:** Al seleccionar un paquete de ese menú, el título de la cita se autocompleta con el nombre del servicio (Ej. "Sesión Masaje Relajante").
- **Distintivo Visual:** Las citas guardadas que pertenezcan a un paquete mostrarán un ícono de un paquete (📦) en el calendario para que puedas distinguirlas fácilmente de las citas normales a simple vista.

### 3. Check-In Manual (Marcar Asistencia)
Como elegiste la opción manual, al crear la cita vinculada al paquete **no se descuenta la sesión ni el inventario** inmediatamente. 
En su lugar:
- Al llegar el día de la cita, puedes hacer clic en ella en el calendario.
- Verás un nuevo botón llamado **"Confirmar Asistencia"** (junto al botón de eliminar).
- Al hacer clic, el sistema te pedirá confirmación y, una vez aceptado:
  - Generará el descuento de la sesión en el historial del paquete.
  - Descontará el inventario correspondiente (ampolletas, productos).
  - Si era la última sesión del paquete, lo marcará como "Completado".
- Una vez confirmada la asistencia, el botón cambiará a un indicador verde con una palomita que dice "Asistencia confirmada" ✅.

> [!TIP]
> **Revertir Cita:** Si te equivocas y eliminas una cita vinculada a un paquete que **ya tenía la asistencia confirmada**, el sistema ahora es inteligente y eliminará también el registro de la sesión para revertir el consumo.

## Verificación

Para probar esto, realiza los siguientes pasos en la aplicación:
1. Asegúrate de tener un cliente con un paquete activo.
2. Ve a la Agenda, haz clic en un horario vacío y selecciona a dicho cliente.
3. Observa cómo aparece la opción para seleccionar el paquete. Selecciónalo.
4. Guarda la cita. Deberías ver el icono 📦 en el calendario.
5. Vuelve a hacer clic en esa cita y presiona el botón **Confirmar Asistencia**. Verás la alerta de éxito.
6. Si vas al módulo de Paquetes de ese cliente, verás que la sesión fue descontada correctamente.
