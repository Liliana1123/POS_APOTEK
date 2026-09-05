<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::query();

        if ($request->filled('cari')) {
            $search = $request->input('cari');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('member_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'member') {
                $query->where('is_member', true);
            } elseif ($status === 'non-member') {
                $query->where('is_member', false);
            }
        }

        $pelanggans = $query->withCount('penjualan')
            ->withSum('penjualan as total_belanja', 'total')
            ->withSum('discountUsages as total_hemat', 'nominal')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('pelanggan.index', compact('pelanggans'));
    }

    public function show(Pelanggan $pelanggan)
    {
        $pelanggan->loadCount('penjualan');
        $pelanggan->total_belanja = $pelanggan->penjualan()->sum('total');
        $pelanggan->total_hemat = $pelanggan->discountUsages()->sum('nominal');

        $penjualans = $pelanggan->penjualan()
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10);

        return view('pelanggan.show', compact('pelanggan', 'penjualans'));
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:30',
            'is_member' => 'nullable|boolean',
        ]);

        $isMember = $request->boolean('is_member');
        $attempts = 0;
        $maxAttempts = 5;
        $saved = false;

        while ($attempts < $maxAttempts && !$saved) {
            try {
                DB::transaction(function () use ($data, $isMember, &$saved) {
                    if ($isMember) {
                        $data['member_id'] = Pelanggan::generateMemberId();
                        $data['is_member'] = true;
                        $data['member_since'] = now();
                    } else {
                        $data['member_id'] = null;
                        $data['is_member'] = false;
                        $data['member_since'] = null;
                    }
                    $pelanggan = Pelanggan::create($data);
                    if ($isMember) {
                        \App\Models\ActivityLog::log('Register Member', "Member ID: {$pelanggan->member_id}, Nama: {$pelanggan->nama}");
                    }
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
                'message' => 'Pelanggan berhasil ditambahkan.',
                'pelanggan' => $pelanggan->load('penjualan'),
            ], 201);
        }

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:30',
            'is_member' => 'nullable|boolean',
        ]);

        if ($pelanggan->is_member) {
            // Jangan izinkan mengubah status membership atau member_id jika sudah member
            unset($data['is_member']);
            unset($data['member_id']);
        } else {
            $isMember = $request->boolean('is_member');
            if ($isMember) {
                $attempts = 0;
                $maxAttempts = 5;
                $saved = false;

                while ($attempts < $maxAttempts && !$saved) {
                    try {
                        DB::transaction(function () use (&$data, $pelanggan, &$saved) {
                            $data['member_id'] = Pelanggan::generateMemberId();
                            $data['is_member'] = true;
                            $data['member_since'] = now();
                            $pelanggan->update($data);
                            \App\Models\ActivityLog::log('Upgrade Member', "Member ID: {$pelanggan->member_id}, Nama: {$pelanggan->nama}");
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
                return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui menjadi member.');
            }
        }

        $pelanggan->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil diperbarui.',
                'pelanggan' => $pelanggan->load('penjualan'),
            ]);
        }

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        if ($pelanggan->penjualan()->exists()) {
            return back()->with('error', 'Pelanggan tidak bisa dihapus karena punya riwayat transaksi.');
        }

        $pelanggan->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
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
                        'diskon_percent' => config('pos.diskon_member', 10),
                    ]
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
                        $pelanggan->member_since = now();
                        $pelanggan->save();
                        \App\Models\ActivityLog::log('Upgrade Member', "Member ID: {$pelanggan->member_id}, Nama: {$pelanggan->nama}");
                        $saved = true;
                    });
                } catch (QueryException $e) {
                    if ($e->getCode() == '23000') {
                        $attempts++;
                        if ($attempts >= $maxAttempts) {
                            return response()->json(['success' => false, 'message' => 'Gagal membuat Member ID unik setelah beberapa percobaan.'], 422);
                        }
                        usleep(100000);
                    } else {
                        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
                    'diskon_percent' => config('pos.diskon_member', 10),
                ]
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
                        'member_since' => now(),
                    ]);
                    \App\Models\ActivityLog::log('Register Member', "Member ID: {$newPelanggan->member_id}, Nama: {$newPelanggan->nama}");
                    $saved = true;
                });
            } catch (QueryException $e) {
                if ($e->getCode() == '23000') {
                    $attempts++;
                    if ($attempts >= $maxAttempts) {
                        return response()->json(['success' => false, 'message' => 'Gagal membuat Member ID unik setelah beberapa percobaan.'], 422);
                    }
                    usleep(100000);
                } else {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
                'diskon_percent' => config('pos.diskon_member', 10),
            ]
        ]);
    }
}
