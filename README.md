# Website KKN Kelompok - Dokumentasi Setup

## Daftar Isi
1. [Instalasi Awal](#instalasi-awal)
2. [Konfigurasi Firebase](#konfigurasi-firebase)
3. [Setup Database Firestore](#setup-database-firestore)
4. [Menjalankan Aplikasi](#menjalankan-aplikasi)
5. [Struktur Proyek](#struktur-proyek)
6. [Fitur Utama](#fitur-utama)

## Instalasi Awal

### Prasyarat
- PHP 8.3+
- Composer
- Node.js & npm
- Browser modern

### Langkah-langkah

1. **Clone Repository** (jika menggunakan Git)
   ```bash
   git clone <repository-url>
   cd web\ kkn
   ```

2. **Install Dependencies PHP**
   ```bash
   composer install
   ```

3. **Install Dependencies JavaScript**
   ```bash
   npm install
   ```

4. **Setup Environment File**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

## Konfigurasi Firebase

### 1. Dapatkan Credentials Firebase

1. Buka [Firebase Console](https://console.firebase.google.com/)
2. Pilih project `website-kkn078`
3. Buka **Project Settings** → **Service Accounts**
4. Klik **Generate New Private Key**
5. File JSON akan diunduh

### 2. Setup File Credentials

1. Pindahkan file JSON yang diunduh ke folder `storage/app/`
2. Rename menjadi `firebase-credentials.json`
3. Update file `.env`:

```env
FIREBASE_PROJECT_ID=website-kkn078
FIREBASE_AUTH_DOMAIN=website-kkn078.firebaseapp.com
FIREBASE_STORAGE_BUCKET=website-kkn078.firebasestorage.app
FIREBASE_CREDENTIALS=storage/app/firebase-credentials.json
FIRESTORE_ENABLED=true
STORAGE_ENABLED=true
AUTH_ENABLED=true
```

## Setup Database Firestore

### Struktur Collections yang Diperlukan

Buat collections di Firestore dengan struktur berikut:

#### 1. `articles` Collection
Document fields:
- `title` (string)
- `slug` (string)
- `content` (string)
- `thumbnail_url` (string)
- `category` (string)
- `author` (string)
- `published_at` (timestamp)
- `gallery` (array)

#### 2. `work_programs` Collection
Document fields:
- `title` (string)
- `description` (string)
- `objective` (string)
- `status` (string: planned, ongoing, completed)
- `start_date` (timestamp)
- `end_date` (timestamp)
- `gallery` (array)

#### 3. `galleries` Collection
Document fields:
- `title` (string)
- `description` (string)
- `category` (string)
- `image_url` (string)
- `created_at` (timestamp)

#### 4. `timelines` Collection
Document fields:
- `title` (string)
- `description` (string)
- `date` (timestamp)
- `icon` (string)
- `color` (string)

#### 5. `members` Collection
Document fields:
- `name` (string)
- `nim` (string)
- `prodi` (string)
- `position` (string)
- `photo_url` (string)
- `social_media` (map)

#### 6. `village_profile` Document
Buat dengan ID: `main`
Fields:
- `name` (string)
- `address` (string)
- `district` (string)
- `regency` (string)
- `province` (string)
- `history` (string)
- `philosophy` (string)
- `demographics` (string)
- `potential` (string)

#### 7. `group_profile` Document
Buat dengan ID: `main`
Fields:
- `name` (string)
- `location` (string)
- `period` (string)
- `university` (string)
- `description` (string)
- `logo_url` (string)
- `photo_url` (string)

#### 8. `contact` Document
Buat dengan ID: `main`
Fields:
- `address` (string)
- `phone` (string)
- `email` (string)
- `instagram` (string)
- `tiktok` (string)
- `whatsapp` (string)
- `map_url` (string)

## Menjalankan Aplikasi

### Development Mode

1. **Build Assets**
   ```bash
   npm run dev
   ```

2. **Jalankan Server Laravel** (terminal baru)
   ```bash
   php artisan serve
   ```

3. Buka browser: `http://localhost:8000`

### Production Mode

```bash
npm run build
php artisan config:cache
php artisan route:cache
```

## Struktur Proyek

```
web kkn/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── PublicController.php
│   │   │   ├── AdminController.php
│   │   │   ├── ArticleController.php
│   │   │   ├── WorkProgramController.php
│   │   │   ├── GalleryController.php
│   │   │   ├── TimelineController.php
│   │   │   ├── VillageProfileController.php
│   │   │   ├── GroupProfileController.php
│   │   │   └── ContactController.php
│   │   ├── Middleware/
│   │   │   └── FirebaseAuthenticate.php
│   ├── Facades/
│   │   └── Firebase.php
│   ├── Models/
│   │   ├── Article.php
│   │   ├── Member.php
│   │   ├── WorkProgram.php
│   │   └── GroupProfile.php
│   ├── Providers/
│   │   └── FirebaseServiceProvider.php
│   ├── Services/
│   │   └── FirebaseService.php
├── config/
│   └── firebase.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php
│   │   ├── public/
│   │   │   ├── index.blade.php
│   │   │   ├── profil-desa.blade.php
│   │   │   ├── profil-kelompok.blade.php
│   │   │   ├── program-kerja.blade.php
│   │   │   ├── program-kerja-detail.blade.php
│   │   │   ├── artikel.blade.php
│   │   │   ├── artikel-detail.blade.php
│   │   │   ├── galeri.blade.php
│   │   │   ├── timeline.blade.php
│   │   │   └── kontak.blade.php
│   │   └── admin/
│   │       └── dashboard.blade.php
├── routes/
│   └── web.php
└── storage/
    └── app/
        └── firebase-credentials.json

```

## Fitur Utama

### Landing Page
- Banner dengan informasi kelompok
- Highlight program kerja
- Artikel terbaru
- Profil anggota

### Profil Desa
- Informasi umum desa
- Sejarah dan filosofi
- Potensi ekonomi
- Kontak pemerintah desa

### Program Kerja
- Daftar program kerja
- Filter berdasarkan status
- Detail program dengan galeri

### Artikel
- Daftar artikel kegiatan
- Search dan filter kategori
- Detail artikel dengan share buttons

### Galeri
- Grid galeri responsif
- Filter berdasarkan kategori
- Modal viewer untuk foto

### Timeline
- Timeline visual kegiatan
- Ikon dan warna custom
- Responsive design

### Dashboard Admin
- Statistik konten
- Menu manajemen CRUD
- List artikel terbaru

