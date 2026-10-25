# WONDR — platform top-up digital

Aplikasi Laravel baru untuk storefront katalog game, checkout sederhana, order tracking, dan sandbox lokal. Repo ini dibuat independen di `wondr-rebuild`; aplikasi lama `wondr` tidak menjadi sumber file aplikasi.

## Status saat ini

**Local development prototype.** Katalog dan order tersimpan di database. Harga order dihitung server-side dari produk aktif; sandbox dibatasi ke `local`/`testing` dan hanya mengubah order menjadi `sandbox_paid` + `manual_review`. Tidak ada pembayaran nyata atau top-up yang dikirim.

| Bagian | Status |
|---|---|
| UI storefront + katalog database | Implemented; tests/build pass |
| Autentikasi Laravel Breeze | Scaffold tersedia; diuji suite |
| Checkout dan tracking token referensi acak | Implemented; tests pass |
| Sandbox pembayaran | Simulasi lokal saja; bukan konfirmasi provider |
| Webhook Midtrans/Digiflazz | Endpoint placeholder; menolak transaksi |
| Fulfillment provider | Belum diimplementasikan |
| Admin dashboard/role & permission | Belum diimplementasikan |
| PostgreSQL + Redis/Docker | Belum disiapkan atau diuji; local SQLite |

## Prasyarat

PHP 8.3+, Composer, Node.js/npm. SQLite cukup untuk local development. Versi PHP yang teruji di mesin pembuat: 8.5.0.

## Setup

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install --ignore-scripts
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`. Gunakan `.env` lokal; jangan commit. Jika `.env` sudah ada, perbarui konfigurasi tanpa menimpa rahasia. `WONDR_SANDBOX_ENABLED` default `false`; untuk sandbox lokal aktifkan hanya dengan `true` di `.env`. Kode tetap membatasi aksi ke `local`/`testing`.

## Pengujian

```sh
php artisan test --compact
npm run build
php artisan migrate:fresh --seed --force
```

`migrate:fresh` menghapus semua tabel dalam database yang dipilih; jalankan hanya pada database development/test yang boleh direset.

## Pemetaan teknis

- Laravel + Inertia React + TypeScript + Tailwind.
- Model: `Game`, `Product`, `Order`.
- Migration: katalog dan order.
- Seeder katalog berisi data contoh, produk nonaktif untuk mencegah anggapan stok/provider nyata.
- Webhook placeholder: `POST /webhooks/payments/{provider}`, `POST /webhooks/suppliers/{provider}`.

## Syarat sebelum operasi produksi

1. Gunakan PostgreSQL/Redis yang disiapkan dan diverifikasi, plus Docker/deployment hardening.
2. Implementasikan adapter payment berdasarkan dokumentasi provider terbaru; validasi signature, idempotensi, status API, expiry, reconciliation, dan tes sandbox end-to-end.
3. Implementasikan adapter supplier resmi; timeout harus masuk reconciliation/manual review, bukan blind retry.
4. Implementasikan role/permission admin server-side, audit trail, produk/price management, observability, backup, dan recovery.
5. Siapkan merchant/provider accounts, secrets via secret manager/environment, domain HTTPS, email service, monitoring.
6. Review privasi, refund, harga, pajak, dan ketentuan provider/hukum Indonesia.

Jangan mengaktifkan kredensial produksi atau menerima dana sampai transaksi provider sandbox diverifikasi dan kontrol finansial siap. Placeholder webhook saat ini bukan integrasi.

---

## Framework reference

Laravel framework reference is available at https://laravel.com/docs.

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
