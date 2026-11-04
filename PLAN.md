# ZerotsuPOS — POS & Inventory Management System

> **A technical, production-grade POS system designed for small cafés and retail businesses.**
> Built with **Laravel 12**, **PHP 8.4/8.5**, **Livewire 3**, **MySQL 8.4 (Laragon)**, **Tailwind CSS + Zerotsudios UI**, and **Midtrans Sandbox Payment Gateway**.

---

## 🎯 0. Project Vision & Goals

ZerotsuPOS is built to prove real-world software engineering depth beyond simple CRUD:

1. **Transactional Database Integrity**: Atomic multi-table updates wrapped in `DB::transaction()` with pessimistic locking (`lockForUpdate()`) to prevent overselling and race conditions.
2. **Double-Entry Inventory Ledger**: Stock is never silently decremented without a verifiable audit entry in `inventory_movements` (tracking `OPENING_BALANCE`, `SALE`, `PURCHASE`, `REFUND`, `DAMAGE`, `ADJUSTMENT`).
3. **Decoupled Payment Domain**: Clear separation between `orders`, `payments`, and `payment_events` using clean abstraction (`PaymentGatewayInterface`).
4. **Midtrans Sandbox Integration**: Native Laravel HTTP Client implementation for Snap transactions, dynamic QRIS, and status verification—without outdated third-party SDKs.
5. **Webhook Security & Idempotency**:
   - Strict SHA-512 signature validation: `hash('sha512', order_id + status_code + gross_amount + server_key)`.
   - **Critical Rule**: Browser redirect $\neq$ payment confirmation. The server relies exclusively on verified provider webhooks.
   - Idempotent event handling: Duplicate webhooks will not double-deduct stock or double-count revenue.
6. **Cashier Shifts & Cash Reconciliation**: Track opening cash, cash sales, cash in/out, expenses, and calculate cash discrepancies (`expected_cash` vs `actual_cash`).
7. **COGS / HPP & Profit Analysis**: Product cost tracking (`cost_price` vs `selling_price`) snapshotted at time of sale.

---

## 💻 1. Technology Stack

- **Backend**: PHP 8.4+ / PHP 8.5, Laravel 12.0
- **Frontend**: Blade, Livewire 3.7, Alpine.js, Tailwind CSS
- **Design System**: **Zerotsudios UI** (Neobrutalist print & architectural dossier, 0-blur mechanical drop shadows, Midnight Ink `#0A090D`, Canvas Paper `#FFFFFF`, Electric Violet `#7C3AED`)
- **Database**: MySQL 8.4 (Laragon Environment)
- **Local Dev Server**: Laragon (Virtual Host: `http://zerotsu-pos.test`)
- **Payment Gateway**: Midtrans Sandbox (Snap Popup & Webhook Receiver)
- **Tunneling**: ngrok (`ngrok http 80`) or Laragon Share
- **Testing**: PHPUnit / Feature & Integration Tests

---

## 🏛️ 2. Core Architecture

```text
                        ┌────────────────────────────────────────┐
                        │          Browser Cashier UI            │
                        │   (Livewire 3 + Zerotsudios UI)        │
                        └───────────────────┬────────────────────┘
                                            │
                                            ▼
                        ┌────────────────────────────────────────┐
                        │             Laravel 12                 │
                        │                                        │
                        │  OrderService    InventoryService      │
                        │  PosTerminal     CashRegisterService   │
                        └─────────┬───────────────────┬──────────┘
                                  │                   │
                  ┌───────────────┴────────┐          │
                  ▼                        ▼          ▼
           ┌─────────────┐       ┌────────────────────────┐
           │  MySQL 8.4  │       │ PaymentGatewayInterface│
           │  Database   │       └───────────┬────────────┘
           └─────────────┘                   │
                                  ┌──────────┴──────────┐
                                  ▼                     ▼
                         ┌─────────────────┐   ┌─────────────────┐
                         │ MockGateway     │   │ MidtransGateway │
                         │ (Local Testing) │   │ (HTTP Client)   │
                         └─────────────────┘   └────────┬────────┘
                                                        │
                                                        ▼
                                               ┌─────────────────┐
                                               │ Midtrans Sandbox│
                                               └────────┬────────┘
                                                        │ Webhook (POST)
                                                        ▼
                                               ┌─────────────────┐
                                               │ Webhook Endpoint│
                                               │ SHA-512 Verified│
                                               └─────────────────┘
```

---

## 📦 3. Domain Model & Entities

