/**
 * Alerta para eliminar un accesorio.
 */
function confirmarEliminar() {
    return window.confirm("¿Seguro que quiere eliminar este accesorio?");
}

/**
 * Alerta para marcar un pedido como completado (eliminarlo).
 */
function confirmarCompletar() {
    return window.confirm("¿Seguro que quieres marcar este pedido como 'Completado'?\n\nEsta acción es irreversible y eliminará el pedido de la lista.");
}

/**
 * Alerta para eliminar un administrador.
 */
function confirmarEliminarAdmin() {
    return window.confirm("¡ADVERTENCIA!\n\n¿Estás seguro de que quieres eliminar a este administrador?\n\nEsta acción es irreversible.");
}