<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Доступные служебные авто — демо</title>
  <style>body{font-family:Arial,Helvetica,sans-serif;margin:20px}label{display:block;margin-top:8px}</style>
</head>
<body>
  <h1>Доступные автомобили</h1>

  <div>
    <label>Сотрудник: 
      <select id="sel-user">
        @foreach($users as $u)
          <option value="{{ $u->email ?? $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
        @endforeach
      </select>
    </label>
    <label>Начало (ISO): <input id="start" type="datetime-local" value="2025-09-01T09:00"></label>
    <label>Окончание (ISO): <input id="end" type="datetime-local" value="2025-09-01T12:00"></label>
    <label>Brand: 
      <select id="sel-brand">
        <option value="">(все)</option>
        @foreach($brands as $b)
          <option value="{{ $b }}">{{ $b }}</option>
        @endforeach
      </select>
    </label>
    <label>Driver: 
      <select id="sel-driver">
        <option value="">(все)</option>
        @foreach($drivers as $d)
          <option value="{{ $d->id }}">{{ $d->full_name }}</option>
        @endforeach
      </select>
    </label>
    <label>Category ID (опционально): 
      <select id="category_id">
        <option value="">(все)</option>
        @foreach($categories as $c)
          <option value="{{ $c->id }}">{{ $c->name }} (rank {{ $c->rank }})</option>
        @endforeach
      </select>
    </label>
    <label><input type="checkbox" id="debug"> Debug режим</label>
    <button id="load">Показать доступные</button>
  </div>

  <h2>Результат</h2>
  <table id="cars-table" border="0" cellspacing="0" cellpadding="8" style="border-collapse:collapse;width:100%;max-width:900px">
    <thead style="background:#f0f0f0"><tr>
      <th>#</th>
      <th>Гос. знак</th>
      <th>VIN</th>
      <th>Марка / Модель</th>
      <th>Категория</th>
      <th>Водитель</th>
      <th>Действия</th>
    </tr></thead>
    <tbody id="cars-body"></tbody>
  </table>

  <div id="empty" style="margin-top:12px;color:#666">Ничего не найдено.</div>

  <pre id="out" style="background:#f8f8f8;padding:10px;border:1px solid #ddd;max-height:200px;overflow:auto;margin-top:8px"></pre>

  <h2>Брони выбранного пользователя</h2>
  <div style="background:#f0f8ff;padding:10px;border:1px solid #bbb;margin-bottom:10px;border-radius:4px;">
    <strong>Инструкция:</strong>
    <ul style="margin:5px 0;">
      <li><strong>Обычное бронирование:</strong> Выберите время, машину и нажмите "Забронировать" - система автоматически найдет свободное время если выбранное занято</li>
      <li><strong>Умное бронирование:</strong> Автоматически найдет подходящую машину и свободное время для выбранного пользователя</li>
      <li><strong>Тест бронирования:</strong> Создает тестовую бронь с предустановленными данными для демонстрации</li>
    </ul>
  </div>
  <table id="bookings-table" border="0" cellspacing="0" cellpadding="8" style="border-collapse:collapse;width:100%;max-width:900px">
    <thead style="background:#f7f7f7"><tr>
      <th>ID</th>
      <th>Период</th>
      <th>Пользователь</th>
      <th>Машина</th>
      <th>Категория</th>
      <th>Водитель</th>
      <th>Действия</th>
    </tr></thead>
    <tbody id="bookings-body"></tbody>
  </table>
  <div id="no-bookings" style="color:#666;margin-top:6px">У выбранного пользователя нет броней.</div>

  <div style="margin-top:8px">
    <button id="pager-prev">← Назад</button>
    <button id="pager-next">Вперёд →</button>
  <button id="export-csv">Экспорт CSV</button>
  <button id="print-list">Печать списка</button>
  <button id="smart-booking" style="margin-left:12px;color:#090">Умное бронирование</button>
  <button id="reset-bookings" style="margin-left:12px;color:#900">Сбросить все брони</button>
  <button id="view-logs" style="margin-left:12px;color:#00f">Показать логи</button>
  <button id="test-booking" style="margin-left:12px;color:#090">Тест бронирования</button>
  </div>

  <div id="logs-section" style="display:none; margin-top:20px;">
    <h3>Логи системы</h3>
    <pre id="logs-content" style="background:#f0f0f0;padding:10px;max-height:300px;overflow:auto;border:1px solid #ccc"></pre>
  </div>

  <!-- modal -->
  <div id="modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);align-items:center;justify-content:center">
    <div style="background:#fff;padding:20px;border-radius:6px;min-width:320px;max-width:90%;margin:40px auto;">
      <h3 id="modal-title">Подтвердите бронь</h3>
      <p id="modal-body">Вы уверены, что хотите забронировать машину?</p>
      <div id="modal-msg" style="display:none;margin-top:8px;color:#900"></div>
      <div style="text-align:right;margin-top:12px">
        <button id="modal-cancel">Отмена</button>
        <button id="modal-confirm">Подтвердить</button>
        <button id="modal-close" style="display:none">Закрыть</button>
      </div>
    </div>
  </div>

  <script>
    const outEl = document.getElementById('out');
    const carsBodyEl = document.getElementById('cars-body');
    const emptyEl = document.getElementById('empty');
    const modalEl = document.getElementById('modal');
    const modalBodyEl = document.getElementById('modal-body');

  let currentPage = 1;

  function setOut(text) {
      if (outEl) outEl.textContent = text;
      else console.warn('out element not found, message:', text);
    }

    async function getToken(email) {
      try {
        const url = '/api/dev/make-token' + (email ? '?email=' + encodeURIComponent(email) : '');
        console.log('Запрос токена для:', email, 'URL:', url);
        const res = await fetch(url);
        if (!res.ok) throw new Error('token fetch failed: ' + res.status);
        const json = await res.json();
        console.log('Получен токен для', email, ':', json);
        setOut('Токен получен для: ' + email + ' (ID: ' + json.user_id + ')');
        return json;
      } catch (err) {
        console.error('getToken error', err);
        setOut('Ошибка получения токена: ' + err.message);
        throw err;
      }
    }

    function fmtCategory(c) { return c ? (c.name + ' (rank ' + c.rank + ')') : '' }
    function renderTable(arr) {
      // normalize paginator response: could be { data: [...] } or an array
      if (arr && arr.data && Array.isArray(arr.data)) arr = arr.data;
      if (!Array.isArray(arr)) arr = [];

      const body = document.getElementById('cars-body');
      body.innerHTML = '';
      if (arr.length === 0) {
        document.getElementById('empty').style.display = 'block';
        return;
      }
      document.getElementById('empty').style.display = 'none';
      arr.forEach((car, i) => {
  const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${i+1}</td>
          <td>${car.license_plate ?? ''}</td>
          <td>${car.vin ?? ''}</td>
          <td>${car.model.brand} ${car.model.model}</td>
          <td>${car.model.category.name} (rank ${car.model.category.rank})</td>
          <td>${car.driver.full_name} <br><small>${car.driver.phone}</small></td>
          <td><button data-car-id="${car.id}" class="btn-book">Забронировать</button></td>
        `;
        body.appendChild(tr);
      });

  // attach handlers
  document.querySelectorAll('.btn-book').forEach(btn => {
        btn.addEventListener('click', (ev) => {
          const id = ev.currentTarget.getAttribute('data-car-id');
          openConfirm(id);
        });
      });
    }

    function openConfirm(carId) {
      window._pendingCarId = carId;
      
      const userEmail = document.getElementById('sel-user').value;
      const start = document.getElementById('start').value;
      const end = document.getElementById('end').value;
      
      document.getElementById('modal-body').textContent = 
        'Забронировать машину ID ' + carId + ' для пользователя ' + userEmail + 
        ' на время ' + start + ' - ' + end + '? (Если время занято, система автоматически найдет свободное)';
      const modal = document.getElementById('modal'); modal.style.display = 'flex';
    }

    function closeModal() { 
      document.getElementById('modal').style.display = 'none'; 
      window._pendingCarId = null; 
      // Сбрасываем состояние модального окна
      document.getElementById('modal-msg').style.display = 'none';
      document.getElementById('modal-confirm').style.display = 'inline-block';
      document.getElementById('modal-close').style.display = 'none';
    }

    document.getElementById('load').addEventListener('click', async () => {
      try {
        setOut('Загрузка...');
        const start = document.getElementById('start').value;
        const end = document.getElementById('end').value;
  const model = document.getElementById('model_id') ? document.getElementById('model_id').value : '';
  const category = document.getElementById('category_id').value;
  const brand = document.getElementById('sel-brand').value;
  const driver = document.getElementById('sel-driver').value;
  const userEmail = document.getElementById('sel-user').value;

        if (!start || !end) {
          setOut('Введите start и end.');
          return;
        }

  const tok = await getToken(userEmail);
  const token = tok.token;

        const qs = new URLSearchParams({ start, end });
  if (model) qs.set('model_id', model);
  if (category) qs.set('category_id', category);
  if (brand) qs.set('brand', brand);
  if (driver) qs.set('driver_id', driver);
  if (document.getElementById('debug') && document.getElementById('debug').checked) qs.set('debug', '1');
  qs.set('page', currentPage);

  console.log('requesting available cars', qs.toString());

        const r = await fetch('/api/available-cars?' + qs.toString(), {
          headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });

        if (!r.ok) {
          const text = await r.text();
          throw new Error('API error ' + r.status + ': ' + text);
        }

    const json = await r.json();
    window._lastAvailable = json;
  setOut('');
  renderTable(json.data || []);
    
    // Показываем debug информацию если есть
    if (json.debug) {
      console.log('Debug info:', json.debug);
      let debugMsg = 'Debug: Пользователь ' + json.debug.user_email + ' (' + json.debug.position + ')' +
        ', доступные категории: ' + json.debug.allowed_category_ids.join(',') + 
        ', всего автомобилей: ' + json.debug.all_cars.length;
      setOut(debugMsg);
    }
    
    // update pagination
    document.getElementById('pager-prev').disabled = (json.meta.current_page <= 1);
    document.getElementById('pager-next').disabled = (json.meta.current_page >= json.meta.last_page);
    currentPage = json.meta.current_page;
      } catch (err) {
        console.error(err);
        setOut('Ошибка: ' + (err.message || err));
      }
    });

  document.getElementById('pager-prev').addEventListener('click', () => { if (currentPage>1) { currentPage--; document.getElementById('load').click(); } });
  document.getElementById('pager-next').addEventListener('click', () => { currentPage++; document.getElementById('load').click(); });
    document.getElementById('export-csv').addEventListener('click', () => {
      const resp = window._lastAvailable;
      if (!resp || !resp.data) { setOut('Нет данных для экспорта'); return; }
      const rows = (resp.data.data || resp.data).map(c => [c.id, c.license_plate, c.vin, c.model.brand, c.model.model, c.model.category.name, c.driver.full_name, c.driver.phone]);
      const csv = ['id,license_plate,vin,brand,model,category,driver,phone', ...rows.map(r => r.map(v => '"' + (v ?? '') + '"').join(','))].join('\n');
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a'); a.href = url; a.download = 'available_cars.csv'; a.click(); URL.revokeObjectURL(url);
    });
    document.getElementById('print-list').addEventListener('click', () => {
      const w = window.open('', '_blank');
      w.document.write('<html><head><title>Available cars</title></head><body>' + document.getElementById('cars-table').outerHTML + '</body></html>');
      w.document.close();
      w.print();
    });

    document.getElementById('modal-cancel').addEventListener('click', () => closeModal());
    document.getElementById('modal-confirm').addEventListener('click', async () => {
  try {
    const userEmail = document.getElementById('sel-user').value;
    console.log('Начинаем бронирование для пользователя:', userEmail);
    const tok = await getToken(userEmail);
    const token = tok.token;
    console.log('Получен токен для бронирования:', token.substring(0, 20) + '...');
        const carId = window._pendingCarId;
        if (!carId) return closeModal();
        
        // Пытаемся забронировать выбранную машину на выбранное время
        let start = document.getElementById('start').value;
        let end = document.getElementById('end').value;
        let finalCarId = carId;
        let purpose = 'Бронирование через интерфейс';
        
        console.log('Попытка бронирования:', {
          user: userEmail,
          car: finalCarId,
          start: start,
          end: end
        });
        
        const body = { car_id: finalCarId, start: start, end: end, purpose: purpose };

        console.log('Отправляем запрос на бронирование:', body);
        console.log('URL:', '/api/bookings');
        console.log('Token (first 20 chars):', token.substring(0, 20));
        console.log('Headers:', { 'Authorization': 'Bearer ' + token.substring(0, 20) + '...', 'Accept': 'application/json', 'Content-Type': 'application/json' });
        
        const r = await fetch('/api/bookings', {
          method: 'POST',
          headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
          body: JSON.stringify(body),
        });

        console.log('Response status:', r.status);
        console.log('Response headers:', [...r.headers.entries()]);
        
        let json;
        try {
          json = await r.json();
        } catch (parseError) {
          console.error('Failed to parse JSON response:', parseError);
          const textResponse = await r.text();
          console.log('Raw response text:', textResponse);
          throw new Error('Invalid JSON response: ' + textResponse);
        }
        
        console.log('Ответ на бронирование:', json);
        const modalMsg = document.getElementById('modal-msg');
        
        if (r.status === 201) {
            modalMsg.style.display = 'block';
            modalMsg.style.color = '#080';
            modalMsg.textContent = 'Бронь создана: ID ' + (json.data?.id ?? '') + ' для пользователя ' + userEmail;
            document.getElementById('modal-confirm').style.display = 'none';
            document.getElementById('modal-close').style.display = 'inline-block';
        } else if (r.status === 422 && json.message && json.message.includes('занята')) {
            // Если время занято, пытаемся найти альтернативное время
            console.log('Время занято, ищем альтернативное...');
            modalMsg.style.display = 'block';
            modalMsg.style.color = '#f60';
            modalMsg.textContent = 'Выбранное время занято, пытаемся найти свободное...';
            
            // Попробуем несколько альтернативных временных слотов
            const alternatives = [
              { start: "2025-09-02T08:00", end: "2025-09-02T11:00" },
              { start: "2025-09-02T12:00", end: "2025-09-02T15:00" },
              { start: "2025-09-02T16:00", end: "2025-09-02T19:00" },
              { start: "2025-09-03T09:00", end: "2025-09-03T12:00" },
              { start: "2025-09-03T13:00", end: "2025-09-03T16:00" },
              { start: "2025-09-04T09:00", end: "2025-09-04T12:00" }
            ];
            
            let booked = false;
            for (const alt of alternatives) {
              try {
                const altBody = { car_id: finalCarId, start: alt.start, end: alt.end, purpose: purpose + ' (автопоиск)' };
                const altR = await fetch('/api/bookings', {
                  method: 'POST',
                  headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                  body: JSON.stringify(altBody),
                });
                
                if (altR.status === 201) {
                  const altJson = await altR.json();
                  modalMsg.style.color = '#080';
                  modalMsg.textContent = 'Найдено свободное время! Бронь создана: ID ' + altJson.data.id + ' на ' + alt.start + ' - ' + alt.end;
                  booked = true;
                  break;
                }
              } catch (altErr) {
                console.log('Alternative booking failed:', altErr);
              }
            }
            
            if (!booked) {
              modalMsg.style.color = '#900';
              modalMsg.textContent = 'Не удалось найти свободное время для этой машины. Попробуйте другую машину или время.';
            }
            
            document.getElementById('modal-confirm').style.display = 'none';
            document.getElementById('modal-close').style.display = 'inline-block';
          } else {
            modalMsg.style.display = 'block';
            modalMsg.style.color = '#900';
            let errorMsg = 'Ошибка бронирования';
            if (r.status === 422 && json.message) {
              errorMsg = json.message; // "Машина уже занята в указанный интервал"
            } else if (r.status === 403) {
              errorMsg = 'Нет прав доступа. Проверьте, что выбран правильный пользователь.';
            } else if (r.status === 401) {
              errorMsg = 'Ошибка авторизации. Токен недействителен.';
            } else if (json.message) {
              errorMsg = json.message;
            } else {
              errorMsg = `HTTP ${r.status}: ${JSON.stringify(json)}`;
            }
            modalMsg.textContent = errorMsg;
          }
          document.getElementById('load').click();
          // refresh bookings list after creation
          await loadBookings();
      } catch (err) {
        console.error(err);
        const modalMsg = document.getElementById('modal-msg');
        modalMsg.style.display = 'block';
        modalMsg.style.color = '#900';
        modalMsg.textContent = 'Ошибка бронирования: ' + (err.message || err);
        document.getElementById('modal-close').style.display = 'inline-block';
        document.getElementById('modal-confirm').style.display = 'none';
      }
    });

    document.getElementById('modal-close').addEventListener('click', () => closeModal());

    // --- Bookings list and cancellation ---
    async function loadBookings() {
      try {
        const userEmail = document.getElementById('sel-user').value;
        console.log('Загружаем брони для пользователя:', userEmail);
        const tok = await getToken(userEmail);
        const token = tok.token;
        console.log('Получен токен для загрузки броней:', token.substring(0, 20) + '...');
        
        // Используем обычный эндпоинт с токеном выбранного пользователя
        const url = '/api/bookings';
        const r = await fetch(url, { 
          headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } 
        });
        
        if (!r.ok) throw new Error('bookings fetch failed ' + r.status);
        const json = await r.json();
        console.log('Ответ с бронями:', json);
        
        // Показываем debug информацию если есть
        if (json.debug) {
          console.log('Bookings Debug info:', json.debug);
        }
        
        // normalize: API may return { data: [...] } or { data: { data: [...] , meta:... } }
        let arr = [];
        if (json && json.data) {
          if (Array.isArray(json.data)) arr = json.data;
          else if (json.data.data && Array.isArray(json.data.data)) arr = json.data.data;
        }
        
        const body = document.getElementById('bookings-body');
        body.innerHTML = '';
        if (!arr.length) {
          document.getElementById('no-bookings').style.display = 'block';
        } else {
          document.getElementById('no-bookings').style.display = 'none';
        }
        arr.forEach(b => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${b.id}</td>
            <td>${b.starts_at} → ${b.ends_at}</td>
            <td>${b.user ? b.user.name : 'Неизвестно'} <br><small>${b.user ? b.user.email : ''}</small><br><em>${b.user && b.user.position ? b.user.position : 'Должность не указана'}</em></td>
            <td>${b.car.license_plate} <br><small>${b.car.vin}</small></td>
            <td>${b.car.model.category.name}</td>
            <td>${b.car.driver.full_name} <br><small>${b.car.driver.phone}</small></td>
            <td><button data-booking-id="${b.id}" class="btn-cancel">Отменить</button></td>
          `;
          body.appendChild(tr);
        });

        document.querySelectorAll('.btn-cancel').forEach(btn => {
          btn.addEventListener('click', async (ev) => {
            const id = ev.currentTarget.getAttribute('data-booking-id');
            if (!confirm('Отменить бронь #' + id + '?')) return;
            await cancelBooking(id);
          });
        });
      } catch (err) {
        console.error('loadBookings', err);
        setOut('Ошибка загрузки броней: ' + err.message);
      }
    }

    async function cancelBooking(id) {
      try {
        const userEmail = document.getElementById('sel-user').value;
        const tok = await getToken(userEmail);
        const token = tok.token;
        const r = await fetch('/api/bookings/' + id, { method: 'DELETE', headers: { 'Authorization': 'Bearer ' + token } });
        if (r.status === 204) {
          setOut('Бронь ' + id + ' отменена.');
          await loadBookings();
          document.getElementById('load').click();
        } else if (r.status === 403) {
          setOut('Нельзя отменить чужую бронь (403).');
        } else {
          const txt = await r.text();
          setOut('Ошибка отмены: ' + r.status + ' ' + txt);
        }
      } catch (err) {
        console.error('cancelBooking', err);
        setOut('Ошибка отмены брони: ' + err.message);
      }
    }

    // load bookings when user selection changes
    document.getElementById('sel-user').addEventListener('change', () => {
      // Очищаем результаты при смене пользователя
      const body = document.getElementById('cars-body');
      body.innerHTML = '';
      document.getElementById('empty').style.display = 'block';
      setOut('Выберите временной интервал и нажмите "Показать доступные"');
      
      // Загружаем брони для нового пользователя
      loadBookings();
    });

    // Просмотр логов
    document.getElementById('view-logs').addEventListener('click', async () => {
      try {
        const response = await fetch('/api/debug/logs');
        const data = await response.json();
        document.getElementById('logs-content').textContent = data.logs;
        const logsSection = document.getElementById('logs-section');
        logsSection.style.display = logsSection.style.display === 'none' ? 'block' : 'none';
      } catch (err) {
        console.error('Error loading logs:', err);
        alert('Ошибка загрузки логов');
      }
    });

    // Умное бронирование
    document.getElementById('smart-booking').addEventListener('click', async () => {
      try {
        const userEmail = document.getElementById('sel-user').value;
        console.log('=== УМНОЕ БРОНИРОВАНИЕ ===');
        console.log('Email пользователя:', userEmail);
        
        setOut('Умное бронирование для ' + userEmail + '...');
        
        // Получаем токен
        const tok = await getToken(userEmail);
        const token = tok.token;
        console.log('Токен получен');
        
        // Получаем доступные машины для этого пользователя
        const availableCarsResponse = await fetch('/api/available-cars?start=2025-09-02T09:00&end=2025-09-02T12:00', {
          headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        
        if (!availableCarsResponse.ok) {
          throw new Error('Не удалось получить доступные машины');
        }
        
        const availableCarsData = await availableCarsResponse.json();
        const availableCars = availableCarsData.data || [];
        
        if (availableCars.length === 0) {
          setOut('❌ Нет доступных машин для пользователя ' + userEmail);
          return;
        }
        
        // Выбираем первую доступную машину
        const selectedCar = availableCars[0];
        console.log('Выбрана машина:', selectedCar);
        
        // Пробуем разные временные слоты
        const timeSlots = [
          { start: "2025-09-02T09:00", end: "2025-09-02T12:00", label: "2 сент 09:00-12:00" },
          { start: "2025-09-02T13:00", end: "2025-09-02T16:00", label: "2 сент 13:00-16:00" },
          { start: "2025-09-02T17:00", end: "2025-09-02T20:00", label: "2 сент 17:00-20:00" },
          { start: "2025-09-03T09:00", end: "2025-09-03T12:00", label: "3 сент 09:00-12:00" },
          { start: "2025-09-03T13:00", end: "2025-09-03T16:00", label: "3 сент 13:00-16:00" },
          { start: "2025-09-04T09:00", end: "2025-09-04T12:00", label: "4 сент 09:00-12:00" }
        ];
        
        let booked = false;
        for (const slot of timeSlots) {
          try {
            const bookingData = {
              car_id: selectedCar.id,
              start: slot.start,
              end: slot.end,
              purpose: 'Умное бронирование: ' + userEmail
            };
            
            console.log('Пробуем слот:', slot.label, 'для машины:', selectedCar.license_plate);
            
            const response = await fetch('/api/bookings', {
              method: 'POST',
              headers: { 
                'Authorization': 'Bearer ' + token, 
                'Accept': 'application/json', 
                'Content-Type': 'application/json' 
              },
              body: JSON.stringify(bookingData),
            });
            
            if (response.ok) {
              const result = await response.json();
              setOut('✅ УМНОЕ БРОНИРОВАНИЕ УСПЕШНО: Бронь ID ' + result.data.id + 
                     ' для ' + userEmail + ' на машину ' + selectedCar.license_plate + 
                     ' время ' + slot.label);
              booked = true;
              break;
            } else {
              console.log('Слот занят:', slot.label);
            }
            
          } catch (error) {
            console.log('Ошибка для слота:', slot.label, error);
          }
        }
        
        if (!booked) {
          setOut('❌ УМНОЕ БРОНИРОВАНИЕ: Не удалось найти свободное время для ' + userEmail);
        }
        
        // Обновляем список броней
        await loadBookings();
        
      } catch (error) {
        console.error('Ошибка умного бронирования:', error);
        setOut('❌ ОШИБКА УМНОГО БРОНИРОВАНИЯ: ' + error.message);
      }
    });

    // Тест бронирования
    document.getElementById('test-booking').addEventListener('click', async () => {
      try {
        const userEmail = document.getElementById('sel-user').value;
        console.log('=== ТЕСТ БРОНИРОВАНИЯ ===');
        console.log('Email пользователя:', userEmail);
        
        setOut('Тест бронирования для ' + userEmail + '...');
        
        // Получаем токен
        const tok = await getToken(userEmail);
        const token = tok.token;
        console.log('Токен получен, длина:', token.length);
        
        // Разные данные для разных пользователей, чтобы избежать пересечений
        let testData;
        if (userEmail.includes('manager')) {
          testData = {
            car_id: 1, // Toyota Camry (категория 1)
            start: "2025-09-02T09:00",
            end: "2025-09-02T12:00",
            purpose: "Тест: Менеджер"
          };
        } else if (userEmail.includes('engineer')) {
          testData = {
            car_id: 2, // Skoda Octavia (категория 2)
            start: "2025-09-02T13:00",
            end: "2025-09-02T16:00",
            purpose: "Тест: Инженер"
          };
        } else if (userEmail.includes('director')) {
          testData = {
            car_id: 6, // BMW X5 (категория 1)
            start: "2025-09-02T17:00",
            end: "2025-09-02T20:00",
            purpose: "Тест: Директор"
          };
        } else if (userEmail.includes('test')) {
          testData = {
            car_id: 8, // Volkswagen Polo (категория 3)
            start: "2025-09-03T09:00",
            end: "2025-09-03T12:00",
            purpose: "Тест: Тестовый"
          };
        } else {
          testData = {
            car_id: 4,
            start: "2025-09-01T21:00",
            end: "2025-09-01T23:00",
            purpose: "Тест: По умолчанию"
          };
        }
        
        console.log('Отправляем тестовые данные:', testData);
        
        // Отправляем запрос
        const response = await fetch('/api/bookings', {
          method: 'POST',
          headers: { 
            'Authorization': 'Bearer ' + token, 
            'Accept': 'application/json', 
            'Content-Type': 'application/json' 
          },
          body: JSON.stringify(testData),
        });
        
        console.log('Статус ответа:', response.status);
        
        const result = await response.json();
        console.log('Результат:', result);
        
        if (response.ok) {
          setOut('✅ ТЕСТ УСПЕШЕН: Бронь создана ID ' + result.data.id + ' для ' + userEmail);
        } else {
          setOut('❌ ТЕСТ ПРОВАЛЕН: ' + (result.message || JSON.stringify(result)));
        }
        
        // Обновляем список броней
        await loadBookings();
        
      } catch (error) {
        console.error('Ошибка теста:', error);
        setOut('❌ ОШИБКА ТЕСТА: ' + error.message);
      }
    });

    // initial load of bookings for default selected user
    loadBookings();

    document.getElementById('reset-bookings').addEventListener('click', async () => {
      if (!confirm('Удалить все брони во всей БД? Это действие необратимо для демо.')) return;
      try {
        // use dev token for currently selected user to authenticate; admin endpoint allowed only in local
        const userEmail = document.getElementById('sel-user').value;
        const tok = await getToken(userEmail);
        const token = tok.token;
        const r = await fetch('/api/admin/bookings', { method: 'DELETE', headers: { 'Authorization': 'Bearer ' + token } });
        if (r.status === 204) {
          setOut('Все брони удалены.');
          await loadBookings();
          document.getElementById('load').click();
        } else {
          const t = await r.text();
          setOut('Ошибка удаления бронирований: ' + r.status + ' ' + t);
        }
      } catch (err) {
        setOut('Ошибка: ' + err.message);
      }
    });
  </script>
</body>
</html>