### Backed Enums
- `OrderStatus`: `draft`, `pending_payment`, `paid`, `cancelled`, `refunded`, `partially_refunded`
- `PaymentStatus`: `pending`, `settlement`, `denied`, `expired`, `cancelled`, `refunded`
- `PaymentMethod`: `cash`, `midtrans_snap`, `midtrans_qris`
- `InventoryMovementType`: `opening_balance`, `sale`, `purchase`, `refund`, `damage`, `adjustment`

### Core Database Tables
1. **`categories`**: Product classifications (`name`, `slug`, `is_active`).
2. **`products`**: Product catalog (`sku`, `name`, `cost_price`, `selling_price`, `stock`, `minimum_stock`, `unit`).
3. **`orders`**: Sales transactions (`order_number`, `cashier_id`, `customer_name`, `subtotal`, `discount_amount`, `tax_amount`, `total_amount`, `status`).
4. **`order_items`**: Line item snapshots (`product_name`, `unit_cost`, `unit_price`, `quantity`, `subtotal`).
5. **`payments`**: Payment records (`payment_method`, `provider`, `amount`, `status`, `provider_transaction_id`, `snap_token`, `snap_redirect_url`, `paid_at`).
6. **`payment_events`**: Audit log of raw provider webhooks (`event_type`, `provider_event_id`, `payload`, `processed_at`).
7. **`inventory_movements`**: Immutable stock ledger (`type`, `quantity`, `stock_before`, `stock_after`, `reference_type`, `reference_id`, `note`).
8. **`cash_registers`** *(Phase 6)*: Cashier shift tracking (`opening_cash`, `expected_cash`, `actual_cash`, `difference`, `status`).

---

## 🗺️ 4. Roadmap & Milestones

### Phase 1: Foundation & Environment (COMPLETED ✅)
- [x] Setup Laragon vhost `http://zerotsu-pos.test` and MySQL database `zerotsu_pos`.
- [x] Migrate Laravel 12 base and install Livewire 3.
- [x] Configure domain Backed Enums (`OrderStatus`, `PaymentStatus`, `PaymentMethod`, `InventoryMovementType`).
- [x] Create core database migrations with foreign keys, indexes, and JSON columns.
- [x] Create Eloquent models with typed relationships, casts, and helper methods.
- [x] Seed realistic café data (`ZerotsuCafeSeeder`) with initial stock ledger records.
- [x] Bundle Zerotsudios UI design tokens and assets (`zerotsudios.css`, `zerotsudios.js`, SVG icons).

### Phase 2: Payment Abstraction & Midtrans Integration (COMPLETED ✅)
- [x] Create `PaymentGatewayInterface` contract and DTOs (`PaymentResultDTO`, `WebhookResultDTO`).
- [x] Implement `MockPaymentGateway` for rapid offline testing.
- [x] Implement `MidtransPaymentGateway` using native Laravel HTTP Client (no legacy SDK).
- [x] Implement SHA-512 webhook signature verification.
- [x] Build `InventoryService` with pessimistic locking (`lockForUpdate()`) and atomic stock deduction.
- [x] Build `OrderService` with price snapshotting and idempotency protection against duplicate webhooks.
- [x] Expose `POST /api/webhooks/midtrans` endpoint with CSRF exemption in `bootstrap/app.php`.

### Phase 3: Interactive Cashier POS Screen (COMPLETED ✅)
- **Phase 3.1: POS Layout Shell & Cashier HUD (COMPLETED ✅)**
  - [x] POS master layout (`resources/views/components/layouts/pos.blade.php`) with Zerotsudios UI tokens & Snap.js.
  - [x] Cashier HUD strip: Live clock, cashier profile, shift badge, telemetry indicators (`Space Mono`).
  - [x] Route `GET /pos` linked to `PosTerminal` component.
- **Phase 3.2: Product Grid & Real-Time Filtering Component (COMPLETED ✅)**
  - [x] Category navigation rail / filter pills with high-contrast active state.
  - [x] Instant search engine with debounce (`wire:model.live.debounce.250ms`).
  - [x] Neobrutalist product cards (`.card-project` / `.card-tilty`) with live stock chips and sold-out states.
- **Phase 3.3: Reactive Cart & Financial Calculation Engine (COMPLETED ✅)**
  - [x] Cart state management with real-time stock ceiling validation.
  - [x] Financial calculator: Subtotal, Tax (PB1 10% toggleable / enabled by default), Discount, Grand Total (`Anton`).
  - [x] Line items manager: quantity stepper (+, -), trash removal, and clear cart action.
