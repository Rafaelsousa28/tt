# Verde Vivo — Plataforma E-commerce de Mudas & Plantas

## Pré-requisitos
- PHP 8.1+
- Composer
- Node.js 18+
- MySQL 8+

## Instalação

```bash
# 1. Clone e entre na pasta
git clone <repo> && cd tt

# 2. Instalar dependências PHP
composer install

# 3. Instalar dependências JS
npm install

# 4. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 5. Configurar .env
# DB_DATABASE=verde_vivo
# DB_USERNAME=root
# DB_PASSWORD=sua_senha
# MP_ACCESS_TOKEN=TEST-xxxxxxxx   ← Mercado Pago Sandbox
# MP_PUBLIC_KEY=TEST-xxxxxxxx

# 6. Criar banco e rodar migrações
php artisan migrate --seed

# 7. Storage link
php artisan storage:link

# 8. Build assets
npm run build   # produção
npm run dev     # desenvolvimento

# 9. Iniciar servidor
php artisan serve
```

## Acesso

| URL             | Descrição             |
|-----------------|-----------------------|
| http://localhost | Loja virtual (Vue.js) |
| http://localhost/admin | Painel Admin (Filament) |

### Login Admin padrão
- **E-mail:** admin@verdevivo.com.br
- **Senha:** admin@123

## Mercado Pago
1. Acesse https://www.mercadopago.com.br/developers
2. Crie uma aplicação
3. Copie o **Access Token** (sandbox) para `MP_ACCESS_TOKEN`
4. Copie a **Public Key** para `MP_PUBLIC_KEY`
5. Configure a URL de webhook: `{APP_URL}/api/payments/webhook`

## Estrutura do Projeto

```
app/
  Filament/Resources/    ← Admin panel (Filament 3)
  Http/Controllers/Api/  ← REST API
  Models/                ← Eloquent models
  Services/              ← MercadoPagoService
database/
  migrations/            ← Schema do banco
  seeders/               ← Dados iniciais
resources/
  js/
    pages/               ← Vue.js pages (SPA)
    components/          ← Componentes reutilizáveis
    stores/              ← Pinia (cart, ui)
    router/              ← Vue Router
  css/app.css            ← Tailwind + design tokens
routes/
  api.php                ← Rotas da API
  web.php                ← SPA catch-all
```
