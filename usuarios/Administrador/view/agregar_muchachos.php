<!-- Modal Bootstrap -->
<div class="modal fade" id="modalMuchacho" tabindex="-1" aria-labelledby="modalMuchachoLbl" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-light">

      <form method="post" class="needs-validation" novalidate>
        <!-- Identificador para procesar en PHP -->
        <input type="hidden" name="accion" value="agregar">

        <div class="modal-header">
          <h5 class="modal-title" id="modalMuchachoLbl" style="color:rgb(255, 255, 0);">Agregar Muchachos</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">

          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
            <div class="invalid-feedback">Ingresa un nombre.</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Apellido</label>
            <input type="text" name="apellido" class="form-control" required>
            <div class="invalid-feedback">Ingresa un apellido.</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Fecha de nacimiento</label>
            <input type="date" name="fecha_na" class="form-control" required>
            <div class="invalid-feedback">Selecciona la fecha de nacimiento.</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Departamento</label>
            <select name="departamento" class="form-select" required>
              <option value="">Elige…</option>
              <?php
                $departamentos = $conexion->query("SELECT ID_DEPARTAMENTO, NOMBRE_DEP FROM departamento");
                while ($dep = $departamentos->fetch_assoc()):
              ?>
                <option value="<?= $dep['ID_DEPARTAMENTO'] ?>">
                  <?= htmlspecialchars($dep['NOMBRE_DEP']) ?>
                </option>
              <?php endwhile; ?>
            </select>
            <div class="invalid-feedback">Selecciona un Departamento.</div>
          </div>

        </div><!-- /.modal-body -->

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>

    </div>
  </div>
</div>
