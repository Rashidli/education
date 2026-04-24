# Education ERP

Təhsil mərkəzləri üçün tələbə, müəllim, qrup və ödəniş idarəetmə sistemi.

## Əsas xüsusiyyətlər

- **Tələbə / Müəllim / Qrup / Enrollment / Ödəniş** — tam CRUD
- **Pro-rata hesablama** — tələbə ay ortasında qoşulduqda qalan günlər üzrə avtomatik
- **Avtomatik növbəti ödəniş tarixi** — `paid_at + 30 gün` (parametr)
- **Müəllim komissiyası** — yalnız real daxil olmuş ödənişlərdən, tip üzrə fərqli faiz
- **İki növ müəllim** — Yerli və Xarici, default qiymət/komissiya sistemdən
- **Yaxınlaşan ödənişlər** — 5 gün pəncərəsi (parametr), dashboard və CLI command
- **Hesabatlar** — aylıq maliyyə, müəllim qazancı, tələbə ödənişləri (CSV + PDF export)
- **Rol əsaslı icazə** — Spatie Laravel Permission (Super Admin, Admin, Manager)
- **Soft delete + Zibil qutusu** — heç bir data itmir, Super Admin bərpa edə bilir
- **Bildiriş kanal arxitekturası** — Database kanalı işləyir, SMS/WhatsApp stub hazır

## Tələblər

- PHP 8.2+ (Laravel 13)
- MySQL 5.7+ / 8.0+
- Composer 2.x
- Node.js (yalnız Vite istəsəniz — custom CSS ilə işləyirik, lazım deyil)

## Quraşdırma

```bash
git clone https://github.com/Rashidli/education.git
cd education

composer install
cp .env.example .env
php artisan key:generate
```

`.env` faylında DB parametrləri:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=education
DB_USERNAME=root
DB_PASSWORD=
```

MySQL-də `education` bazasını yaradın, sonra:

```bash
php artisan migrate --seed
```

Seeder **yalnız** aşağıdakıları yaradır:
- 3 rol (Super Admin, Admin, Manager) və 15 icazə
- 6 default parametr (qiymət, komissiya, cycle gün sayı)
- 3 admin user

## Default istifadəçilər

| Rol | Email | Şifrə |
|---|---|---|
| Super Admin | `superadmin@education.az` | `super123` |
| Admin | `admin@education.az` | `admin123` |
| Manager | `manager@education.az` | `manager123` |

**Production-a deploy edərkən mütləq bu şifrələri dəyişin.**

## Demo data (istəyə bağlı)

Dev/staging mühitdə test üçün:

```bash
php artisan db:seed --class=DemoSeeder
```

Bu 5 müəllim, 8 qrup, 25 tələbə, ~80 ödəniş yaradır.

## Avtomatik bildirişlər

Yaxınlaşan ödənişlər üçün:

```bash
php artisan payments:notify-upcoming
```

Cron-da işləsin deyə server-də:

```cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

`routes/console.php`-də hər gün saat 09:00-da avtomatik işləyir.

## Arxitektura

```
app/
├── Http/Controllers/     # Resource controller-lər
├── Http/Requests/        # FormRequest validation
├── Models/               # Eloquent + SoftDeletes + TracksDeletedBy
│   └── Concerns/         # Reusable trait-lər
├── Services/             # Biznes məntiqi
│   ├── ProRataCalculator
│   ├── EnrollmentService
│   ├── PaymentService
│   ├── EarningsService
│   ├── ReportService
│   ├── DashboardService
│   ├── SettingsService
│   └── NotificationService
├── Notifications/Channels/  # NotificationChannel interface + DB/SMS/WhatsApp
└── Console/Commands/     # payments:notify-upcoming
```

## İcazə sistemi

- `Gate::before` — Super Admin bütün icazələri avtomatik alır
- Controller-lər və sidebar `@can` / `@role` ilə qorunur
- Route middleware `can:permission.name` və `role:Super Admin`

## Texnologiyalar

- Laravel 13
- MySQL
- Spatie Laravel Permission
- Barryvdh DomPDF
- Saf HTML / Blade / Custom CSS (Tailwind, React, Vue yoxdur)

## Lisenziya

MIT
