<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Доступные служебные авто — демо</title>
  <style>
    * {
      box-sizing: border-box;
    }
    
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
      margin: 0;
      padding: 20px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      color: #333;
    }
    
    .container {
      max-width: 1200px;
        // Сбрасываем состояние модального окна
        document.getElementById('modal-msg').style.display = 'none';
        document.getElementById('modal-msg').className = 'status-error';
        document.getElementById('modal-confirm').style.display = 'inline-block';   margin: 0 auto;
      background: white;
      border-radius: 15px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    
    .header {
      background: linear-gradient(45deg, #2193b0, #6dd5ed);
      color: white;
      padding: 30px;
      text-align: center;
    }
    
    .header h1 {
      margin: 0;
      font-size: 2.5rem;
      font-weight: 300;
    }
    
    .content {
      padding: 30px;
    }
    
    .form-section {
      background: #f8fafc;
      border-radius: 12px;
      padding: 25px;
      margin-bottom: 30px;
      border: 1px solid #e2e8f0;
    }
    
    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      margin-bottom: 25px;
    }
    
    label {
      display: block;
      font-weight: 600;
      color: #4a5568;
      margin-bottom: 8px;
    }
    
    select, input[type="datetime-local"] {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #e2e8f0;
      border-radius: 8px;
      font-size: 16px;
      transition: all 0.3s ease;
      background: white;
    }
    
    select:focus, input[type="datetime-local"]:focus {
      outline: none;
      border-color: #4299e1;
      box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
    }
    
    .btn {
      padding: 12px 24px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
      margin: 5px;
    }
    
    .btn-primary {
      background: linear-gradient(45deg, #4299e1, #3182ce);
      color: white;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(66, 153, 225, 0.3);
    }
    
    .btn-success {
      background: linear-gradient(45deg, #48bb78, #38a169);
      color: white;
    }
    
    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(72, 187, 120, 0.3);
    }
    
    .btn-danger {
      background: linear-gradient(45deg, #f56565, #e53e3e);
      color: white;
    }
    
    .btn-danger:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(245, 101, 101, 0.3);
    }
    
    .btn-secondary {
      background: #edf2f7;
      color: #4a5568;
      border: 2px solid #e2e8f0;
    }
    
    .btn-secondary:hover {
      background: #e2e8f0;
      transform: translateY(-1px);
    }
    
    .section-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: #2d3748;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 3px solid #e2e8f0;
    }
    
    .table-container {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      margin-bottom: 20px;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
    }
    
    th {
      background: linear-gradient(45deg, #667eea, #764ba2);
      color: white;
      padding: 16px 12px;
      font-weight: 600;
      text-align: left;
      font-size: 14px;
    }
    
    td {
      padding: 16px 12px;
      border-bottom: 1px solid #e2e8f0;
      vertical-align: top;
    }
    
    tr:hover {
      background: #f7fafc;
    }
    
    .status-info {
      background: #ebf8ff;
      border: 1px solid #bee3f8;
      border-radius: 8px;
      padding: 16px;
      margin: 20px 0;
      color: #2c5282;
    }
    
    .status-success {
      background: #f0fff4;
      border: 1px solid #9ae6b4;
      color: #22543d;
    }
    
    .status-error {
      background: #fed7d7;
      border: 1px solid #feb2b2;
      color: #742a2a;
    }
    
    .pagination {
      display: flex;
      gap: 10px;
      align-items: center;
      justify-content: center;
      margin: 20px 0;
      flex-wrap: wrap;
    }
    
    .modal {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      padding: 20px;
    }
    
    .modal-content {
      background: white;
      padding: 30px;
      border-radius: 15px;
      min-width: 320px;
      max-width: 500px;
      width: 100%;
      box-shadow: 0 25px 50px rgba(0,0,0,0.25);
    }
    
    .modal-title {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 15px;
      color: #2d3748;
    }
    
    .modal-body {
      margin-bottom: 20px;
      color: #4a5568;
      line-height: 1.6;
    }
    
    .modal-actions {
      display: flex;
      gap: 15px;
      justify-content: flex-end;
      flex-wrap: wrap;
    }
    
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #718096;
    }
    
    .empty-state-icon {
      font-size: 4rem;
      margin-bottom: 20px;
      opacity: 0.5;
    }
    
    /* Мобильная адаптивность */
    @media (max-width: 768px) {
      body {
        padding: 10px;
      }
      
      .header {
        padding: 20px 15px;
      }
      
      .header h1 {
        font-size: 1.8rem;
      }
      
      .content {
        padding: 20px 15px;
      }
      
      .form-section {
        padding: 20px 15px;
      }
      
      .form-grid {
        grid-template-columns: 1fr;
        gap: 15px;
      }
      
      .table-container {
        overflow-x: auto;
      }
      
      table {
        min-width: 600px;
      }
      
      th, td {
        padding: 12px 8px;
        font-size: 14px;
      }
      
      .btn {
        padding: 10px 16px;
        font-size: 14px;
        margin: 3px;
      }
      
      .pagination {
        justify-content: center;
      }
      
      .modal-content {
        margin: 20px;
        padding: 20px;
      }
      
      .modal-actions {
        flex-direction: column;
      }
      
      .modal-actions .btn {
        width: 100%;
        margin: 0;
      }
    }
    
    @media (max-width: 480px) {
      .header h1 {
        font-size: 1.5rem;
      }
      
      .form-grid {
        gap: 10px;
      }
      
      .form-section {
        padding: 15px 10px;
      }
      
      .content {
        padding: 15px 10px;
      }
      
      th, td {
        padding: 8px 6px;
        font-size: 12px;
      }
      
      .btn {
        padding: 8px 12px;
        font-size: 13px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>🚗 Автопарк Компании</h1>
      <p style="margin:10px 0 0 0;font-size:1.1rem;opacity:0.9;font-weight:300;">
        Система бронирования служебного транспорта с контролем доступа
      </p>
    </div>
    
    <div class="content">
      <div class="form-section">
        <div class="form-grid">
        <div class="form-grid">
          <div>
            <label>👤 Пользователь:</label>
            <select id="sel-user">
              @foreach($users as $u)
                <option value="{{ $u->email ?? $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
              @endforeach
            </select>
          </div>
          
          <div>
            <label>🕐 Начало брони:</label>
            <input id="start" type="datetime-local" value="2025-09-01T09:00">
          </div>
          
          <div>
            <label>🕐 Окончание брони:</label>
            <input id="end" type="datetime-local" value="2025-09-01T12:00">
          </div>
          
          <div>
            <label>🚗 Марка автомобиля:</label>
            <select id="sel-brand">
              <option value="">(все марки)</option>
              @foreach($brands as $b)
                <option value="{{ $b }}">{{ $b }}</option>
              @endforeach
            </select>
          </div>
          
          <div>
            <label>👨‍💼 Закрепленный водитель:</label>
            <select id="sel-driver">
              <option value="">(любой водитель)</option>
              @foreach($drivers as $d)
                <option value="{{ $d->id }}">{{ $d->full_name }}</option>
              @endforeach
            </select>
          </div>
          
          <div>
            <label>⭐ Категория комфорта:</label>
            <select id="category_id">
              <option value="">(все категории)</option>
              @foreach($categories as $c)
                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->rank }} уровень)</option>
              @endforeach
            </select>
          </div>
        </div>
        
        <div style="text-align: center;">
          <button id="load" class="btn btn-primary">🔍 Найти автомобили</button>
          <button id="toggle-help" class="btn btn-secondary" style="margin-left:10px;">📚 Показать руководство</button>
        </div>
        
        <div id="help-section" style="display:none;background:linear-gradient(135deg, #e6f3ff 0%, #f0f8ff 100%);padding:20px;border-radius:12px;margin-top:20px;font-size:14px;color:#1a365d;border-left:4px solid #4299e1;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
          <h4 style="margin:0 0 15px 0;color:#2d3748;font-size:16px;">� Руководство пользователя системы автопарка</h4>
          
          <div style="margin-bottom:15px;">
            <strong>🚗 Структура автопарка:</strong>
            <ul style="margin:5px 0 0 0;padding-left:20px;line-height:1.5;">
              <li>В компании <strong>9 служебных автомобилей</strong> разных категорий комфорта</li>
              <li>За каждым автомобилем <strong>закреплен персональный водитель</strong></li>
              <li>Все автомобили доступны для бронирования сотрудниками согласно их должности</li>
            </ul>
          </div>

          <div style="margin-bottom:15px;">
            <strong>👥 Категории доступа:</strong>
            <ul style="margin:5px 0 0 0;padding-left:20px;line-height:1.5;">
              <li><strong>Менеджер:</strong> доступны автомобили 1-й и 2-й категории (эконом и комфорт)</li>
              <li><strong>Инженер:</strong> доступны автомобили 2-й и 3-й категории (комфорт и стандарт)</li>
              <li><strong>Директор:</strong> доступны автомобили всех категорий (включая премиум)</li>
              <li><strong>Тестовый пользователь:</strong> доступны автомобили 3-й категории (стандарт)</li>
            </ul>
          </div>

          <div style="margin-bottom:15px;">
            <strong>🔍 Как пользоваться поиском:</strong>
            <ol style="margin:5px 0 0 0;padding-left:20px;line-height:1.5;">
              <li><strong>Выберите себя</strong> в поле "Пользователь" (определяет доступные вам автомобили)</li>
              <li><strong>Укажите время</strong> начала и окончания поездки</li>
              <li><strong>Настройте фильтры</strong> (по желанию):
                <ul style="margin:3px 0;padding-left:15px;">
                  <li>Марка автомобиля - если нужна конкретная марка</li>
                  <li>Закрепленный водитель - если хотите поехать с определенным водителем</li>
                  <li>Категория комфорта - если нужен определенный уровень автомобиля</li>
                </ul>
              </li>
              <li><strong>Нажмите "Найти автомобили"</strong> - система покажет доступные варианты</li>
            </ol>
          </div>

          <div style="margin-bottom:15px;">
            <strong>📅 Процесс бронирования:</strong>
            <ol style="margin:5px 0 0 0;padding-left:20px;line-height:1.5;">
              <li>В таблице результатов выберите подходящий автомобиль</li>
              <li>Нажмите кнопку <strong>"🚗 Забронировать"</strong></li>
              <li>Подтвердите бронирование в появившемся окне</li>
              <li>Система автоматически:
                <ul style="margin:3px 0;padding-left:15px;">
                  <li>Проверит доступность автомобиля в указанное время</li>
                  <li>Назначит закрепленного водителя</li>
                  <li>Создаст бронь на ваше имя</li>
                  <li>Если время занято - найдет альтернативное свободное время</li>
                </ul>
              </li>
            </ol>
          </div>

          <div style="margin-bottom:0;">
            <strong>📋 Управление бронями:</strong>
            <ul style="margin:5px 0 0 0;padding-left:20px;line-height:1.5;">
              <li>Все ваши брони отображаются в таблице <strong>"Брони выбранного пользователя"</strong></li>
              <li>Каждая бронь содержит: период, автомобиль, категорию, назначенного водителя</li>
              <li>Ненужные брони можно отменить кнопкой <strong>"❌ Отменить"</strong></li>
              <li>Для очистки всех броней (администратор) используйте <strong>"🗑️ Сбросить брони"</strong></li>
            </ul>
          </div>
        </div>
      </div>

      <h2 class="section-title">📋 Результаты поиска</h2>
      <div style="background:#f7fafc;padding:12px;border-radius:8px;margin-bottom:15px;font-size:13px;color:#4a5568;border:1px solid #e2e8f0;">
        <strong>📊 Категории автомобилей:</strong>
        <span style="margin-left:10px;">
          ⭐ <strong>Первая (rank 1)</strong> - Премиум класс (BMW, Mercedes) | 
          ⭐ <strong>Вторая (rank 2)</strong> - Комфорт класс (Skoda, Nissan) | 
          ⭐ <strong>Третья (rank 3)</strong> - Стандарт класс (Volkswagen, Hyundai)
        </span>
      </div>
      <div class="table-container">
        <table id="cars-table">
          <thead>
            <tr>
              <th>#</th>
              <th>🚗 Гос. номер</th>
              <th>🔢 VIN</th>
              <th>🏭 Марка / Модель</th>
              <th>⭐ Категория</th>
              <th>👨‍💼 Водитель</th>
              <th>⚡ Действия</th>
            </tr>
          </thead>
          <tbody id="cars-body"></tbody>
        </table>
      </div>

      <div id="empty" class="empty-state" style="display:none;">
        <div class="empty-state-icon">🚫</div>
        <div>Автомобили не найдены</div>
        <small>Попробуйте изменить параметры поиска</small>
      </div>

      <div id="out" class="status-info"></div>

      <div class="pagination">
        <button id="pager-prev" class="btn btn-secondary">← Назад</button>
        <button id="pager-next" class="btn btn-secondary">Вперёд →</button>
        <button id="export-csv" class="btn btn-secondary">📊 Экспорт CSV</button>
        <button id="print-list" class="btn btn-secondary">🖨️ Печать</button>
        <button id="reset-bookings" class="btn btn-danger">🗑️ Сбросить брони</button>
      </div>

      <h2 class="section-title">📅 Брони выбранного пользователя</h2>
      <div class="table-container">
        <table id="bookings-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>📅 Период</th>
              <th>👤 Пользователь</th>
              <th>🚗 Автомобиль</th>
              <th>⭐ Категория</th>
              <th>👨‍💼 Водитель</th>
              <th>⚡ Действия</th>
            </tr>
          </thead>
          <tbody id="bookings-body"></tbody>
        </table>
      </div>
      
      <div id="no-bookings" class="empty-state" style="display:none;">
        <div class="empty-state-icon">📝</div>
        <div>У пользователя нет активных броней</div>
      </div>
    </div>
  </div>

  <!-- Модальное окно бронирования -->
  <div id="modal" class="modal" style="display:none;">
    <div class="modal-content">
      <h3 id="modal-title" class="modal-title">🚗 Подтверждение бронирования</h3>
      <div id="modal-body" class="modal-body">Вы уверены, что хотите забронировать автомобиль?</div>
      <div id="modal-msg" class="status-error" style="display:none;"></div>
      <div class="modal-actions">
        <button id="modal-cancel" class="btn btn-secondary">❌ Отмена</button>
        <button id="modal-confirm" class="btn btn-success">✅ Подтвердить</button>
        <button id="modal-close" class="btn btn-primary" style="display:none;">Закрыть</button>
      </div>
    </div>
  </div>

  <script>
    // Управление показом руководства
    document.getElementById('toggle-help').addEventListener('click', () => {
      const helpSection = document.getElementById('help-section');
      const toggleBtn = document.getElementById('toggle-help');
      
      if (helpSection.style.display === 'none') {
        helpSection.style.display = 'block';
        toggleBtn.textContent = '📚 Скрыть руководство';
        toggleBtn.className = 'btn btn-secondary';
      } else {
        helpSection.style.display = 'none';
        toggleBtn.textContent = '📚 Показать руководство';
        toggleBtn.className = 'btn btn-secondary';
      }
    });

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
        // Не показываем техническую информацию о токенах в интерфейсе
        return json;
      } catch (err) {
        console.error('getToken error', err);
        setOut('Выберите пользователя для работы с системой');
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
          <td>${car.driver.full_name} <br><small style="color:#666;">${car.driver.phone}</small></td>
          <td><button data-car-id="${car.id}" class="btn btn-success btn-book">🚗 Забронировать</button></td>
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
          setOut('Укажите время начала и окончания брони');
          return;
        }

  const tok = await getToken(userEmail);
  const token = tok.token;

        const qs = new URLSearchParams({ start, end });
  if (model) qs.set('model_id', model);
  if (category) qs.set('category_id', category);
  if (brand) qs.set('brand', brand);
  if (driver) qs.set('driver_id', driver);
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
  setOut('Найдено автомобилей: ' + (json.data ? json.data.length : 0));
  renderTable(json.data || []);
    
    // update pagination
    document.getElementById('pager-prev').disabled = (json.meta.current_page <= 1);
    document.getElementById('pager-next').disabled = (json.meta.current_page >= json.meta.last_page);
    currentPage = json.meta.current_page;
      } catch (err) {
        console.error(err);
        setOut('Необходимо выбрать пользователя и временной интервал');
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
            modalMsg.className = 'status-success';
            modalMsg.textContent = '✅ Бронь успешно создана: ID ' + (json.data?.id ?? '') + ' для пользователя ' + userEmail;
            document.getElementById('modal-confirm').style.display = 'none';
            document.getElementById('modal-close').style.display = 'inline-block';
        } else if (r.status === 422 && json.message && json.message.includes('занята')) {
            // Если время занято, пытаемся найти альтернативное время
            console.log('Время занято, ищем альтернативное...');
            modalMsg.style.display = 'block';
            modalMsg.className = 'status-info';
            modalMsg.textContent = '🔍 Выбранное время занято, ищем свободное время...';
            
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
                  modalMsg.className = 'status-success';
                  modalMsg.textContent = '✅ Найдено свободное время! Бронь создана: ID ' + altJson.data.id + ' на ' + alt.start + ' - ' + alt.end;
                  booked = true;
                  break;
                }
              } catch (altErr) {
                console.log('Alternative booking failed:', altErr);
              }
            }
            
            if (!booked) {
              modalMsg.className = 'status-error';
              modalMsg.textContent = '❌ Не удалось найти свободное время для этой машины. Попробуйте другую машину или время.';
            }
            
            document.getElementById('modal-confirm').style.display = 'none';
            document.getElementById('modal-close').style.display = 'inline-block';
          } else {
            modalMsg.style.display = 'block';
            modalMsg.className = 'status-error';
            let errorMsg = '❌ Ошибка бронирования';
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
        modalMsg.className = 'status-error';
        modalMsg.textContent = '❌ Не удалось создать бронь. Попробуйте другое время или машину.';
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
            <td><button data-booking-id="${b.id}" class="btn btn-danger btn-cancel">❌ Отменить</button></td>
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
        setOut('Выберите пользователя для просмотра броней');
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
        setOut('Не удалось отменить бронь');
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
        setOut('Не удалось выполнить операцию');
      }
    });
  </script>
</body>
</html>
