<?php
require_once 'config.php';
$conn = getConnection();

// Mensajes de feedback
$mensaje = '';
$tipo_mensaje = '';

// Procesar eliminación
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $stmt = $conn->prepare("DELETE FROM envios WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $mensaje = 'Envío eliminado correctamente.';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al eliminar el envío.';
        $tipo_mensaje = 'danger';
    }
    $stmt->close();
}

// Procesar nuevo envío
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    $destinatario = trim($_POST['destinatario'] ?? '');
    $direccion    = trim($_POST['direccion'] ?? '');
    $descripcion  = trim($_POST['descripcion'] ?? '');

    if ($destinatario === '' || $direccion === '') {
        $mensaje = 'El destinatario y la dirección son obligatorios.';
        $tipo_mensaje = 'warning';
    } else {
        $stmt = $conn->prepare("INSERT INTO envios (destinatario, direccion, descripcion) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $destinatario, $direccion, $descripcion);
        if ($stmt->execute()) {
            $mensaje = 'Envío registrado correctamente.';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al registrar el envío.';
            $tipo_mensaje = 'danger';
        }
        $stmt->close();
    }
}

// Obtener todos los envíos
$result = $conn->query("SELECT * FROM envios ORDER BY fecha_creacion DESC");
$envios = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$total  = count($envios);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Envíos | Envíos Express</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
                <i class="bi bi-truck fs-3"></i>
                <span>Envíos Express</span>
            </a>
            <div class="d-flex align-items-center text-white-50 small">
                <i class="bi bi-geo-alt me-1"></i> Sistema de gestión de envíos
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <!-- Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">Panel de Envíos</h1>
                <p class="text-muted mb-0">Administra y da seguimiento a tus envíos de forma sencilla</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2">
                    <i class="bi bi-box-seam me-1"></i> <?php echo $total; ?> envío<?php echo $total !== 1 ? 's' : ''; ?>
                </span>
            </div>
        </div>

        <!-- Alertas -->
        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo e($tipo_mensaje); ?> alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : ($tipo_mensaje === 'warning' ? 'exclamation-triangle' : 'x-circle'); ?> me-2"></i>
                <?php echo e($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Formulario Nuevo Envío -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-plus-circle text-primary me-2"></i>Nuevo Envío
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="index.php" novalidate>
                            <input type="hidden" name="accion" value="crear">

                            <div class="mb-3">
                                <label for="destinatario" class="form-label fw-medium">Destinatario <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="destinatario" name="destinatario"
                                           placeholder="Nombre completo" required maxlength="255"
                                           value="<?php echo e($_POST['destinatario'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="direccion" class="form-label fw-medium">Dirección de entrega <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                    <textarea class="form-control" id="direccion" name="direccion" rows="3"
                                              placeholder="Calle, número, ciudad, código postal..." required><?php echo e($_POST['direccion'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="descripcion" class="form-label fw-medium">Descripción / Contenido</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-card-text"></i></span>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                              placeholder="Detalle del paquete, observaciones..."><?php echo e($_POST['descripcion'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="bi bi-send-check me-2"></i>Registrar Envío
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Listado de Envíos -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-list-ul text-primary me-2"></i>Listado de Envíos
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($envios)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-3 opacity-50"></i>
                                <p class="mb-0">No hay envíos registrados todavía.</p>
                                <small>Utiliza el formulario de la izquierda para crear el primero.</small>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4" style="width:70px">#</th>
                                            <th>Destinatario</th>
                                            <th>Dirección</th>
                                            <th>Descripción</th>
                                            <th class="text-nowrap">Fecha</th>
                                            <th class="text-end pe-4" style="width:120px">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($envios as $envio): ?>
                                            <tr>
                                                <td class="ps-4 fw-medium text-muted">#<?php echo (int)$envio['id']; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="avatar-circle">
                                                            <?php echo strtoupper(mb_substr($envio['destinatario'], 0, 1)); ?>
                                                        </div>
                                                        <span class="fw-medium"><?php echo e($envio['destinatario']); ?></span>
                                                    </div>
                                                </td>
                                                <td class="text-muted small" style="max-width:220px">
                                                    <?php echo e($envio['direccion']); ?>
                                                </td>
                                                <td class="text-muted small" style="max-width:180px">
                                                    <?php echo e($envio['descripcion'] ?: '—'); ?>
                                                </td>
                                                <td class="text-nowrap small text-muted">
                                                    <?php
                                                    $fecha = new DateTime($envio['fecha_creacion']);
                                                    echo $fecha->format('d/m/Y H:i');
                                                    ?>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="edit.php?id=<?php echo (int)$envio['id']; ?>"
                                                           class="btn btn-outline-primary" title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="index.php?eliminar=<?php echo (int)$envio['id']; ?>"
                                                           class="btn btn-outline-danger"
                                                           title="Eliminar"
                                                           onclick="return confirm('¿Estás seguro de eliminar este envío?');">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-top mt-5 py-4">
        <div class="container text-center text-muted small">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Envíos Express · Sistema de gestión de envíos</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
