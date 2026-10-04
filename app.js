/* ═══════════════════════════════════════
   Weekly Planner — app.js
   ═══════════════════════════════════════ */

/* ─────────────────────────────────────
   UTILS
───────────────────────────────────── */

function $(id) {
  return document.getElementById(id);
}

function save(key, value) {
  localStorage.setItem(key, JSON.stringify(value));
}

function load(key, defaultValue) {
  try {
    const item = localStorage.getItem(key);
    return item ? JSON.parse(item) : defaultValue;
  } catch (e) {
    return defaultValue;
  }
}

/* ─────────────────────────────────────
   DATE HEADER
───────────────────────────────────── */

function initDateHeader() {
  const now = new Date();

  const opts = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  $('dateLabel').textContent = now.toLocaleDateString('id-ID', opts);

  const dayOfWeek = now.getDay();
  const diffToMon = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
  const monday = new Date(now);
  monday.setDate(now.getDate() + diffToMon);
  const sunday = new Date(monday);
  sunday.setDate(monday.getDate() + 6);

  const fmt = (d) => d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
  $('weekBadge').textContent = fmt(monday) + ' – ' + fmt(sunday);
}

initDateHeader();

/* ─────────────────────────────────────
   QUOTE HARIAN
───────────────────────────────────── */

const DEFAULT_QUOTE = '"The secret of getting ahead is getting started." — Mark Twain';

const quoteEl = $('quoteInput');
quoteEl.value = load('wp_quote', DEFAULT_QUOTE);
quoteEl.addEventListener('input', () => save('wp_quote', quoteEl.value));

/* ─────────────────────────────────────
   WEEKLY TABLE (MySQL via API)
───────────────────────────────────── */

const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
let weeklyRows = [];

function generateTimeOptions(selectedTime) {
  let options = '';
  for (let hour = 5; hour <= 22; hour++) {
    const time = `${hour.toString().padStart(2, '0')}:00`;
    options += `<option value="${time}"${time === selectedTime ? ' selected' : ''}>${time}</option>`;
  }
  return options;
}

function rowClass(status) {
  if (status === 'done')     return 'row-done';
  if (status === 'progress') return 'row-prog';
  return '';
}

async function fetchRows() {
  const res = await fetch('/weeklyplanner/api/weekly.php');
  weeklyRows = await res.json();
  renderRows();
}

function renderRows() {
  const tbody = $('weekBody');
  tbody.innerHTML = '';

  weeklyRows.forEach((row) => {
    const tr = document.createElement('tr');
    tr.className = rowClass(row.status);

    tr.innerHTML = `
      <td>
        <select class="time-select" onchange="changeField(${row.id}, 'time', this.value)">
          ${generateTimeOptions(row.time)}
        </select>
      </td>
      <td>
        <select class="day-select" onchange="changeField(${row.id}, 'day', this.value)">
          ${DAYS.map(d => `<option${d === row.day ? ' selected' : ''}>${d}</option>`).join('')}
        </select>
      </td>
      <td>
        <input
          class="act-input"
          type="text"
          value="${row.activity.replace(/"/g, '&quot;')}"
          placeholder="Tambahkan aktivitas..."
          onchange="changeField(${row.id}, 'activity', this.value)"
        />
      </td>
      <td>
        <select class="status-select" onchange="changeField(${row.id}, 'status', this.value)">
          <option value="none"${row.status === 'none'     ? ' selected' : ''}>— Belum</option>
          <option value="progress"${row.status === 'progress' ? ' selected' : ''}>● On-going</option>
          <option value="done"${row.status === 'done'     ? ' selected' : ''}>✓ Selesai</option>
        </select>
      </td>
      <td>
        <button class="del-btn" onclick="delRow(${row.id})" title="Hapus">×</button>
      </td>
    `;

    tbody.appendChild(tr);
  });

  updateStats(weeklyRows);
}

async function changeField(id, field, value) {
  await fetch('/weeklyplanner/api/weekly.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, field, value })
  });
  await fetchRows();
}

async function delRow(id) {
  await fetch('/weeklyplanner/api/weekly.php', {
    method: 'DELETE',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id })
  });
  await fetchRows();
}

async function addRow() {
  await fetch('/weeklyplanner/api/weekly.php', { method: 'POST' });
  await fetchRows();
}

function updateStats(rows) {
  const done  = rows.filter(r => r.status === 'done').length;
  const prog  = rows.filter(r => r.status === 'progress').length;
  const total = rows.length || 1;
  const pct   = Math.round((done / total) * 100);

  $('statDone').textContent = done;
  $('statProg').textContent = prog;
  $('statPct').textContent  = pct + '%';
  $('progFill').style.width = pct + '%';
  $('progLeft').textContent  = pct + '%';
  $('progRight').textContent = done + ' / ' + rows.length + ' task selesai';
}

// Initial fetch
fetchRows();

/* ─────────────────────────────────────
   TO-DO LIST
───────────────────────────────────── */

