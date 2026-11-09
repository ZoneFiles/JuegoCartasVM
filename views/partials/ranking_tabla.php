<?php /** Fragmento HTML del historial. Recibe $partidas (array). */ ?>
<?php if (!empty($partidas)): ?>
<table class="tabla-ranking">
    <thead>
        <tr><th>Ganador</th><th>Perdedor</th><th>Resultado</th><th>Fecha</th></tr>
    </thead>
    <tbody>
    <?php foreach ($partidas as $p): ?>
        <tr>
            <td><?= e($p['ganador']) ?></td>
            <td><?= e($p['perdedor']) ?></td>
            <td class="resultado"><?= (int)$p['puntaje_j1'] ?> - <?= (int)$p['puntaje_j2'] ?></td>
            <td class="fecha"><?= e(date('d/m/Y H:i', strtotime($p['fecha']))) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p class="sin-partidas">Todavía no hay partidas registradas en el historial.</p>
<?php endif; ?>
