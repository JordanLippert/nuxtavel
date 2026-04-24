#!/bin/sh
set -e

echo "→ Rodando migrations..."
php artisan migrate --force

echo "→ Criando symlink de storage..."
php artisan storage:link --quiet 2>/dev/null || true

echo "→ Cacheando config e rotas..."
php artisan config:cache
php artisan route:cache

echo "→ Iniciando servidor na porta ${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
