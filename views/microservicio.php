<?php
$pageTitle = 'API REST - Microservicio';
require_once '../includes/header.php';
?>

<div class="page-hero">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2><i class="fa fa-code me-2"></i>Microservicio REST - Proveedores API</h2>
      <p>Consumo del microservicio REST para gestión de proveedores vía API</p>
    </div>
    <span class="badge bg-success fs-6 px-3 py-2"><i class="fa fa-circle me-1"></i>API Activa</span>
  </div>
</div>

<div class="row g-4">
  <!-- INFO API -->
  <div class="col-lg-4">
    <div class="card h-auto">
      <div class="card-header bg-dark text-white"><i class="fa fa-book me-2"></i>Documentación API</div>
      <div class="card-body">
        <p class="small text-muted">Endpoint base:</p>
        <div class="api-request mb-3">api/proveedores_api.php</div>
        <p class="small fw-bold mb-2">Autenticación:</p>
        <div class="api-request mb-3">Authorization: COMPRAS-API-KEY-2024-ESPE</div>
        <p class="small fw-bold mb-2">Endpoints disponibles:</p>
        <table class="table table-sm small">
          <tr><td><span class="badge bg-success">GET</span></td><td>?id=X &rarr; obtener uno</td></tr>
          <tr><td><span class="badge bg-success">GET</span></td><td>→ listar todos</td></tr>
          <tr><td><span class="badge bg-success">GET</span></td><td>?q=term → buscar</td></tr>
          <tr><td><span class="badge bg-primary">POST</span></td><td>→ crear proveedor</td></tr>
          <tr><td><span class="badge bg-warning text-dark">PUT</span></td><td>?id=X → actualizar</td></tr>
          <tr><td><span class="badge bg-danger">DELETE</span></td><td>?id=X → eliminar</td></tr>
        </table>
        <p class="small fw-bold mb-2">Campos JSON (POST/PUT):</p>
        <div class="api-request small">
{<br>
&nbsp;"razon_social": "Empresa S.A.",<br>
&nbsp;"numero_identificacion": "1790000001",<br>
&nbsp;"telefono": "02-2345678",<br>
&nbsp;"correo": "info@empresa.com",<br>
&nbsp;"direccion": "Av. ..."<br>
}
        </div>
      </div>
    </div>
  </div>

  <!-- CRUD INTERACTIVO -->
  <div class="col-lg-8">
    <!-- LISTAR / BUSCAR -->
    <div class="card mb-3">
      <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <span><i class="fa fa-list me-2"></i>GET - Listar Proveedores (vía API)</span>
        <button class="btn btn-sm btn-light" onclick="apiGetAll()"><i class="fa fa-refresh me-1"></i>Cargar</button>
      </div>
      <div class="card-body">
        <div class="input-group mb-3">
          <input type="text" id="buscarQ" class="form-control" placeholder="Buscar por nombre o RUC...">
          <button class="btn btn-outline-success" onclick="apiSearch()"><i class="fa fa-search me-1"></i>Buscar</button>
        </div>
        <div class="api-response" id="responseGet">-- Presione "Cargar" para consultar la API --</div>
        <div id="tablaApi" class="mt-3"></div>
      </div>
    </div>

    <!-- CREAR -->
    <div class="card mb-3">
      <div class="card-header bg-primary text-white"><i class="fa fa-plus me-2"></i>POST - Crear Proveedor (vía API)</div>
      <div class="card-body">
        <div class="row g-2">
          <div class="col-md-6"><input type="text" id="c_razon" class="form-control" placeholder="Razón Social *"></div>
          <div class="col-md-6"><input type="text" id="c_numid" class="form-control" placeholder="RUC/NúmID *"></div>
          <div class="col-md-4"><input type="text" id="c_tel" class="form-control" placeholder="Teléfono"></div>
          <div class="col-md-4"><input type="email" id="c_correo" class="form-control" placeholder="Correo"></div>
          <div class="col-md-4"><input type="text" id="c_dir" class="form-control" placeholder="Dirección"></div>
          <div class="col-12">
            <button class="btn btn-primary" onclick="apiCreate()"><i class="fa fa-save me-1"></i>Crear via API</button>
          </div>
        </div>
        <div class="api-response mt-2" id="responsePost">--</div>
      </div>
    </div>

    <!-- ACTUALIZAR -->
    <div class="card mb-3">
      <div class="card-header bg-warning text-dark"><i class="fa fa-pen me-2"></i>PUT - Actualizar Proveedor (vía API)</div>
      <div class="card-body">
        <div class="row g-2">
          <div class="col-md-2"><input type="number" id="u_id" class="form-control" placeholder="ID *"></div>
          <div class="col-md-5"><input type="text" id="u_razon" class="form-control" placeholder="Razón Social *"></div>
          <div class="col-md-5"><input type="text" id="u_numid" class="form-control" placeholder="RUC/NúmID"></div>
          <div class="col-md-4"><input type="text" id="u_tel" class="form-control" placeholder="Teléfono"></div>
          <div class="col-md-4"><input type="email" id="u_correo" class="form-control" placeholder="Correo"></div>
          <div class="col-md-4"><input type="text" id="u_dir" class="form-control" placeholder="Dirección"></div>
          <div class="col-12">
            <button class="btn btn-warning" onclick="apiUpdate()"><i class="fa fa-save me-1"></i>Actualizar via API</button>
          </div>
        </div>
        <div class="api-response mt-2" id="responsePut">--</div>
      </div>
    </div>

    <!-- ELIMINAR -->
    <div class="card">
      <div class="card-header bg-danger text-white"><i class="fa fa-trash me-2"></i>DELETE - Eliminar Proveedor (vía API)</div>
      <div class="card-body">
        <div class="input-group">
          <input type="number" id="d_id" class="form-control" placeholder="ID del proveedor a eliminar">
          <button class="btn btn-danger" onclick="apiDelete()"><i class="fa fa-trash me-1"></i>Eliminar via API</button>
        </div>
        <div class="api-response mt-2" id="responseDelete">--</div>
      </div>
    </div>
  </div>
