#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import re, os, sys

# 1. Настройки
PROJECT_ROOT = os.path.abspath(os.path.dirname(__file__))
ROUTES_DIR   = os.path.join(PROJECT_ROOT, 'extracted_files', 'routes')
CONTROLLERS_DIR = os.path.join(PROJECT_ROOT, 'extracted_files', 'app', 'Http', 'Controllers')
VIEWS_DIR    = os.path.join(PROJECT_ROOT, 'extracted_files', 'resources', 'views')

# 2. Шаблоны для поиска маршрутов
route_pattern = re.compile(
    r"Route::(?:get|post|put|patch|delete|match|resource)\s*\(\s*"
    r"(?:\[(.+?)\]\s*,\s*)?    # для match: список методов"
    r"['\"](.+?)['\"]\s*,\s*"  # URL
    r"(?:['\"](.+?)['\"]|\s*function)",  # контроллер@метод или function
    re.IGNORECASE
)

# 3. Функции валидации
def check_view(view_name):
    # допускаем как "folder.name" → folder/name.blade.php
    path = os.path.join(VIEWS_DIR, *view_name.split('.')) + '.blade.php'
    return os.path.isfile(path), path

def check_controller(action):
    # action = "SomeController@method" или "Namespace\\SomeController@method"
    parts = action.split('@')
    if len(parts)!=2: return False, None, None
    ctrl, method = parts
    file_path = os.path.join(CONTROLLERS_DIR, *ctrl.split('\\')) + '.php'
    if not os.path.isfile(file_path):
        return False, file_path, None
    # ищем метод в файле
    text = open(file_path, encoding='utf-8', errors='ignore').read()
    m = re.search(r"function\s+" + re.escape(method) + r"\s*\(", text)
    return bool(m), file_path, method

# 4. Собираем маршруты
results = []
for fname in os.listdir(ROUTES_DIR):
    if not fname.endswith('.php'): continue
    full = os.path.join(ROUTES_DIR, fname)
    text = open(full, encoding='utf-8', errors='ignore').read()
    for m in route_pattern.finditer(text):
        methods, url, action = m.groups()
        http = methods or 'standard'
        target = 'Closure' if action is None else action
        status = 'OK'
        detail = ''
        # если Closure — ищем внутри function вызов view('name')
        if action is None:
            # простой regex view('...')
            vm = re.search(r"view\s*\(\s*['\"](.+?)['\"]", m.string[m.start():m.end()+200])
            if vm:
                ok, path = check_view(vm.group(1))
                if not ok:
                    status = 'ERR'
                    detail = f"Не найден шаблон {path}"
            else:
                status = 'WARN'
                detail = "Closure без явного view()"
        else:
            ok, path, method = check_controller(action)
            if not ok:
                status = 'ERR'
                if method is None:
                    detail = f"Контроллер или файл не найден: {path}"
                else:
                    detail = f"Метод {method} не найден в {path}"
        results.append((fname, http, url, target, status, detail))

# 5. Вывод отчёта
print("Файл,Метод,URL,Действие,Статус,Примечание")
for rec in results:
    print(','.join(rec))
