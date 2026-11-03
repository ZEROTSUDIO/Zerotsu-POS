# ZerotsuPOS — POS & Inventory Management System

<p align="center">
  <img src="public/assets/zero-studios-icon.svg" width="80" alt="Zerotsudios Logo" />
</p>

> A modern, technical Point-of-Sale (POS) & Inventory Management system designed for small cafés and retail businesses.
> Featuring **Double-Entry Inventory Movements**, **Midtrans Sandbox Payment Gateway**, **Webhook Idempotency**, and **Cashier Reconciliation**.

---

## ⚡ Tech Stack

- **Framework**: Laravel 12.0 (PHP 8.4+ / PHP 8.5)
- **Frontend**: Livewire 3.7 + Alpine.js + Tailwind CSS
- **Design System**: [Zerotsudios UI](d:\Wengdev\zerotsudios-ui) (Neobrutalist print & architectural dossier, 0-blur mechanical drop shadows)
- **Database**: MySQL 8.4 (Laragon)
- **Payment Gateway**: Midtrans Sandbox (Snap Popup & Core API)
- **Local Dev**: Laragon (`http://zerotsu-pos.test`)

---

## 🎯 Architecture Highlights

- **Pessimistic Row Locking (`SELECT ... FOR UPDATE`)**: Eliminates race conditions and prevents negative stock during concurrent checkouts.
- **Double-Entry Inventory Ledger (`inventory_movements`)**: Every stock change (opening balance, sale, refund, damage, adjustment) is tracked with before/after balances.
- **Webhook Security**: Authoritative payment confirmation via SHA-512 signature validation.
- **Idempotency Protection**: Safe against duplicated provider webhook deliveries.
- **Price Snapshots**: Historic order items capture product names and unit costs at time of sale.

---

## 📖 Project Plan & Roadmap

For the detailed architectural roadmap and implementation progress, see [PLAN.md](PLAN.md).

---

## 🚀 Quick Start (Laragon Environment)

```bash
# 1. Clone repository
git clone https://github.com/ZEROTSUDIO/Zerotsu-POS.git
cd Zerotsu-POS

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Migrate and Seed Café Demo Data
php artisan migrate:fresh --seed

# 4. Open in Browser
http://zerotsu-pos.test
```

---

## 📜 License

MIT License. Designed and developed by **Zerotsudios**.
