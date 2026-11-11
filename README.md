# Test Data Generator for Quality Control (QC) / Quality Assurance (QA)

A Laravel application built for QC/QA teams to automatically generate realistic dummy test data matching predefined Excel templates for data import testing.

---

## 🚀 Features

1. **3 Template Generators**:
   - **Data Pegawai/Guru**: 51 columns matching `sample_import_pegawai.xlsx`
   - **Data Murid/Dapodik**: 72 columns matching `format sidigs murid-dapodik (1).xlsx` (with grouped sub-headers for Data Ayah/Ibu/Wali)
   - **Data Tagihan**: 12 columns matching `template impor tagihan.xlsx` (with conditional `Nama POS` logic)
2. **Stateless Generation**: Data is generated in-memory and previewed/exported without polluting the production database.
3. **Data Sanitization & CSV Injection Mitigation**: Export layer automatically prefixes dangerous formula characters (`=`, `+`, `-`, `@`, `\t`, `\r`, `\n`) with `'` (single quote) during export.
4. **Indonesian Datasets & Identifiers**: Purely synthetic identifiers (NIK, NUPTK, NIP, NISN, NPWP, VA, Bank Accounts, No KK) generated locally using Faker `id_ID` and custom Indonesian data providers.
5. **Interactive UI**: Minimalist, modern dark glassmorphism dashboard built with Livewire and Tailwind CSS.
6. **Multi-Format Export**: Full support for CSV (UTF-8 with BOM) and Excel (XLSX).

---

## 🛠️ Requirements & Tech Stack

- **PHP**: 8.2+ (tested on PHP 8.5)
- **Framework**: Laravel 13 / 12
- **Front-end**: Livewire v3/v4, Blade, Tailwind CSS, Vite
- **Data & Export**: FakerPHP (`id_ID`), Maatwebsite/Excel (`phpoffice/phpspreadsheet`)
- **Database**: SQLite (for user authentication)

---

## 📥 Installation

```bash
# Clone or open project directory
cd "d:\Project\Data Generator"

# Install dependencies (if needed)
composer install
npm install

# Setup environment file
cp .env.example .env

# Run database migrations and seed default admin
php artisan migrate --seed

# Build front-end assets
npm run build
```

---

## 🔑 Default Login Credentials

Seeded automatically by `DatabaseSeeder`:

- **Email**: `admin@example.com`
- **Password**: `password`

---

## 🏃 Running the Application

Start the local Laravel development server:

```bash
php artisan serve
```

Access the application in your web browser at:  
👉 **`http://127.0.0.1:8000`**

---

## 🧪 Running Automated Tests

The application includes automated unit and feature test coverage:

```bash
php artisan test
```

### Test Coverage Includes:
- **Unit Tests**: `DataSanitizerTest`, `IdentifierGeneratorTest`, `GeneratorsTest`
- **Feature Tests**: `GenerateDataTest` (validation rules, quantity bounds 1-500, invalid category), `ExportTest` (CSV & XLSX exports, security authorization, session handling)

---

## 📖 How to Add a New Template Generator

The system uses an extensible Service/Strategy architecture (`AbstractGenerator`):

1. **Add Schema Definition** in `app/Support/TemplateSchema.php`:
   Define the columns array, exact header strings, data types, and enums.
2. **Create Generator Class** in `app/Services/Generators/YourNewGenerator.php`:
   Extend `AbstractGenerator` and implement `generateRow(int $index, GenerateMode $mode): array`.
3. **Register Generator** in `app/Services/TestDataGeneratorService.php`:
   Add your category key to `resolveGenerator()`.
4. **Update UI** in `app/Livewire/TestDataGenerator.php` & `resources/views/livewire/test-data-generator.blade.php`:
   Add a new template card in the selector.

---

## 🔒 Security Controls

- **CSRF Protection**: Native Laravel CSRF tokens on all requests.
- **XSS Prevention**: Automatic Blade escaping on preview tables.
- **CSV Injection Prevention**: Applied automatically at export stream layer via `DataSanitizer`.
- **Rate Limiting**: `throttle:export` (15 req/min) and `throttle:generate` (20 req/min).
- **Authentication**: Authorization enforced on generator and export routes.
