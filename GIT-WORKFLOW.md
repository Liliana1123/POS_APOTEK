# Git Workflow — POS Apotek

> **Alur kerja:** Setiap user punya branch sendiri. Semua perubahan ditampung di `branch-dev` oleh integrator (Risang). Konflik diselesaikan di `branch-dev` supaya user lain tidak terganggu.

---

## Tim & Branch

| User | Branch | Peran |
|------|--------|-------|
| Risang (Kamu) | `risang-fitur-data-master` + `branch-dev` | Integrator — merge semua branch ke `branch-dev` |
| Teman 1 | `lili` | Developer |
| Teman 2 | `feriena` | Developer |

> Ganti nama branch sesuai kebutuhan. Prinsipnya sama.

---

## 1. Setup Awal

### Clone Repository (sekali saja)
```bash
git clone https://github.com/Liliana1123/POS_APOTEK.git
cd pos-apotek
```

### Checkout Branch Masing-Masing
```bash
# Kamu (integrator)
git checkout risang-fitur-data-master

# Teman 1
git checkout lili

# Teman 2
git checkout feriena
```

### Ambil Semua Branch Remote
```bash
git fetch --all
```

---

## 2. Daily Workflow — Developer (Teman 1 & 2)

### Selalu Mulai Kerja dengan Pull
```bash
# Pastikan di branch sendiri
git status
# output: On branch lili

# Tarik update terbaru dari remote
git pull origin lili

# Jika ada perubahan dari branch-dev, gabungkan dulu
git fetch origin
git merge origin/branch-dev
```

### Kerja & Commit
```bash
# Edit file, lalu...
git add .
git commit -m "deskripsi perubahan"
```

### Push ke Remote
```bash
git push origin lili
```

### Cek Status Sebelum Push
```bash
# Pastikan tidak ada yang tertinggal
git status

# Lihat branch mana saja yang sudah di-push
git branch -vv
```

---

## 3. Alur Integrasi — Kamu (Integrator)

> **Penting:** Kamu sebagai integrator bekerja langsung di `branch-dev`.  
> `risang-fitur-data-master` adalah branch pribadi kamu (untuk menyimpan fiturmu sendiri).  
> Jangan gabungkan branch teman ke `risang-fitur-data-master`, tapi gabungkan langsung ke `branch-dev`.

### Ambil Semua Perubahan dari Semua Branch

```bash
# Pastikan di branch-dev
git checkout branch-dev
git pull origin branch-dev

# Tarik semua branch remote terbaru
git fetch --all
```

### Merge Branch Teman 1 (lili)
```bash
git merge origin/lili --no-edit -m "Merge branch 'lili' into branch-dev"
```

### Jika TIDAK Ada Konflik
```bash
# Langsung push
git push origin branch-dev
```

### Jika ADA Konflik
```
CONFLICT (content): Merge conflict in file.php
Automatic merge failed; fix conflicts and then commit the result.
```

**Langkah resolve:**

```bash
# 1. Cari file yang konflik
git status
# output akan menunjukkan "Unmerged paths"

# 2. Buka file yang konflik, cari marker:
# <<<<<<< HEAD
# (kode dari branch-dev)
# =======
# (kode dari branch lili)
# >>>>>>> origin/lili

# 3. Edit file — pilih kode yang benar, hapus semua marker konflik

# 4. Tandai sudah diselesaikan
git add file.php

# 5. Commit hasil resolve
git commit -m "Resolve conflict: merge lili ke branch-dev"

# 6. Push
git push origin branch-dev
```

### Repeat untuk Branch Teman 2 (feriena)
```bash
git merge origin/feriena --no-edit -m "Merge branch 'feriena' into branch-dev"

# Jika konflik, resolve seperti langkah di atas

git push origin branch-dev
```

### Merge Perubahan dari Branch Pribadi Sendiri (`risang-fitur-data-master`)
Jika kamu punya perubahan di branch pribadimu yang ingin dimasukkan ke `branch-dev`:

```bash
# Pastikan tetap di branch-dev
git merge origin/risang-fitur-data-master --no-edit -m "Merge fitur risang into branch-dev"

# Jika konflik, selesaikan lalu push
git push origin branch-dev
```

---

## 4. Sinkronisasi ke Developer

### Developer Pull Update dari branch-dev
Setelah kamu push ke `branch-dev`, kabari teman-teman untuk update:

```bash
# Pastikan di branch sendiri
git checkout lili

# Ambil update dari branch-dev
git fetch origin
git merge origin/branch-dev

# Jika ada konflik di sisi developer (jarang terjadi), resolve seperti biasa
# Lalu push
git push origin lili
```

---

## 5. Kondisi Khusus

### 5a. Konflik File yang Sama di 2 Branch

Dua user mengedit file/fungsi yang sama. Kamu resolve di `branch-dev`:

```bash
git merge origin/lili --no-edit
# CONFLICT di src/obat.php

# Buka file, pilih mana yang dipakai (atau gabungkan keduanya)
# Simpan, lalu:
git add src/obat.php
git commit -m "Resolve: pilih logika stok dari lili, struktur dari feriena"
git push origin branch-dev
```

