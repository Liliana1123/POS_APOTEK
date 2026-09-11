<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'semua');
        $allowedStatuses = [
            'semua',
            'pelanggan_tetap',
            'keluarga_nakes',
            'member_only',
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'semua';
        }

        $statusPiutang = $request->input('status_piutang', 'semua');
        $allowedStatusPiutang = [
            'semua',
            'lunas',
            'belum_lunas',
        ];

        if (!in_array($statusPiutang, $allowedStatusPiutang, true)) {
            $statusPiutang = 'semua';
        }

        $query = Pelanggan::query();

        // Halaman Pelanggan/Member hanya menampilkan member.
        $query->where('is_member', true);

        if ($status === 'pelanggan_tetap') {
            $query->where('status_member', 'Member Pelanggan Tetap');
        } elseif ($status === 'keluarga_nakes') {
            $query->where('status_member', 'Member Keluarga Nakes');
        } elseif ($status === 'member_only') {
            $query->where('status_member', 'Member Only');
        }

        if ($statusPiutang === 'lunas') {
            $query->where(function ($q) {
                $q->whereNull('saldo_piutang')
                    ->orWhere('saldo_piutang', '<=', 0);
            });
        } elseif ($statusPiutang === 'belum_lunas') {
            $query->where('saldo_piutang', '>', 0);
        }

        if ($request->filled('cari')) {
            $search = $request->input('cari');

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('telepon', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%");
            });
        }

        $pelanggans = $query
            ->withCount('penjualan')
            ->withSum('penjualan as total_belanja', 'total')
            ->withSum('discountUsages as total_diskon', 'nominal')
            ->orderByRaw('member_id IS NULL, member_id asc')
            ->paginate(15)
            ->withQueryString();

        return view('pelanggan.index', compact(
            'pelanggans',
            'status',
            'statusPiutang'
        ));
    }

    public function show(Request $request, Pelanggan $pelanggan)
    {
        $pelanggan->loadCount('penjualan');

        $totalBelanja = (float) $pelanggan->penjualan()->sum('total');
        $totalDiskon = (float) $pelanggan->discountUsages()->sum('nominal');

        // Riwayat transaksi terbaru untuk ditampilkan di modal Detail.
        $penjualans = $pelanggan->penjualan()
            ->select([
                'id',
                'pelanggan_id',
                'tanggal',
                'no_faktur',
                'total',
                'metode_pembayaran',
            ])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        // Jika dipanggil dari modal Detail, kembalikan JSON.
        if ($request->expectsJson()) {
            return response()->json([
                'pelanggan' => [
                    'id' => $pelanggan->id,
                    'nama' => $pelanggan->nama,
                    'member_id' => $pelanggan->member_id,
                    'telepon' => $pelanggan->telepon,
                    'alamat' => $pelanggan->alamat,
                    'is_member' => (bool) $pelanggan->is_member,
                    'member_aktif' => (bool) $pelanggan->member_aktif,
                    'status_member' => $pelanggan->status_member,
                    'saldo_piutang' => (float) ($pelanggan->saldo_piutang ?? 0),
                    'total_diskon' => $totalDiskon,
                    'total_belanja' => $totalBelanja,
                    'jumlah_transaksi' => $pelanggan->penjualan_count,
                ],
                'transaksi' => $penjualans->map(function ($penjualan) {
                    return [
                        'id' => $penjualan->id,
                        'tanggal' => $penjualan->tanggal?->format('Y-m-d'),
                        'no_faktur' => $penjualan->no_faktur,
                        'metode_pembayaran' => $penjualan->metode_pembayaran,
                        'total' => (float) $penjualan->total,
                    ];
                })->values(),
            ]);
        }

        // Tetap pertahankan halaman show lama jika route dibuka langsung.
        $pelanggan->total_belanja = $totalBelanja;
        $pelanggan->total_diskon = $totalDiskon;

        $penjualansPaginated = $pelanggan->penjualan()
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10);

        return view('pelanggan.show', [
            'pelanggan' => $pelanggan,
            'penjualans' => $penjualansPaginated,
        ]);
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:30',
            'alamat' => 'required|string',
            'tanggal_lahir' => 'nullable|date',
            'status_member' => 'required|in:Member Pelanggan Tetap,Member Keluarga Nakes,Member Only',
        ]);

        $attempts = 0;
        $maxAttempts = 5;
        $saved = false;
        $pelanggan = null;

        while ($attempts < $maxAttempts && !$saved) {
            try {
                DB::transaction(function () use ($data, &$saved, &$pelanggan) {
                    $data['member_id'] = Pelanggan::generateMemberId();
                    $data['is_member'] = true;
                    $data['member_aktif'] = true;
                    $data['member_since'] = now();
                    $data['saldo_piutang'] = $data['saldo_piutang'] ?? 0;

                    $pelanggan = Pelanggan::create($data);

                    \App\Models\ActivityLog::log(
                        'Register Member',
                        "Member ID: {$pelanggan->member_id}, Nama: {$pelanggan->nama}"
                    );

                    $saved = true;
                });
            } catch (QueryException $e) {
                if ($e->getCode() == '23000') {
                    $attempts++;

                    if ($attempts >= $maxAttempts) {
                        throw $e;
                    }

                    usleep(100000);
                } else {
                    throw $e;
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Membership berhasil ditambahkan.',
                'pelanggan' => $pelanggan->load('penjualan'),
            ], 201);
        }

        return redirect()
            ->route('pelanggan.index')
            ->with('success', 'Membership berhasil ditambahkan.');
    }

    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:30',
            'alamat' => 'required|string|max:1000',
            'tanggal_lahir' => 'nullable|date',
        ]);

        $pelanggan->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan/member berhasil diperbarui.',
                'pelanggan' => $pelanggan->load('penjualan'),
            ]);
        }

        return redirect()
            ->route('pelanggan.index')
            ->with('success', 'Data pelanggan/member berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        if ($pelanggan->penjualan()->exists()) {
            return back()->with(
                'error',
                'Membership tidak bisa dihapus karena punya riwayat transaksi.'
            );
        }

        $pelanggan->delete();

        return redirect()
            ->route('pelanggan.index')
            ->with('success', 'Membership berhasil dihapus.');
    }

    public function registerMember(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:30',
        ]);

        $telepon = $request->input('telepon');
        $nama = $request->input('nama');

        $pelanggan = null;

        if ($telepon) {
            $pelanggan = Pelanggan::where('telepon', $telepon)->first();
        }

        $maxAttempts = 5;

        if ($pelanggan) {
            if ($pelanggan->is_member) {
                return response()->json([
                    'success' => true,
                    'member' => [
                        'id' => $pelanggan->id,
                        'nama' => $pelanggan->nama,
                        'telepon' => $pelanggan->telepon,
                        'is_member' => true,
                        'member_id' => $pelanggan->member_id,
                        'member_aktif' => (bool) $pelanggan->member_aktif,
                        'diskon_percent' => $pelanggan->member_aktif
                            ? config('pos.diskon_member', 10)
                            : 0,
                    ],
                ]);
            }

            $attempts = 0;
            $saved = false;

            while ($attempts < $maxAttempts && !$saved) {
                try {
                    DB::transaction(function () use ($pelanggan, $nama, &$saved) {
                        $pelanggan->nama = $nama;
                        $pelanggan->member_id = Pelanggan::generateMemberId();
                        $pelanggan->is_member = true;
                        $pelanggan->member_aktif = true;
                        $pelanggan->member_since = now();
                        $pelanggan->save();

                        \App\Models\ActivityLog::log(
                            'Upgrade Member',
                            "Member ID: {$pelanggan->member_id}, Nama: {$pelanggan->nama}"
                        );

                        $saved = true;
                    });
                } catch (QueryException $e) {
                    if ($e->getCode() == '23000') {
                        $attempts++;

                        if ($attempts >= $maxAttempts) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Gagal membuat Member ID unik setelah beberapa percobaan.',
                            ], 422);
                        }

                        usleep(100000);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => $e->getMessage(),
                        ], 500);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'member' => [
                    'id' => $pelanggan->id,
                    'nama' => $pelanggan->nama,
                    'telepon' => $pelanggan->telepon,
                    'is_member' => true,
                    'member_id' => $pelanggan->member_id,
                    'member_aktif' => (bool) $pelanggan->member_aktif,
                    'diskon_percent' => $pelanggan->member_aktif
                        ? config('pos.diskon_member', 10)
                        : 0,
                ],
            ]);
        }

        $attempts = 0;
        $saved = false;
        $newPelanggan = null;

        while ($attempts < $maxAttempts && !$saved) {
            try {
                DB::transaction(function () use ($nama, $telepon, &$saved, &$newPelanggan) {
                    $newPelanggan = Pelanggan::create([
                        'nama' => $nama,
                        'telepon' => $telepon,
                        'member_id' => Pelanggan::generateMemberId(),
                        'is_member' => true,
                        'member_aktif' => true,
                        'member_since' => now(),
                        'saldo_piutang' => 0,
                    ]);

                    \App\Models\ActivityLog::log(
                        'Register Member',
                        "Member ID: {$newPelanggan->member_id}, Nama: {$newPelanggan->nama}"
                    );

                    $saved = true;
                });
            } catch (QueryException $e) {
                if ($e->getCode() == '23000') {
                    $attempts++;

                    if ($attempts >= $maxAttempts) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Gagal membuat Member ID unik setelah beberapa percobaan.',
                        ], 422);
                    }

                    usleep(100000);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 500);
                }
            }
        }

        return response()->json([
            'success' => true,
            'member' => [
                'id' => $newPelanggan->id,
                'nama' => $newPelanggan->nama,
                'telepon' => $newPelanggan->telepon,
                'is_member' => true,
                'member_id' => $newPelanggan->member_id,
                'member_aktif' => true,
                'diskon_percent' => config('pos.diskon_member', 10),
            ],
        ]);
    }
}