</div>

<script>
const API_URL = '../api/proveedores_api.php';
const API_KEY = 'COMPRAS-API-KEY-2024-ESPE';
const headers = { 'Content-Type': 'application/json', 'Authorization': API_KEY };

function showResponse(elId, data) {
    document.getElementById(elId).textContent = JSON.stringify(data, null, 2);
}

function renderTable(proveedores) {
    if (!proveedores || !proveedores.length) {
        document.getElementById('tablaApi').innerHTML = '<p class="text-muted small">Sin resultados</p>';
        return;
    }
    let rows = proveedores.map(p => `
        <tr>
            <td>${p.id_proveedor}</td>
            <td>${p.razon_social}</td>
            <td><code>${p.numero_identificacion}</code></td>
            <td>${p.telefono||'—'}</td>
            <td>${p.correo||'—'}</td>
            <td>
                <button class="btn btn-xs btn-outline-warning btn-sm" onclick="fillUpdate(${JSON.stringify(p).replace(/'/g,"\\'")})">
                    <i class="fa fa-pen"></i>
                </button>
                <button class="btn btn-xs btn-outline-danger btn-sm" onclick="fillDelete(${p.id_proveedor})">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>`).join('');
    document.getElementById('tablaApi').innerHTML = `
        <table class="table table-sm table-hover">
            <thead class="table-dark"><tr><th>#</th><th>Razón Social</th><th>RUC</th><th>Teléfono</th><th>Correo</th><th>Acc.</th></tr></thead>
            <tbody>${rows}</tbody>
        </table>`;
}

function fillUpdate(p) {
    document.getElementById('u_id').value = p.id_proveedor;
    document.getElementById('u_razon').value = p.razon_social;
    document.getElementById('u_numid').value = p.numero_identificacion;
    document.getElementById('u_tel').value = p.telefono || '';
    document.getElementById('u_correo').value = p.correo || '';
    document.getElementById('u_dir').value = p.direccion || '';
    document.querySelector('.card-header.bg-warning').scrollIntoView({behavior:'smooth'});
}

function fillDelete(id) {
    document.getElementById('d_id').value = id;
    document.querySelector('.card-header.bg-danger').scrollIntoView({behavior:'smooth'});
}

async function apiGetAll() {
    try {
        const res = await fetch(API_URL, { headers });
        const data = await res.json();
        showResponse('responseGet', data);
        renderTable(data.data);
    } catch(e) { document.getElementById('responseGet').textContent = 'Error: ' + e.message; }
}

async function apiSearch() {
    const q = document.getElementById('buscarQ').value;
    try {
        const res = await fetch(API_URL + '?q=' + encodeURIComponent(q), { headers });
        const data = await res.json();
        showResponse('responseGet', data);
        renderTable(data.data);
    } catch(e) { document.getElementById('responseGet').textContent = 'Error: ' + e.message; }
}

async function apiCreate() {
    const body = {
        razon_social: document.getElementById('c_razon').value,
        numero_identificacion: document.getElementById('c_numid').value,
        telefono: document.getElementById('c_tel').value,
        correo: document.getElementById('c_correo').value,
        direccion: document.getElementById('c_dir').value
    };
    try {
        const res = await fetch(API_URL, { method:'POST', headers, body: JSON.stringify(body) });
        const data = await res.json();
        showResponse('responsePost', data);
        if (data.status === 'success') { apiGetAll(); }
    } catch(e) { document.getElementById('responsePost').textContent = 'Error: ' + e.message; }
}

async function apiUpdate() {
    const id = document.getElementById('u_id').value;
    if (!id) { alert('Ingrese el ID a actualizar'); return; }
    const body = {
        razon_social: document.getElementById('u_razon').value,
        numero_identificacion: document.getElementById('u_numid').value,
        telefono: document.getElementById('u_tel').value,
        correo: document.getElementById('u_correo').value,
        direccion: document.getElementById('u_dir').value
    };
    try {
        const res = await fetch(API_URL + '?id=' + id, { method:'PUT', headers, body: JSON.stringify(body) });
        const data = await res.json();
        showResponse('responsePut', data);
        if (data.status === 'success') { apiGetAll(); }
    } catch(e) { document.getElementById('responsePut').textContent = 'Error: ' + e.message; }
}

async function apiDelete() {
    const id = document.getElementById('d_id').value;
    if (!id) { alert('Ingrese el ID a eliminar'); return; }
    if (!confirm('¿Eliminar proveedor ID ' + id + ' via API?')) return;
    try {
        const res = await fetch(API_URL + '?id=' + id, { method:'DELETE', headers });
        const data = await res.json();
        showResponse('responseDelete', data);
        if (data.status === 'success') { apiGetAll(); }
    } catch(e) { document.getElementById('responseDelete').textContent = 'Error: ' + e.message; }
}

// Auto-load on page open
window.addEventListener('DOMContentLoaded', apiGetAll);
</script>

<?php require_once '../includes/footer.php'; ?>
