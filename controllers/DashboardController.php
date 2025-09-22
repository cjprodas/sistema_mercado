<?php
/**
 * Controlador del Dashboard
 */

class DashboardController extends BaseController
{

    public function index()
    {
        // Configurar datos para la vista
        $this->viewData['pageTitle'] = 'Dashboard';
        $this->viewData['viewFile'] = 'dashboard/index';

        // Obtener estadísticas básicas
        try {
            // Contar inquilinos activos
            $this->viewData['totalInquilinos'] = $this->db->count(
                "SELECT COUNT(*) FROM inquilinos WHERE estado = 'activo'"
            );

            // Contar usuarios activos
            $this->viewData['totalUsuarios'] = $this->db->count(
                "SELECT COUNT(*) FROM usuarios WHERE estado = 'activo'"
            );

            // Contar estados de cuenta pendientes (mes actual)
            $mesActual = date('Y-m');
            $this->viewData['estadosPendientes'] = $this->db->count(
                "SELECT COUNT(*) FROM estados_cuenta 
                 WHERE mes_año = ? AND estado IN ('pendiente', 'vencido')",
                [$mesActual]
            );

            // Calcular total de pagos del mes actual
            $this->viewData['pagosMesActual'] = $this->db->fetch(
                "SELECT COALESCE(SUM(monto), 0) as total 
                 FROM pagos 
                 WHERE YEAR(fecha_pago) = YEAR(CURRENT_DATE) 
                 AND MONTH(fecha_pago) = MONTH(CURRENT_DATE)"
            )['total'] ?? 0;

        } catch (Exception $e) {
            // Si hay error con la BD, usar valores por defecto
            $this->viewData['totalInquilinos'] = 0;
            $this->viewData['totalUsuarios'] = 1;
            $this->viewData['estadosPendientes'] = 0;
            $this->viewData['pagosMesActual'] = 0;

            // Log del error
            logMessage('WARNING', 'Error al obtener estadísticas del dashboard', [
                'error' => $e->getMessage(),
                'user_id' => $this->currentUser['id']
            ]);
        }

        // Registrar acceso al dashboard
        $this->logActivity('Acceso al dashboard');

        // Renderizar vista
        $this->view('dashboard/index');
    }

    /**
     * Obtener estadísticas para AJAX
     */
    public function getStats()
    {
        $this->requirePermission('dashboard', 'read');

        try {
            $stats = [
                'inquilinos_activos' => $this->db->count("SELECT COUNT(*) FROM inquilinos WHERE estado = 'activo'"),
                'usuarios_activos' => $this->db->count("SELECT COUNT(*) FROM usuarios WHERE estado = 'activo'"),
                'pagos_mes' => $this->db->fetch(
                    "SELECT COALESCE(SUM(monto), 0) as total 
                     FROM pagos 
                     WHERE YEAR(fecha_pago) = YEAR(CURRENT_DATE) 
                     AND MONTH(fecha_pago) = MONTH(CURRENT_DATE)"
                )['total'] ?? 0,
                'estados_pendientes' => $this->db->count(
                    "SELECT COUNT(*) FROM estados_cuenta 
                     WHERE mes_año = ? AND estado IN ('pendiente', 'vencido')",
                    [date('Y-m')]
                )
            ];

            $this->jsonResponse([
                'success' => true,
                'data' => $stats
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al obtener estadísticas'
            ], 500);
        }
    }

    /**
     * Obtener notificaciones para AJAX
     */
    public function getNotifications()
    {
        try {
            $notifications = $this->db->fetchAll(
                "SELECT * FROM notificaciones 
                 WHERE (usuario_id = ? OR usuario_id IS NULL) 
                 AND leida = FALSE 
                 ORDER BY fecha_envio DESC 
                 LIMIT 10",
                [$this->currentUser['id']]
            );

            $this->jsonResponse([
                'success' => true,
                'data' => $notifications,
                'count' => count($notifications)
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al obtener notificaciones'
            ], 500);
        }
    }

    /**
     * Marcar notificación como leída
     */
    public function markNotificationRead()
    {
        $this->requireMethod('POST');
        $this->validateCSRF();

        $notificationId = $_POST['notification_id'] ?? null;

        if (!$notificationId) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'ID de notificación requerido'
            ]);
        }

        try {
            $this->db->query(
                "UPDATE notificaciones 
                 SET leida = TRUE, fecha_leida = NOW() 
                 WHERE id = ? AND (usuario_id = ? OR usuario_id IS NULL)",
                [$notificationId, $this->currentUser['id']]
            );

            $this->jsonResponse([
                'success' => true,
                'message' => 'Notificación marcada como leída'
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al marcar notificación'
            ], 500);
        }
    }
}