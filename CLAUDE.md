# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

- **Framework:** CodeIgniter 3 (PHP 7.4)
- **Database:** MariaDB 10.4 local (`fhbpos`) / MySQL 8.0 production (`fhbrestaurant`)
- **Web server:** Apache via XAMPP on Windows (local), IIS on Windows Server 2019 (production)
- **Root:** `C:\xampp\htdocs\fhbrestaurant\web\html\` — all CodeIgniter files live here

## Running locally

Open `http://localhost/fhbrestaurant/web/html/` in a browser. No build step. XAMPP must have Apache and MySQL running.

Database connection is in `html/application/config/database.php` — currently points to `fhbpos` on `localhost` with no password.

## Database compatibility note

Production uses MySQL 8.0 (`utf8mb4_0900_ai_ci`). Local MariaDB 10.4 does not support that collation. When importing a production dump, replace all occurrences of `utf8mb4_0900_ai_ci` with `utf8mb4_general_ci` before importing.

## Architecture

Standard CodeIgniter 3 MVC. Entry point is `html/index.php`. All routes are defined in `html/application/config/routes.php`.

**Core helper functions** (loaded globally, defined in `html/application/helpers/core_helper.php`):
- `TraerUnDato($tabla, $condicion)` — fetch single row
- `TraerDatos($tabla, $condicion, $orden)` — fetch multiple rows
- `GuardarDatos($tabla, $datos)` — insert, returns new ID
- `EditarDatos($tabla, $datos, $condicion)` — update
- `GblPlantilla($vista, $datos, $extras, $titulo)` — renders a view inside the POS layout (admin panel only)
- `EnviarCorreoConConfig($prefijo, $destinatarios, $asunto, $html)` — email via SMTP/SendGrid using config from DB

**Session keys:**
- POS staff: `existeSesion`, `idUsuario`, `admin`, `superAdmin`
- Online customers: `online_cliente`, `online_carrito`, `online_ultimo_pedido`, `online_otp_pending`

## Online ordering system (current — embedded in POS)

Everything lives inside the single CodeIgniter app:

| Layer | Files |
|---|---|
| Public storefront | `controllers/Online.php` → routes under `/pedidos/*` |
| POS admin panel | `controllers/AdminOnline.php` → routes under `/AdminOnline/*` |
| Models | `models/Online_model.php`, `models/AdminOnline_model.php` |
| Customer views | `views/online/` (login, registro, verificar, menu_online, checkout, confirmacion, perfil, mi_cuenta, recuperar, terminos) |
| Admin views | `views/adminonline/` (ordenes, clientes, zonas, configuracion, productosMenu) |
| WhatsApp | `helpers/Whatsapp_helper.php` — sends via Fonnte API (token: `WA_API_KEY_REST` in DB) |
| Horario | `helpers/Horario_helper.php` — reads `HORARIO_DOM_JUE` / `HORARIO_VIE_SAB` from DB |

**Order flow:** Customer registers → OTP via WhatsApp → adds to cart (session) → checkout with Google Maps zone validation → `Online_model::CrearPedidoOnline()` inserts into `pedido` + `pedidoDetalle` + `pedidoonline` → WhatsApp notifications sent to both customer and restaurant → POS cocina screen polls `/Online/VerificarOnlinePendientes` every few seconds.

**Delivery zone check:** `EncontrarZonaDelivery($lat, $lng)` (in `core_helper.php`) checks coordinates against polygons stored as JSON in the `zonadelivery` table.

**Config keys in DB** (`configuraciones` table, `parametroConfiguracion` column):
- `WA_API_KEY_REST`, `WA_NUMERO_REST`, `WA_MODO_PRUEBA`
- `GOOGLE_MAPS_API_KEY`, `RESTAURANTE_LAT`, `RESTAURANTE_LNG`
- `HORARIO_DOM_JUE`, `HORARIO_VIE_SAB`
- `EMAIL_*`, `NOTIF_EMAIL_*` — email providers (SendGrid or SMTP)
- `PAGO_BANCO_*`, `PAGO_CHIVO_*` — payment info shown at checkout
- `ORION_API_URL`, `ORION_API_KEY` — Orion delivery dispatch integration

**Products visible online:** controlled by `visibleOnlineProducto` column (`'Si'`/`'No'`) in the `producto` table. Managed from `AdminOnline/productosMenu`.

**Order states** (`estadoOnline`): `Recibido → EnPreparacion → Listo → EnCamino → Entregado | Cancelado`

## Key DB tables for online orders

- `pedidoonline` — online metadata (tracking code, type, payment method, lat/lng, zone, WA flags)
- `pedido` — the actual POS order (shared with in-house orders; online ones are prefixed `[WEB]` in `nombreClientePedido`)
- `pedidoDetalle` — line items
- `clienteacceso` — web login credentials (email + bcrypt password + OTP fields)
- `cliente` — shared customer record between POS and online
- `zonadelivery` — delivery zone polygons (JSON) with color per zone
