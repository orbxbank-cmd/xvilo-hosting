<?php
require __DIR__ . '/../core/Database.php';
require __DIR__ . '/../core/PterodactylAPI.php';
require __DIR__ . '/../core/Auth.php';
Auth::require();
$user = Auth::user();
$config = require __DIR__ . '/../config/app.php';

$id = (int)($_GET['id'] ?? 0);
$db = Database::getInstance();
$order = $db->fetch("SELECT * FROM xvilo_orders WHERE id = ? AND user_id = ?", [$id, $user['id']]);
if (!$order || !$order['server_id']) {
    header('Location: /dashboard.php');
    exit;
}

$ptero = new PterodactylAPI();
$srv = $ptero->getServer((int)$order['server_id']);
$srvAttr = $srv['attributes'] ?? null;
$uuid = $srvAttr['uuid'] ?? '';
$node = $srvAttr['node'] ?? 2;
$allocId = $srvAttr['allocation'] ?? 0;
$limits = $srvAttr['limits'] ?? [];
$container = $srvAttr['container'] ?? [];
$env = $container['environment'] ?? [];
$sampVer = $env['SAMP_VERSION'] ?? '0.3.7';

$vpsApiUrl = 'http://62.84.180.151/panel-api.php';
$apiSecret = 'xvil0pr0xy2024!';

$allocation = $ptero->request('GET', "/api/application/nodes/{$node}/allocations/{$allocId}");
$allocPort = $allocation['attributes']['port'] ?? 0;