- **Phase 3.4: Checkout Dossier & Cash Payment Flow (COMPLETED ✅)**
  - [x] Neobrutalist checkout modal dossier with customer name & table notes.
  - [x] Cash tender chips (`Pas`, `50k`, `100k`, `200k`, `+10k`, `+20k`, `+50k`) and live change calculation.
  - [x] Atomic cash settlement via `OrderService` + stock deduction via `InventoryService`.
  - [x] Printable transaction receipt dossier modal with thermal printer support.
- **Phase 3.5: Midtrans Snap Integration & Client-Side Flow (COMPLETED ✅)**
  - [x] Midtrans Snap payment method in checkout modal.
  - [x] Event dispatching and `window.snap.pay(snapToken)` integration with graceful sandbox simulator fallback.
  - [x] Callback lifecycle management (`onSuccess`, `onPending`, `onError`, `onClose`).
- **Phase 3.6: Live Payment Polling & Transaction Receipt Modal (COMPLETED ✅)**
  - [x] Real-time status polling (`wire:poll.2000ms="checkPendingPaymentStatus"`) on `PENDING_PAYMENT`.
  - [x] Transaction receipt dossier popup with café branding, itemized summary, and payment details.
  - [x] Thermal POS printer stylesheet (`@media print` for 58mm/80mm receipt paper).
- **Phase 3.7: In-App Webhook Simulator & Developer Testing Toolbar (COMPLETED ✅)**
  - [x] Floating developer toolbar for instant local webhook simulation (Settlement, Expire, Cancel).
  - [x] Verification of SHA-512 validation, stock deduction, and duplicate event idempotency.

### Phase 4: Webhook Tunneling & Real Sandbox Testing
- [ ] Expose local webhook via ngrok: `ngrok http 80`.
- [ ] Configure Notification URL in Midtrans Sandbox Dashboard: `https://<ngrok-id>.ngrok-free.app/api/webhooks/midtrans`.
- [ ] Perform real test transactions using Midtrans Simulator (GoPay, QRIS, Virtual Account BCA/Mandiri).
- [ ] Verify database updates: `payments.status = SETTLEMENT`, `orders.status = PAID`, `inventory_movements` recorded.
- [ ] Test edge cases: Expired QRIS, customer cancel, duplicate webhook callback.

### Phase 5: Cashier Shifts & Reconciliation
- [ ] Open shift drawer modal with initial opening cash.
- [ ] Cash in / Cash out movements (petty cash, operational expenses).
- [ ] Close shift modal: enter physical cash count, compute expected vs actual difference.
- [ ] End-of-shift summary report.

### Phase 6: Refunds & Stock Restoration
- [ ] Full and partial refund requests.
- [ ] Update payment & order status to `REFUNDED`.
- [ ] Automatically restore product stock via `InventoryMovement` (type: `REFUND`).
- [ ] Prevent refunds exceeding original payment amount.

### Phase 7: Reporting, COGS & Polishing
- [ ] Daily sales & gross profit dashboard (Revenue - COGS = Gross Margin).
- [ ] Printable thermal receipt layout (with monospace font and clean borders).
- [ ] Automated feature tests covering order lifecycle, webhook idempotency, and stock consistency.

---

## 🔒 5. Essential Security & Idempotency Rules

1. **Authoritative Webhooks**: Status is NEVER marked as `PAID` merely from front-end redirect URLs.
2. **Signature Verification**: Every incoming webhook must have its SHA-512 signature verified against `MIDTRANS_SERVER_KEY`.
3. **Idempotent Webhook Processing**:
   ```php
   if ($payment->status === PaymentStatus::SETTLEMENT) {
       // Log duplicate event and safely exit with 200 OK
       return true;
   }
   ```
4. **Pessimistic Locking**:
   ```php
   $product = Product::where('id', $itemId)->lockForUpdate()->first();
   ```
   Prevents race conditions where simultaneous checkouts result in negative stock.
5. **Credential Safety**: `MIDTRANS_SERVER_KEY` and `MIDTRANS_CLIENT_KEY` are stored strictly in `.env` and never committed to source control.

---

## 🚀 6. Local Setup Instructions

```bash
# 1. Clone repository
git clone https://github.com/ZEROTSUDIO/Zerotsu-POS.git
cd Zerotsu-POS

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Configure MySQL in .env (Laragon default)
DB_DATABASE=zerotsu_pos
DB_USERNAME=root
DB_PASSWORD=

# 4. Configure Midtrans Sandbox Keys in .env
MIDTRANS_SERVER_KEY=your-midtrans-server-key
MIDTRANS_CLIENT_KEY=your-midtrans-client-key

# 5. Run migrations & seed data
php artisan migrate:fresh --seed

# 6. Run local server / access via Laragon virtual host
http://zerotsu-pos.test
```
