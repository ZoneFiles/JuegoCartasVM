<?php
/**
 * Vista del panel CRUD.
 * Recibe: $jugadores (array), $editando (array|null), $mensaje (string|null)
 */
$f = $editando ?? ['id' => '', 'nombre' => '', 'ataque' => '', 'defensa' => '', 'control' => '',
    'carta_img' => '', 'equipo' => '', 'division' => 'primera', 'escudo_equipo' => 'img/logo.png'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <base href="<?= e(ASSETS_URL) ?>/">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar cartas · Batalla de Cartas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="container">
        <header>
            <h2><small>Batalla de Cartas</small>Administrar cartas</h2>
            <a href="<?= e(url()) ?>" class="btn-back">Volver al juego</a>
        </header>

        <?php if ($mensaje): ?>
            <div class="mensaje-flash"><?= e($mensaje) ?></div>
        <?php endif; ?>

        <div class="grid-layout">
            <!-- Formulario -->
            <div class="card-panel">
                <h3><?= $editando ? 'Editar jugador' : 'Nuevo jugador' ?></h3>
                <form method="POST" action="<?= e(url('admin/guardar')) ?>" id="form-jugador">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= e($f['id']) ?>">

                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" id="input-nombre" name="nombre" value="<?= e($f['nombre']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Equipo</label>
                        <input type="text" id="input-equipo" name="equipo" value="<?= e($f['equipo']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>División</label>
                        <select name="division" required>
                            <option value="primera" <?= $f['division'] === 'primera' ? 'selected' : '' ?>>Primera</option>
                            <option value="segunda" <?= $f['division'] === 'segunda' ? 'selected' : '' ?>>Segunda</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Ataque</label>
                        <input type="number" id="input-ataque" name="ataque" min="0" max="100" value="<?= e($f['ataque']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Defensa</label>
                        <input type="number" id="input-defensa" name="defensa" min="0" max="100" value="<?= e($f['defensa']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Control</label>
                        <input type="number" id="input-control" name="control" min="0" max="100" value="<?= e($f['control']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Imagen de la carta</label>
                        <input type="text" id="input-img" name="carta_img" value="<?= e($f['carta_img']) ?>" placeholder="img/cartas/jugador.png" required>
                    </div>

                    <div class="form-group">
                        <label>Escudo del equipo</label>
                        <input type="text" name="escudo_equipo" value="<?= e($f['escudo_equipo']) ?>" placeholder="img/logo.png">
                    </div>

                    <button type="submit" class="btn-submit"><?= $editando ? 'Actualizar' : 'Guardar' ?></button>
                    <?php if ($editando): ?>
                        <a href="<?= e(url('admin')) ?>" class="btn-cancel">Cancelar</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Tabla -->
            <div class="card-panel">
                <h3>Jugadores cargados</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Jugador</th>
                                <th>Equipo</th>
                                <th>Atq / Def / Ctr</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jugadores as $row): ?>
                            <tr>
                                <td><?= (int)$row['id'] ?></td>
                                <td>
                                    <div class="player-info">
                                        <img src="<?= e($row['carta_img']) ?>" class="player-img" alt="">
                                        <span><?= e($row['nombre']) ?></span>
                                    </div>
                                </td>
                                <td><?= e($row['equipo']) ?></td>
                                <td><strong><?= (int)$row['ataque'] ?> / <?= (int)$row['defensa'] ?> / <?= (int)$row['control'] ?></strong></td>
                                <td>
                                    <div class="actions">
                                        <a href="<?= e(url('admin?editar=' . (int)$row['id'])) ?>" class="btn-editar">Editar</a>
                                        <form class="form-eliminar" method="POST" action="<?= e(url('admin/eliminar')) ?>"
                                              onsubmit="return confirm('¿Borrar?');">
                                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                            <button type="submit" class="btn-eliminar">Borrar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Vista Previa Card -->
            <div class="card-panel" style="text-align: center;">
                <h3>Vista previa</h3>
                <div class="preview-card">
                    <img id="prev-img" src="img/logo.png" alt="Preview">
                    <h4 id="prev-nombre">Nombre</h4>
                    <div class="preview-stats" id="prev-stats">ATQ 0 · DEF 0 · CTR 0</div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