let todos = load('wp_todos', [
  { text: 'Review laporan Q2',            done: false },
  { text: 'Kirim email follow-up klien',  done: true  },
  { text: 'Update dokumentasi proyek',    done: false },
]);

function renderTodos() {
  const container = $('todoList');
  container.innerHTML = '';

  todos.forEach((todo, index) => {
    const div = document.createElement('div');
    div.className = 'todo-item';

    div.innerHTML = `
      <input
        type="checkbox"
        class="todo-check"
        ${todo.done ? 'checked' : ''}
        onchange="toggleTodo(${index})"
      />
      <input
        class="todo-text${todo.done ? ' done' : ''}"
        type="text"
        value="${todo.text.replace(/"/g, '&quot;')}"
        placeholder="Nama tugas..."
        onchange="editTodo(${index}, this.value)"
      />
      <button class="del-btn" onclick="delTodo(${index})" title="Hapus">×</button>
    `;

    container.appendChild(div);
  });

  save('wp_todos', todos);
}

function toggleTodo(index) {
  todos[index].done = !todos[index].done;
  renderTodos();
}

function editTodo(index, value) {
  todos[index].text = value;
  save('wp_todos', todos);
}

function delTodo(index) {
  todos.splice(index, 1);
  renderTodos();
}

function addTodo() {
  todos.push({ text: '', done: false });
  renderTodos();
  setTimeout(() => {
    const inputs = document.querySelectorAll('.todo-text');
    if (inputs.length) inputs[inputs.length - 1].focus();
  }, 50);
}

// Initial render
renderTodos();

/* ─────────────────────────────────────
   NOTES
───────────────────────────────────── */

const notesEl = $('notesArea');
notesEl.value = load('wp_notes', '');
notesEl.addEventListener('input', () => save('wp_notes', notesEl.value));

/* ─────────────────────────────────────
   POMODORO TIMER
───────────────────────────────────── */

let pomoSeconds  = 25 * 60;
let pomoRunning  = false;
let pomoInterval = null;
let pomoSessions = 0;
let pomoMode     = 'focus';

const MODE_LABELS = {
  focus: 'Fokus',
  short: 'Istirahat Pendek',
  long:  'Istirahat Panjang',
};

function formatTime(seconds) {
  const m = String(Math.floor(seconds / 60)).padStart(2, '0');
  const s = String(seconds % 60).padStart(2, '0');
  return `${m}:${s}`;
}

function updatePomoDisplay() {
  $('pomoTime').textContent = formatTime(pomoSeconds);
  document.title = pomoRunning
    ? `(${formatTime(pomoSeconds)}) Weekly Planner`
    : 'Weekly Planner';
}

function setMode(mode, minutes, buttonEl) {
  clearInterval(pomoInterval);
  pomoRunning = false;

  const startBtn = $('btnStart');
  startBtn.textContent = 'Mulai';
  startBtn.classList.remove('active');

  pomoMode    = mode;
  pomoSeconds = minutes * 60;

  $('pomoModeBadge').textContent = MODE_LABELS[mode];

  document.querySelectorAll('.pomo-mode-btn').forEach(b => b.classList.remove('selected'));
  if (buttonEl) buttonEl.classList.add('selected');

  updatePomoDisplay();
}

function togglePomo() {
  const startBtn = $('btnStart');

  if (pomoRunning) {
    clearInterval(pomoInterval);
    pomoRunning = false;
    startBtn.textContent = 'Lanjut';
    startBtn.classList.remove('active');
    document.title = 'Weekly Planner';
  } else {
    pomoRunning = true;
    startBtn.textContent = 'Pause';
    startBtn.classList.add('active');

    pomoInterval = setInterval(() => {
      if (pomoSeconds > 0) {
        pomoSeconds--;
        updatePomoDisplay();
      } else {
        clearInterval(pomoInterval);
        pomoRunning = false;
        startBtn.textContent = 'Mulai';
        startBtn.classList.remove('active');
        document.title = 'Weekly Planner';

        if (pomoMode === 'focus') {
          pomoSessions++;
          $('pomoCnt').textContent = 'Sesi selesai: ' + pomoSessions;

          if (Notification.permission === 'granted') {
            new Notification('Sesi fokus selesai!', { body: 'Waktunya istirahat sejenak.' });
          } else {
            alert('Sesi fokus selesai! Waktunya istirahat.');
          }
        } else {
          alert('Istirahat selesai! Kembali fokus.');
        }
      }
    }, 1000);
  }
}

function resetPomo() {
  clearInterval(pomoInterval);
  pomoRunning = false;

  const startBtn = $('btnStart');
  startBtn.textContent = 'Mulai';
  startBtn.classList.remove('active');

  const durations = { focus: 25, short: 5, long: 15 };
  pomoSeconds = durations[pomoMode] * 60;

  updatePomoDisplay();
  document.title = 'Weekly Planner';
}

updatePomoDisplay();

document.addEventListener('click', function requestNotif() {
  if (Notification && Notification.permission === 'default') {
    Notification.requestPermission();
  }
  document.removeEventListener('click', requestNotif);
}, { once: true });
