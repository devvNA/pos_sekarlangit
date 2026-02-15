# Product Requirements Document (PRD): POS Sekarlangit

**Version:** 1.0  
**Status:** Final Draft  
**Author:** Devna.PRD

---

## 1. Executive Summary (One Pager)

**Product Overview:** A lightweight, web-based Point of Sale (POS) system tailored for "Toko Sekarlangit" in Karanggintung. The system focuses on inventory accuracy, barcode-integrated sales, and debt management.

**Problem:** Manual recording leads to stock discrepancies, untracked debts (kasbon), and slow service.

**Objectives:** \* Zero stock discrepancy through real-time tracking.

- Faster checkout using EAN-13 barcode scanning.
- Automated financial and debt reporting.

**Persona:** Single Owner/Cashier (Requires simple, high-contrast UI in Bahasa Indonesia).

---

## 2. Features

### 2.1 Features In (Priority)

- **Dashboard:** Summary of today's sales, total cash on hand, and low-stock alerts.
- **Inventory Management:** \* Product master data with EAN-13 Barcode support.
    - Supplier database.
    - Stock-in/Purchasing module to update inventory.
- **Point of Sale (POS):**
    - Barcode scanner integration for quick item entry.
    - Manual payment method selection (Cash / Debit Card / Credit Card).
    - Receipt printing support for thermal printers.
- **Debt Tracking (Buku Piutang):** Record customer names, debt amounts, and mark as "Paid".
- **Financial Records (Buku Kas):** Record operational expenses and income.
- **Automated Reporting:** \* Daily/Monthly Sales Reports.
    - Inventory/Stock Movement Reports.
    - Profit/Loss summary based on COGS (HPP).

### 2.2 Features Out (Post-MVP)

- **Multi-User/Employee Roles:** System only supports 1 admin (owner).
- **Digital Payment Integration:** No automatic QRIS/E-Wallet API (Manual recording only).
- **Advanced Analytics:** Predictive restock or customer behavior analysis.

---

## 3. User Experience & Design

- **Localization:** The entire application interface must use **100% Bahasa Indonesia**.
- **UI Style:** High contrast, large buttons (touch-friendly), and optimized for low-resolution laptop screens.
- **Hardware Compatibility:** \* Supports USB/Bluetooth Barcode Scanners (EAN-13).
    - Supports 58mm/80mm Thermal Receipt Printers.

---

## 4. Success Metrics

- **Stock Accuracy:** 0% variance between system stock and physical stock during monthly audits.
- **Transaction Speed:** Average checkout time under 45 seconds using barcode scanning.
- **Reporting Efficiency:** 100% automated monthly reports (Zero manual calculation needed).

---

## 5. Technical Considerations

- **Platform:** Responsive Web Application (Optimized for Chrome/Edge on low-spec Windows/Linux).
- **Database:** Lightweight database (e.g., SQLite or MySQL) for fast query on low-spec hardware.
- **Print Function:** Native browser print or specialized printing library for thermal alignment.

---

## 6. GTM & Phasing

- **Phase 1 (Days 1-3):** Core Development (Database, POS, Barcode Scan).
- **Phase 2 (Days 4-5):** Inventory & Reporting Setup.
- **Phase 3 (Day 6):** Data Migration (Inputting existing stock into the system).
- **Phase 4 (Day 7):** Live Go-To-Market at Toko Sekarlangit.

---

## 7. Open Issues

- **Offline Capability:** Will the app still work if the internet in Karanggintung goes down? (Recommended: Localhost/Offline-first approach).