require __DIR__ . '/../templates/header.php';
?>
<style>
.panel-layout { display:flex; gap:0; min-height:calc(100vh - 80px); margin-top:80px; background:#0a0a0a; }
.panel-side { width:220px; background:#111; border-right:1px solid #222; padding:20px 0; flex-shrink:0; }
.panel-side a { display:block; padding:12px 24px; color:#888; text-decoration:none; font-size:13px; transition:.2s; border-left:3px solid transparent; }
.panel-side a:hover, .panel-side a.active { color:#fff; background:rgba(255,0,0,.08); border-left-color:var(--accent); }
.panel-main { flex:1; padding:30px; overflow-y:auto; }
.server-bar { display:flex; align-items:center; gap:16px; padding:16px 20px; background:#111; border-radius:10px; margin-bottom:24px; border:1px solid #222; flex-wrap:wrap; }
.server-status { display:flex; align-items:center; gap:8px; font-size:13px; }
.status-dot { width:10px; height:10px; border-radius:50%; display:inline-block; }
.status-dot.running { background:#22c55e; box-shadow:0 0 8px #22c55e66; }
.status-dot.offline { background:#ef4444; box-shadow:0 0 8px #ef444466; }
.server-addr { font-family:monospace; font-size:13px; color:var(--text-muted); }
.server-uptime { font-size:12px; color:var(--text-muted); }
.power-btns { display:flex; gap:8px; margin-left:auto; }
.power-btn { padding:8px 16px; border-radius:6px; border:none; cursor:pointer; font-weight:600; font-size:12px; transition:.2s; }
.power-btn.start { background:#22c55e22; color:#22c55e; border:1px solid #22c55e44; }
.power-btn.start:hover { background:#22c55e44; }
.power-btn.stop { background:#ef444422; color:#ef4444; border:1px solid #ef444444; }
.power-btn.stop:hover { background:#ef444444; }
.power-btn.restart { background:#f59e0b22; color:#f59e0b; border:1px solid #f59e0b44; }
.power-btn.restart:hover { background:#f59e0b44; }
.power-btn:disabled { opacity:.4; cursor:not-allowed; }
.res-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; margin-bottom:24px; }
.res-card { background:#111; border:1px solid #222; border-radius:10px; padding:20px; }
.res-card .label { font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px; }
.res-card .value { font-size:22px; font-weight:700; color:#fff; }
.res-card .bar { height:4px; background:#222; border-radius:2px; margin-top:10px; overflow:hidden; }
.res-card .bar-fill { height:100%; border-radius:2px; transition:width 1s; }
.bar-fill.cpu { background:#3b82f6; }
.bar-fill.mem { background:#22c55e; }
.bar-fill.disk { background:#f59e0b; }
.bar-fill.net { background:#8b5cf6; }
.console-wrap { background:#0a0a0a; border:1px solid #222; border-radius:10px; overflow:hidden; }
.console-header { display:flex; justify-content:space-between; align-items:center; padding:10px 16px; background:#111; border-bottom:1px solid #222; font-size:12px; color:var(--text-muted); }
.console-body { height:350px; overflow-y:auto; padding:16px; font-family:monospace; font-size:12px; color:#ccc; line-height:1.6; white-space:pre-wrap; word-break:break-all; }
.console-body::-webkit-scrollbar { width:6px; }
.console-body::-webkit-scrollbar-track { background:transparent; }
.console-body::-webkit-scrollbar-thumb { background:#333; border-radius:3px; }
.console-input-wrap { display:flex; border-top:1px solid #222; }
.console-input-wrap input { flex:1; background:transparent; border:none; padding:12px 16px; color:#fff; font-family:monospace; font-size:13px; outline:none; }
.console-input-wrap input::placeholder { color:#555; }
.console-input-wrap button { padding:12px 20px; background:var(--accent); color:#fff; border:none; cursor:pointer; font-weight:600; font-size:12px; }
.console-input-wrap button:hover { opacity:.8; }
.panel-msg { padding:10px 16px; background:var(--accent); color:#fff; border-radius:6px; margin-bottom:16px; font-size:13px; }
.expire-bar { background:#111; border:1px solid #222; border-radius:10px; padding:16px 20px; margin-bottom:24px; display:flex; align-items:center; gap:16px; }
.expire-bar .label { font-size:12px; color:var(--text-muted); }
.expire-bar .countdown { font-family:monospace; font-size:18px; color:var(--accent); font-weight:700; }
@media(max-width:768px) {
  .panel-layout { flex-direction:column; }
  .panel-side { width:100%; border-right:none; border-bottom:1px solid #222; padding:10px 0; display:flex; flex-wrap:wrap; }
  .panel-side a { padding:10px 16px; border-left:none; border-bottom:2px solid transparent; }
  .panel-side a:hover, .panel-side a.active { border-bottom-color:var(--accent); }
  .panel-main { padding:16px; }
  .res-grid { grid-template-columns:1fr 1fr; }
  .power-btns { margin-left:0; width:100%; }
  .server-bar { flex-direction:column; align-items:flex-start; }
}
</style>

<?php if (isset($_GET['msg'])): ?>
  <div class="panel-msg"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<div class="panel-layout">
  <div class="panel-side">
    <a href="/server.php?id=<?= $id ?>" class="active">Console</a>
    <a href="#" onclick="return false;">File Manager</a>
    <a href="#" onclick="return false;">Databases</a>
    <a href="#" onclick="return false;">Schedules</a>
    <a href="#" onclick="return false;">Backups</a>
    <a href="#" onclick="return false;">Network</a>
    <a href="#" onclick="return false;">Startup</a>
    <a href="#" onclick="return false;">Settings</a>
  </div>

  <div class="panel-main">
    <div class="server-bar" id="serverBar">
      <div class="server-status">
        <span class="status-dot offline" id="statusDot"></span>
        <span id="statusText" style="font-weight:600;">Loading...</span>
      </div>
      <div class="server-addr" id="serverAddr">
        <?= htmlspecialchars($order['server_name']) ?> — <?= $config['db']['host'] ?>:<?= $allocPort ?: '?' ?>
      </div>
      <div class="server-uptime" id="uptimeText"></div>
      <div class="power-btns">
        <button class="power-btn start" onclick="power('start')" id="btnStart">Start</button>
        <button class="power-btn restart" onclick="power('restart')" id="btnRestart">Restart</button>
        <button class="power-btn stop" onclick="power('stop')" id="btnStop">Stop</button>
      </div>
    </div>

    <?php if ($order['expires_at']): ?>
    <div class="expire-bar">
      <span class="label">Expire dans</span>
      <span class="countdown" id="countdownTimer" data-expires="<?= strtotime($order['expires_at']) ?>"></span>
    </div>
    <?php endif; ?>

    <div class="res-grid" id="resGrid">
      <div class="res-card"><div class="label">CPU</div><div class="value" id="cpuVal">-</div><div class="bar"><div class="bar-fill cpu" id="cpuBar" style="width:0%"></div></div></div>
      <div class="res-card"><div class="label">RAM</div><div class="value" id="memVal">-</div><div class="bar"><div class="bar-fill mem" id="memBar" style="width:0%"></div></div></div>
      <div class="res-card"><div class="label">Disk</div><div class="value" id="diskVal">-</div><div class="bar"><div class="bar-fill disk" id="diskBar" style="width:0%"></div></div></div>
      <div class="res-card"><div class="label">Network</div><div class="value" id="netVal">-</div><div class="bar"><div class="bar-fill net" id="netBar" style="width:0%"></div></div></div>
    </div>

    <div class="console-wrap">
      <div class="console-header">
        <span>Console Output</span>
        <a href="#" onclick="clearConsole();return false;" style="color:var(--text-muted);font-size:11px;">Clear</a>
      </div>
      <div class="console-body" id="consoleBody">Connecting...</div>
      <div class="console-input-wrap">
        <input type="text" id="cmdInput" placeholder="Type a command..." onkeydown="if(event.key==='Enter')sendCmd()">
        <button onclick="sendCmd()">Send</button>
      </div>
    </div>
  </div>
</div>

<script>
const VPS_URL = '<?= $vpsApiUrl ?>';
const KEY = '<?= $apiSecret ?>';
const SERVER_ID = <?= (int)$order['server_id'] ?>;
let statusInterval, consoleInterval;

async function api(action, extra = '') {
  const url = VPS_URL + '?action=' + action + '&key=' + KEY + '&server_id=' + SERVER_ID + extra;
  try {
    const r = await fetch(url);
    return await r.json();
  } catch(e) { return null; }
}

function updateStats(data) {
  const running = data.status === 'running';
  document.getElementById('statusDot').className = 'status-dot ' + (running ? 'running' : 'offline');
  document.getElementById('statusText').textContent = data.status || 'offline';

  document.getElementById('btnStart').disabled = running;
  document.getElementById('btnStop').disabled = !running;
  document.getElementById('btnRestart').disabled = !running;

  if (data.startedAt) {
    const t = new Date(data.startedAt);
    const diff = Math.floor((Date.now() - t) / 1000);
    const h = Math.floor(diff / 3600);
    const m = Math.floor((diff % 3600) / 60);
    const s = diff % 60;
    document.getElementById('uptimeText').textContent = 'Uptime: ' + h + 'h ' + m + 'm ' + s + 's';
  }

  if (data.cpu) {
    const cpu = parseFloat(data.cpu) || 0;
    document.getElementById('cpuVal').textContent = data.cpu;
    document.getElementById('cpuBar').style.width = Math.min(cpu, 100) + '%';
  }
  if (data.mem) {
    document.getElementById('memVal').textContent = data.mem;
    const parts = data.mem.split('/');
    if (parts.length === 2) {
      const used = parseFloat(parts[0]) || 0;
      const total = parseFloat(parts[1]) || 1;
      document.getElementById('memBar').style.width = Math.min((used/total)*100, 100) + '%';
    }
  }
  if (data.memPerc) {
    document.getElementById('memVal').textContent = data.mem + ' (' + data.memPerc + ')';
  }
  if (data.netIO) {
    document.getElementById('netVal').textContent = data.netIO;
  }

  const memLimit = <?= ($limits['memory'] ?? 1024) ?>;
  const diskLimit = <?= ($limits['disk'] ?? 51200) ?>;
  document.getElementById('diskVal').textContent = '0 / ' + (diskLimit / 1024).toFixed(1) + ' GB';
}

async function refreshStatus() {
  const data = await api('status');
  if (data) updateStats(data);
}

async function power(action) {
  const btn = document.getElementById('btn' + action.charAt(0).toUpperCase() + action.slice(1));
  btn.disabled = true;
  btn.textContent = action === 'start' ? 'Starting...' : action === 'stop' ? 'Stopping...' : 'Restarting...';
  const data = await api(action);
  if (data && data.result) {
    setTimeout(refreshStatus, 2000);
  }
  setTimeout(() => { btn.textContent = action.charAt(0).toUpperCase() + action.slice(1); }, 3000);
}

async function refreshConsole() {
  const data = await api('console', '&lines=100');
  if (data && data.output !== undefined) {
    document.getElementById('consoleBody').textContent = data.output || 'No output';
  }
}

async function sendCmd() {
  const input = document.getElementById('cmdInput');
  const cmd = input.value.trim();
  if (!cmd) return;
  input.value = '';
  const data = await api('command', '&cmd=' + encodeURIComponent(cmd));
  setTimeout(refreshConsole, 500);
}

function clearConsole() {
  document.getElementById('consoleBody').textContent = 'Console cleared.';
}

// Countdown timer
function updateCountdown() {
  const el = document.getElementById('countdownTimer');
  if (!el) return;
  const expires = parseInt(el.dataset.expires) * 1000;
  const diff = expires - Date.now();
  if (diff <= 0) {
    el.textContent = 'Expiré';
    el.style.color = '#ef4444';
    return;
  }
  const d = Math.floor(diff / 86400000);
  const h = Math.floor((diff % 86400000) / 3600000);
  const m = Math.floor((diff % 3600000) / 60000);
  const s = Math.floor((diff % 60000) / 1000);
  el.textContent = d + 'd ' + h + 'h ' + m + 'm ' + s + 's';
}

refreshStatus();
refreshConsole();
statusInterval = setInterval(refreshStatus, 5000);
consoleInterval = setInterval(refreshConsole, 10000);
updateCountdown();
setInterval(updateCountdown, 1000);
</script>

<?php require __DIR__ . '/../templates/footer.php'; ?>
