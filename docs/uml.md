# POS Apotek - UML Diagrams

> Render online: [mermaid.live](https://mermaid.live) atau paste di GitHub/Notion.

---

## 1. Class Diagram

```mermaid
classDiagram
    direction TB

    class User {
        +int id
        +string name
        +string email
        +string password
        +string role "admin|kasir|apoteker"
        +boolean aktif
        +isAdmin() bool
        +isApoteker() bool
        +isKasir() bool
    }

    class Pelanggan {
        +int id
        +string nama
        +string telepon
        +string member_id
        +boolean is_member
        +date member_since
        +generateMemberId() string
    }

    class Kategori {
        +int id
        +string nama
    }

    class Satuan {
        +int id
        +string nama
    }

    class Pabrik {
        +int id
        +string nama
    }

    class Supplier {
        +int id
        +string nama
        +string telepon
        +string alamat
    }

    class Barang {
        +int id
        +string nama
        +int kategori_id
        +int satuan_id
        +int pabrik_id
        +string barcode
        +boolean butuh_resep
        +int stok_minimum
        +boolean aktif
        +stokTotal() int
        +batchFefo() Collection
        +stokMenipis() bool
        +hargaJualTerkini() float
    }

    class DetailPenerimaan {
        +int id
        +int penerimaan_id
        +int barang_id
        +string no_batch
        +float harga_beli
        +float harga_jual
        +date expired_date
        +int jumlah
        +int stok
        +boolean aktif
        +sudahExpired() bool
    }

    class Penerimaan {
        +int id
        +int user_id
        +int supplier_id
        +date tanggal
        +string no_faktur
        +boolean lunas
    }

    class Penjualan {
        +int id
        +int user_id
        +int pelanggan_id
        +date tanggal
        +string no_faktur
        +float total
    }

    class DetailPenjualan {
        +int id
        +int penjualan_id
        +int detail_penerimaan_id
        +float harga_jual
        +int jumlah
        +float diskon
        +float subtotal
    }

    class CustomDiscount {
        +int id
        +string nama
        +int persentase
        +date tanggal_mulai
        +date tanggal_selesai
        +boolean aktif
        +string cakupan "semua|kategori|barang|kombinasi"
        +getPercentForBarang() int
    }

    class DiscountUsage {
        +int id
        +int penjualan_id
        +int detail_penjualan_id
        +int barang_id
        +string barang_nama
        +string jenis "member|custom"
        +int custom_discount_id
        +string custom_discount_nama
        +int persentase
        +float nominal
    }

    class Rusak {
        +int id
        +int detail_penerimaan_id
        +date tanggal
        +int jumlah
        +string keterangan
    }

    class ActivityLog {
        +int id
        +int user_id
        +string action
        +timestamps
    }

    User "1" --> "*" Penerimaan : membuat
    User "1" --> "*" Penjualan : membuat
    User "1" --> "*" ActivityLog : menghasilkan

    Pelanggan "1" --> "*" Penjualan : memiliki
    Pelanggan "1" --> "*" DiscountUsage : melalui Penjualan

    Kategori "1" --> "*" Barang
    Satuan "1" --> "*" Barang
    Pabrik "1" --> "*" Barang
    Supplier "1" --> "*" Penerimaan

    Barang "1" --> "*" DetailPenerimaan
    Penerimaan "1" --> "*" DetailPenerimaan

    DetailPenerimaan "1" --> "*" DetailPenjualan
    DetailPenerimaan "1" --> "*" Rusak
    Penjualan "1" --> "*" DetailPenjualan
    Penjualan "1" --> "*" DiscountUsage

    CustomDiscount "1" --> "*" DiscountUsage
    CustomDiscount "1" --> "*" Kategori : many-to-many
    CustomDiscount "1" --> "*" Barang : many-to-many
```

---

## 2. Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email UK
        string password
        string role "admin|kasir|apoteker"
        boolean aktif
        timestamp created_at
        timestamp updated_at
    }

    kategoris {
        bigint id PK
        string nama
        timestamp created_at
        timestamp updated_at
    }

    satuans {
        bigint id PK
        string nama
        timestamp created_at
        timestamp updated_at
    }

    pabriks {
        bigint id PK
        string nama
        timestamp created_at
        timestamp updated_at
    }

    suppliers {
        bigint id PK
        string nama
        string telepon
        string alamat
        timestamp created_at
        timestamp updated_at
    }

    pelanggans {
        bigint id PK
        string nama
        string telepon
        string member_id UK "MBR-XXXXXX"
        boolean is_member
        date member_since
        timestamp created_at
        timestamp updated_at
    }

    barangs {
        bigint id PK
        string nama
        bigint kategori_id FK
        bigint satuan_id FK
        bigint pabrik_id FK
        string barcode UK
        boolean butuh_resep
        int stok_minimum
        boolean aktif
        timestamp created_at
        timestamp updated_at
    }

    penerimaans {
        bigint id PK
        bigint user_id FK
        bigint supplier_id FK
        date tanggal
        string no_faktur UK
        boolean lunas
        timestamp created_at
        timestamp updated_at
    }

    detail_penerimaans {
        bigint id PK
        bigint penerimaan_id FK
        bigint barang_id FK
        string no_batch
        decimal harga_beli
        decimal harga_jual
        date expired_date
        int jumlah
        int stok
        boolean aktif
        timestamp created_at
        timestamp updated_at
    }

    penjualans {
        bigint id PK
        bigint user_id FK
        bigint pelanggan_id FK "nullable"
        date tanggal
        string no_faktur UK
        decimal total
        timestamp created_at
        timestamp updated_at
    }

    detail_penjualans {
        bigint id PK
        bigint penjualan_id FK
        bigint detail_penerimaan_id FK
        decimal harga_jual
        int jumlah
        decimal diskon
        decimal subtotal
        timestamp created_at
        timestamp updated_at
    }

    rusaks {
        bigint id PK
        bigint detail_penerimaan_id FK
        date tanggal
        int jumlah
        string keterangan
        timestamp created_at
        timestamp updated_at
    }

    custom_discounts {
        bigint id PK
        string nama
        int persentase
        date tanggal_mulai
        date tanggal_selesai
        boolean aktif
        string cakupan
        timestamp created_at
        timestamp updated_at
    }

    custom_discount_categories {
        bigint custom_discount_id FK
        bigint kategori_id FK
    }

    custom_discount_barangs {
        bigint custom_discount_id FK
        bigint barang_id FK
    }

    discount_usages {
        bigint id PK
        bigint penjualan_id FK
        bigint detail_penjualan_id FK
        bigint barang_id FK
        string barang_nama
        string jenis
        bigint custom_discount_id FK "nullable"
        string custom_discount_nama
        int persentase
        decimal nominal
        timestamp created_at
        timestamp updated_at
    }

    activity_logs {
        bigint id PK
        bigint user_id FK
        string action
        text detail
        timestamp created_at
        timestamp updated_at
    }

    users ||--o{ penerimaans : "membuat"
    users ||--o{ penjualans : "membuat"
    users ||--o{ activity_logs : "menghasilkan"

    suppliers ||--o{ penerimaans : "memasok"

    kategoris ||--o{ barangs : "memiliki"
    satuans ||--o{ barangs : "mengukur"
    pabriks ||--o{ barangs : "memproduksi"

    barangs ||--o{ detail_penerimaans : "diterima dalam batch"

    penerimaans ||--o{ detail_penerimaans : "berisi"

    detail_penerimaans ||--o{ detail_penjualans : "dijual"
    detail_penerimaans ||--o{ rusaks : "rusak"

    pelanggans ||--o{ penjualans : "membeli"

    penjualans ||--o{ detail_penjualans : "berisi"
    penjualans ||--o{ discount_usages : "menggunakan diskon"

    custom_discounts ||--o{ discount_usages : "dicatat pemakaiannya"
    custom_discounts }|--|{ custom_discount_categories : "berlaku untuk kategori"
    custom_discounts }|--|{ custom_discount_barangs : "berlaku untuk barang"
```

---

## 3. Use Case Diagram

```mermaid
graph TB
    subgraph Actors[" "]
        admin["👤 Admin"]
        kasir["👤 Kasir"]
        apoteker["👤 Apoteker"]
    end

    subgraph UC["POS Apotek System"]
        direction TB
        UC1(["🔐 Login"])
        UC2(["📊 Dashboard"])
        UC3(["🏷️ Kelola Kategori"])
        UC4(["📐 Kelola Satuan"])
        UC5(["🏭 Kelola Pabrik"])
        UC6(["🚚 Kelola Supplier"])
        UC7(["📦 Kelola Barang"])
        UC8(["👥 Kelola Pelanggan"])
        UC8a(["📋 Daftar Member"])
        UC9(["📥 Penerimaan Barang"])
        UC10(["⚠️ Catat Barang Rusak"])
        UC11(["💰 Proses Penjualan"])
        UC12(["🏷️ Kelola Promo"])
        UC12a(["🔄 Toggle Promo"])
        UC13(["📈 Laporan Stok"])
        UC14(["📈 Laporan Penerimaan"])
        UC15(["📈 Laporan Penjualan"])
        UC16(["📈 Laporan Rusak"])
        UC17(["📈 Laporan Laba Rugi"])
        UC18(["📈 Laporan Diskon"])
        UC19(["📤 Export CSV"])
        UC20(["📝 Activity Log"])
    end

    admin --> UC1
    admin --> UC2
    admin --> UC3
    admin --> UC4
    admin --> UC5
    admin --> UC6
    admin --> UC7
    admin --> UC8
    admin --> UC9
    admin --> UC10
    admin --> UC11
    admin --> UC12
    admin --> UC13
    admin --> UC14
    admin --> UC15
    admin --> UC16
    admin --> UC17
    admin --> UC18
    admin --> UC20

    kasir --> UC1
    kasir --> UC2
    kasir --> UC7
    kasir --> UC8a
    kasir --> UC11

    apoteker --> UC1
    apoteker --> UC2

    UC12 -.-> UC12a
    UC13 -.-> UC19
    UC14 -.-> UC19
    UC15 -.-> UC19
    UC16 -.-> UC19
    UC18 -.-> UC19

    classDef actor fill:#e3f2fd,stroke:#1976d2,stroke-width:2px,font-weight:bold
    classDef usecase fill:#f3e5f5,stroke:#7b1fa2,stroke-width:1px,rx:50

    class admin,kasir,apoteker actor
    class UC1,UC2,UC3,UC4,UC5,UC6,UC7,UC8,UC8a,UC9,UC10,UC11,UC12,UC12a,UC13,UC14,UC15,UC16,UC17,UC18,UC19,UC20 usecase
```

---

## 4. Sequence Diagram - Transaksi Penjualan

```mermaid
sequenceDiagram
    autonumber
    actor Kasir
    participant Browser
    participant PenjualanCtrl as PenjualanController
    participant DB as Database
    participant ActivityLog

    Kasir->>Browser: Buka halaman kasir
    Browser->>PenjualanCtrl: GET /penjualan/create
    PenjualanCtrl->>DB: Load pelanggans
    PenjualanCtrl->>DB: Load barangs + stok
    PenjualanCtrl->>DB: Load custom discount per barang
    PenjualanCtrl-->>Browser: Render form kasir

    Note over Kasir,Browser: User memilih barang & pelanggan di client-side JS

    Kasir->>Browser: Klik "Simpan Transaksi"
    Browser->>PenjualanCtrl: POST /penjualan (items[], pelanggan_id, no_faktur, tanggal)

    PenjualanCtrl->>DB: BEGIN TRANSACTION

    rect rgb(245, 245, 245)
        Note over PenjualanCtrl,DB: Per Item Loop
        PenjualanCtrl->>DB: SELECT ... FOR UPDATE (lock barang row)
        PenjualanCtrl->>DB: Get custom discount % (getPercentForBarang)
        PenjualanCtrl->>DB: SELECT detail_penerimaans FOR UPDATE (FEFO batches)

        loop Setiap batch (FEFO order)
            PenjualanCtrl->>DB: INSERT detail_penjualan (harga, qty, diskon, subtotal)
            PenjualanCtrl->>DB: UPDATE detail_penerimaan SET stok = stok - qty
            alt stok batch habis
                PenjualanCtrl->>DB: UPDATE detail_penerimaan SET aktif = false
            end
            alt diskon > 0
                PenjualanCtrl->>DB: INSERT discount_usage (member/custom)
            end
        end

        alt stok tidak cukup
            PenjualanCtrl->>DB: ROLLBACK
            PenjualanCtrl-->>Browser: Error: stok tidak mencukupi
        end
    end

    PenjualanCtrl->>DB: UPDATE penjualan SET total = totalFaktur
    PenjualanCtrl->>DB: COMMIT
    PenjualanCtrl->>ActivityLog: Log transaksi penjualan
    PenjualanCtrl-->>Browser: Redirect ke detail penjualan
```

---

## 5. Sequence Diagram - Penerimaan Barang

```mermaid
sequenceDiagram
    autonumber
    actor Admin
    participant Browser
    participant PenerimaanCtrl as PenerimaanController
    participant DB as Database
    participant ActivityLog

    Admin->>Browser: Buka form penerimaan
    Browser->>PenerimaanCtrl: GET /penerimaan/create
    PenerimaanCtrl->>DB: Load suppliers, barangs
    PenerimaanCtrl-->>Browser: Render form

    Admin->>Browser: Isi form + items (barang, no_batch, harga, qty, expired)
    Browser->>PenerimaanCtrl: POST /penerimaan

    PenerimaanCtrl->>DB: BEGIN TRANSACTION
    PenerimaanCtrl->>DB: INSERT penerimaan (faktur, supplier, tanggal)
    loop Setiap item
        PenerimaanCtrl->>DB: INSERT detail_penerimaan (batch baru, stok = jumlah)
    end
    PenerimaanCtrl->>DB: COMMIT
    PenerimaanCtrl-->>Browser: Redirect index + success toast
```

---

## 6. Sequence Diagram - Login & Auth

```mermaid
sequenceDiagram
    autonumber
    actor User
    participant Browser
    participant LoginCtrl as LoginController
    participant Auth as Laravel Auth
    participant DB as Database

    User->>Browser: Buka /login
    Browser-->>User: Render login form

    User->>Browser: Submit email + password
    Browser->>LoginCtrl: POST /login

    LoginCtrl->>Auth: Auth::attempt(credentials)
    Auth->>DB: SELECT users WHERE email = ?
    alt credentials salah
        Auth-->>LoginCtrl: false
        LoginCtrl-->>Browser: Error: "Email atau password salah"
    else credentials benar
        alt user tidak aktif
            Auth-->>LoginCtrl: true
            LoginCtrl->>Auth: Auth::logout()
            LoginCtrl-->>Browser: Error: "Akun tidak aktif"
        else user aktif
            Auth-->>LoginCtrl: true
            LoginCtrl->>DB: Regenerate session
            LoginCtrl-->>Browser: Redirect ke /dashboard
        end
    end
```

---

## 7. Sequence Diagram - Register Member

```mermaid
sequenceDiagram
    autonumber
    actor Kasir
    participant Browser as Browser (Kasir UI)
    participant PelangganCtrl as PelangganController
    participant DB as Database

    Kasir->>Browser: Klik "+ Member"
    Browser-->>Kasir: Buka modal daftar member

    Kasir->>Browser: Isi nama + HP, submit
    Browser->>PelangganCtrl: POST /pelanggan/register-member (JSON)

    alt HP sudah ada di DB
        PelangganCtrl->>DB: SELECT pelanggans WHERE telepon = ?
        alt Sudah member
            PelangganCtrl-->>Browser: { success: true, member (existing) }
        else Belum member (upgrade)
            loop Retry up to 5x (race condition)
                PelangganCtrl->>DB: BEGIN TRANSACTION
                PelangganCtrl->>DB: Generate member_id (MBR-XXXXXX)
                PelangganCtrl->>DB: UPDATE pelanggan SET is_member=true
                alt unique constraint OK
                    PelangganCtrl->>DB: COMMIT
                    PelangganCtrl->>DB: INSERT activity_log
                    PelangganCtrl-->>Browser: { success: true, member }
                else duplicate member_id
                    PelangganCtrl->>DB: ROLLBACK
                    Note right of PelangganCtrl: Retry...
                end
            end
        end
    else HP baru
        loop Retry up to 5x
            PelangganCtrl->>DB: BEGIN TRANSACTION
            PelangganCtrl->>DB: Generate member_id
            PelangganCtrl->>DB: INSERT pelanggan (is_member=true)
            alt OK
                PelangganCtrl->>DB: COMMIT
                PelangganCtrl-->>Browser: { success: true, member }
            else duplicate
                PelangganCtrl->>DB: ROLLBACK
            end
        end
    end

    Browser->>Browser: Tambah ke local pelanggans array
    Browser->>Browser: selectPelanggan(memberObj)
```

---

## 8. Activity Diagram - Alur Transaksi Penjualan

```mermaid
flowchart TD
    Start([Mulai]) --> OpenKasir[Buka Halaman Kasir]
    OpenKasir --> LoadData[Load Data: Barang + Pelanggan + Discount]
    LoadData --> SearchBarang[Cari / Pilih Barang]

    SearchBarang --> AddToCart[Tambah ke Keranjang]
    AddToCart --> CheckStokClient{Stok Cukup?}
    CheckStokClient -->|Ya| ShowSubtotal[Tampilkan Subtotal + Diskon]
    CheckStokClient -->|Tidak| WarnStok[Warning: Stok Kurang]
    WarnStok --> SearchBarang

    ShowSubtotal --> MoreItem{Tambah Lagi?}
    MoreItem -->|Ya| SearchBarang
    MoreItem -->|Tidak| SelectPelanggan[Pilih Pelanggan / Member]

    SelectPelanggan --> SetFaktur[Isi No. Faktur + Tanggal]
    SetFaktur --> Submit[Simpan Transaksi]

    Submit --> ValidateServer{Validasi Server}
    ValidateServer -->|Gagal| ShowError[Tampilkan Error]
    ShowError --> SearchBarang

    ValidateServer -->|OK| BeginTx[BEGIN TRANSACTION]
    BeginTx --> LoopItem{Untuk Setiap Item}

    LoopItem --> LockBarang[Lock Row Barang FOR UPDATE]
    LockBarang --> GetDiscount[Ambil Diskon Custom %]
    GetDiscount --> CalcTotalDiskon[Diskon Total = min(50%, Member% + Custom%)]
    CalcTotalDiskon --> GetBatches[Ambil Batch FEFO]
    GetBatches --> LoopBatch{Untuk Setiap Batch}

    LoopBatch --> CalcSubtotal[Subtotal = (Harga x Qty) - Diskon]
    CalcSubtotal --> InsertDetail[INSERT detail_penjualan]
    InsertDetail --> DecStok[UPDATE stok batch]
    DecStok --> CheckStokHabis{Stok Batch Habis?}
    CheckStokHabis -->|Ya| DeactivateBatch[SET aktif = false]
    CheckStokHabis -->|Tidak| LogDiscount{Ada Diskon?}

    DeactivateBatch --> LogDiscount
    LogDiscount -->|Ya| InsertDiscountUsage[INSERT discount_usage]
    LogDiscount -->|Tidak| CheckSisa{Sisa Qty > 0?}

    InsertDiscountUsage --> CheckSisa
    CheckSisa -->|Ya| LoopBatch
    CheckSisa -->|Tidak| CheckAllItem{Item Lain?}

    CheckAllItem -->|Ya| LoopItem
    CheckAllItem -->|Tidak| UpdateTotal[UPDATE penjualan.total]
    UpdateTotal --> LogActivity[Log Activity]
    LogActivity --> CommitTx[COMMIT]
    CommitTx --> Success[Redirect: Detail Penjualan + Success Toast]
    Success --> End([Selesai])
```

---

## 9. Activity Diagram - Kelola Custom Discount

```mermaid
flowchart TD
    Start([Mulai]) --> AdminOpen[Buka Halaman Custom Discount]
    AdminOpen --> ListDiscount[Tampilkan Daftar Promo + Filter Status]

    ListDiscount --> Action{Aksi}

    Action -->|Buat Baru| CreateForm[Form: Nama, %, Tanggal, Cakupan]
    CreateForm --> ValidateForm{Validasi}
    ValidateForm -->|Gagal| ShowFormError[Tampilkan Error]
    ShowFormError --> CreateForm
    ValidateForm -->|OK| CheckOverlap{Cek Overlap Konflik}

    CheckOverlap -->|Konflik| ShowConflict[Error: Konflik dengan Promo X]
    ShowConflict --> CreateForm
    CheckOverlap -->|Aman| SavePromo[BEGIN TRANSACTION]
    SavePromo --> SyncRelations[Sync kategori/barang pivot]
    SyncRelations --> LogCreate[Log: Create Promo]
    LogCreate --> CommitSave[COMMIT + Redirect]

    Action -->|Edit| EditForm[Form Edit + Pre-fill Data]
    EditForm --> CheckOverlapEdit{Cek Overlap}
    CheckOverlapEdit -->|Konflik| ShowConflictEdit[Error Konflik]
    ShowConflictEdit --> EditForm
    CheckOverlapEdit -->|Aman| UpdatePromo[BEGIN TRANSACTION]
    UpdatePromo --> SyncEdit[Sync/Sync Detach pivot]
    SyncEdit --> LogUpdate[Log: Update Promo]
    LogUpdate --> CommitUpdate[COMMIT + Redirect]

    Action -->|Toggle Aktif/Nonaktif| CheckToggle{Mengaktifkan?}
    CheckToggle -->|Ya| CheckOverlapToggle{Cek Overlap}
    CheckOverlapToggle -->|Konflik| ShowToggleConflict[Error: Tidak Bisa Aktifkan]
    CheckOverlapToggle -->|Aman| DoToggle[UPDATE aktif = !aktif]
    CheckToggle -->|Nonaktifkan| DoToggle
    DoToggle --> LogToggle[Log: Toggle Promo]

    Action -->|Hapus| ConfirmHapus[Konfirmasi Hapus]
    ConfirmHapus --> DeletePromo[BEGIN TRANSACTION]
    DeletePromo --> DetachRelations[Detach kategori + barang]
    DetachRelations --> LogDelete[Log: Delete Promo]
    LogDelete --> CommitDelete[COMMIT + Redirect]

    CommitSave --> End([Selesai])
    CommitUpdate --> End
    LogToggle --> End
    CommitDelete --> End
```

---

## 10. Component Diagram

```mermaid
graph TB
    subgraph Client["Client (Browser)"]
        Blade["Blade Templates<br/>+ Tailwind CSS"]
        JS["Vanilla JS<br/>Kasir Cart Logic"]
        Axios["Axios<br/>AJAX Calls"]
    end

    subgraph Laravel["Laravel 12 Application"]
        subgraph HTTP["HTTP Layer"]
            Router["Route::web()"]
            MW_Auth["auth middleware"]
            MW_Role["CheckRole middleware"]
        end

        subgraph Controllers["Controllers"]
            LoginCtrl["LoginController"]
            DashboardCtrl["DashboardController"]
            BarangCtrl["BarangController"]
            PenjualanCtrl["PenjualanController"]
            PenerimaanCtrl["PenerimaanController"]
            PelangganCtrl["PelangganController"]
            RusakCtrl["RusakController"]
            LaporanCtrl["LaporanController"]
            CustomDiscountCtrl["CustomDiscountController"]
        end

        subgraph Models["Eloquent Models"]
            User["User"]
            Barang["Barang"]
            Penjualan["Penjualan"]
            Pelanggan["Pelanggan"]
            DetailPenerimaan["DetailPenerimaan"]
            DetailPenjualan["DetailPenjualan"]
            CustomDiscount["CustomDiscount"]
            DiscountUsage["DiscountUsage"]
            ActivityLog["ActivityLog"]
        end

        subgraph Config["Config"]
            POS["config/pos.php<br/>diskon_member, max_diskon_percent"]
        end
    end

    subgraph DB["Database (MySQL)"]
        MySQL[("pos_apotek<br/>14 tables")]
    end

    Blade --> Router
    JS --> Axios
    Axios --> Router

    Router --> MW_Auth
    MW_Auth --> MW_Role
    MW_Role --> Controllers

    Controllers --> Models
    Controllers --> Config
    Models --> MySQL
```

---

## 11. State Diagram - Status Barang

```mermaid
stateDiagram-v2
    [*] --> Aktif: Create Barang

    AdaStok --> StokMenipis: stok menipis
    StokMenipis --> AdaStok: stok bertambah
    AdaStok --> StokHabis: semua batch habis
    StokHabis --> AdaStok: penerimaan baru

    Aktif --> Nonaktif: Edit (uncheck aktif)
    Nonaktif --> Aktif: Edit (check aktif)

    Aktif --> Terhapus: Delete (tanpa riwayat)
    Nonaktif --> Terhapus: Delete (tanpa riwayat)

    Terhapus --> [*]
```

---

## 12. State Diagram - Status Custom Discount

```mermaid
stateDiagram-v2
    [*] --> BelumMulai: Create (tanggal_mulai > today)

    BelumMulai --> Aktif: tgl_mulai tercapai
    BelumMulai --> Dibatalkan: Toggle off / Delete

    Aktif --> Berakhir: tgl_selesai lewat
    Aktif --> Dibatalkan: Toggle off

    Berakhir --> [*]

    Dibatalkan --> Aktif: Toggle on + no overlap
    Dibatalkan --> [*]: Delete
```

---

## Ringkasan

| Diagram | File | Kegunaan |
|---------|------|----------|
| Class Diagram | Section 1 | Struktur model & relasi |
| ERD | Section 2 | Schema database lengkap |
| Use Case | Section 3 | Hak akses per role |
| Sequence - Penjualan | Section 4 | Alur transaksi kasir |
| Sequence - Penerimaan | Section 5 | Alur masuk barang |
| Sequence - Login | Section 6 | Autentikasi |
| Sequence - Register Member | Section 7 | Pendaftaran member |
| Activity - Penjualan | Section 8 | Detail alur kasir |
| Activity - Custom Discount | Section 9 | Manajemen promo |
| Component | Section 10 | Arsitektur tingkat tinggi |
| State - Barang | Section 11 | Lifecycle barang |
| State - Discount | Section 12 | Lifecycle promo |

> **Cara render**: Buka [mermaid.live](https://mermaid.live), copy-paste code block yang diinginkan.