### 5b. Developer Belum Push Sebelum Kamu Merge

Developer lupa push. Kamu tidak melihat perubahannya.

```bash
# Cek branch remote apa adanya
git fetch --all

# Kalau belum di-push, kamu tidak akan lihat perubahan baru
# Kabari teman untuk push dulu:
# "Push dulu branch kamu, baru aku merge ke dev"
```

### 5c. Developer Kerja Tanpa Pull Dulu (Branche Diverge)

Developer mulai kerja tanpa pull, commit-nya sudah beda dengan remote.

```bash
# Di sisi developer:
git pull origin lili
# Output: CONFLICT atau auto-merge

# Jika auto-merge — langsung push
# Jika konflik — resolve, commit, push
```

**Pencegahan:** Selalu `git pull` sebelum mulai kerja.

### 5d. Rollback Perubahan

Jika ada commit yang salah di `branch-dev`:

```bash
# Rollback 1 commit (tapi tetap push history baru)
git revert HEAD
git push origin branch-dev

# JANGAN gunakan git reset -hard di branch yang sudah di-push
# Gunakan revert — lebih aman, tidak hapus history
```

### 5e. Backup Branch Sebelum Merge

Sebelum merge ke `branch-dev`, buat backup jika perlu:

```bash
# Backup branch-dev sebelum merge
git branch branch-dev-backup-20260915

# Merge seperti biasa
git merge origin/lili --no-edit

# Jika hasilnya buruk, restore:
git checkout branch-dev-backup-20260915
git branch -D branch-dev
git branch -m branch-dev-backup-20260915 branch-dev
```

### 5f. Membuat Branch Baru dari branch-dev

Jika ada fitur baru yang butuh branch sendiri:

```bash
# Dari branch-dev yang terbaru
git checkout branch-dev
git pull origin branch-dev

# Buat branch baru
git checkout -b fitur-baru

# Kerja, commit, push
git push origin fitur-baru
```

### 5g. Developer Salah Branch

Developer commit ke branch yang salah:

```bash
# Pindahkan commit ke branch yang benar
git log --oneline  # catat hash commit

# Pindah ke branch yang benar
git checkout lili
git cherry-pick <hash-commit>

# Hapus dari branch salah
git checkout risang-fitur-data-master
git reset --hard HEAD~1

# Push kedua branch
git push origin lili
git push origin risang-fitur-data-master
```

### 5h. Branch Teman Outdated (Ketinggalan Jauh)

Perubahan teman tidak sama dengan project terbaru karena dia jarang pull dari `branch-dev`.

```bash
# Di sisi developer:
git checkout lili
git fetch origin
git merge origin/branch-dev

# Jika muncul konflik, biarkan developer yang selesaikan di branch mereka 
# atau kamu bantu selesaikan saat merge ke branch-dev.
```

### 5i. Branch Teman Tidak Ada Perubahan (Up-to-date)

Saat kamu merge, muncul pesan "Already up to date". Artinya teman belum commit/push apapun yang baru.

```bash
git merge origin/lili
# Output: Already up to date.

# Tidak ada yang perlu dilakukan. 
# Minta teman cek apakah mereka sudah git add & git commit & git push.
```

---

## 6. Checklist Harian

### Developer (Teman 1 & 2)
- [ ] `git pull origin <branch-sendiri>` sebelum mulai kerja
- [ ] `git merge origin/branch-dev` untuk ambil update terbaru
- [ ] `git commit` dengan pesan yang jelas
- [ ] `git push origin <branch-sendiri>` setelah selesai

### Integrator (Kamu — Risang)
- [ ] `git fetch --all` ambil semua perubahan
- [ ] `git checkout branch-dev`
- [ ] `git merge origin/<branch-teman>` satu per satu
- [ ] Resolve conflict jika ada
- [ ] `git push origin branch-dev`
- [ ] Kabari teman bahwa sudah di-merge

---

## 7. Cheat Sheet Cepat

```
# Developer rutin:
git pull origin <branch> && git merge origin/branch-dev

# Integrator rutin:
git fetch --all && git checkout branch-dev && git merge origin/<branch-teman> && git push origin branch-dev

# Cek konflik:
git status | findstr "Unmerged"

# Hapus branch setelah merge (opsional):
git branch -d <branch-teman>        # local
git push origin --delete <branch>   # remote
```

---

## 8. Aturan Emas

1. **Selalu pull sebelum kerja.** Tidak ada pengecualian.
2. **Push setelah commit.** Jangan biarkan commit hanya di lokal.
3. **Konflik = tanggung jawab integrator.** Developer fokus di branch sendiri.
4. **Commit message yang jelas.** Format: `jenis: deskripsi` (contoh: `fix: koreksi stok obat`, `feat: tambah form pelanggan`)
5. **Jangan force push ke branch bersama.** Force push hanya untuk branch pribadi dan sudah dikonfirmasi aman.
6. **Backup sebelum merge besar.**
