@props([
    'type' => 'custom', // pembayaran, penerimaan, stok, expired, aktif, resep, role, member, promo, custom
    'value' => null,
    'status' => null,
    'stok' => null,
    'min' => 0,
    'date' => null,
    'variant' => null,
    'label' => null,
])

@php
    $val = $value ?? $status;
    $computedVariant = 'secondary';
    $computedLabel = '';

    switch ($type) {
        case 'pembayaran':
        case 'lunas':
            $isLunas = filter_var($val, FILTER_VALIDATE_BOOLEAN) || in_array(strtolower((string)$val), ['lunas', '1', 'paid']);
            $computedVariant = $isLunas ? 'success' : 'warning';
            $computedLabel = $isLunas ? 'Lunas' : (strtolower((string)$val) === 'piutang' ? 'Piutang' : 'Belum Lunas');
            break;

        case 'penerimaan':
            $isLengkap = in_array(strtoupper((string)$val), ['LENGKAP', '1', 'TRUE']);
            $computedVariant = $isLengkap ? 'success' : 'warning';
            $computedLabel = $isLengkap ? 'Lengkap' : 'Belum Lengkap';
            break;

        case 'stok':
            if (is_string($val) && in_array(strtolower($val), ['aman', 'menipis', 'habis'])) {
                $computedLabel = ucfirst(strtolower($val));
                $computedVariant = match(strtolower($val)) {
                    'aman' => 'success',
                    'menipis' => 'warning',
                    default => 'danger'
                };
                break;
            }
            $stokVal = (float)($stok ?? $val ?? 0);
            $minVal = (float)$min;
            if ($stokVal === 0.0) {
                $computedVariant = 'danger';
                $computedLabel = 'Habis';
            } elseif ($stokVal < 0) {
                $computedVariant = 'danger';
                $computedLabel = 'Minus (' . $stokVal . ')';
            } elseif ($stokVal <= $minVal) {
                $computedVariant = 'warning';
                $computedLabel = 'Menipis';
            } else {
                $computedVariant = 'success';
                $computedLabel = 'Aman';
            }
            break;

        case 'expired':
        case 'kadaluarsa':
            $parsedDate = $date ?? $val;
            if (!$parsedDate) {
                $computedVariant = 'secondary';
                $computedLabel = 'Tidak Ada Tanggal';
            } else {
                $cDate = $parsedDate instanceof \Carbon\Carbon ? $parsedDate : \Carbon\Carbon::parse($parsedDate);
                $today = now()->startOfDay();
                $oneMonth = now()->addMonth();
                $threeMonths = now()->addMonths(3);

                if ($cDate->isSameDay($today) || $cDate->isBefore($today)) {
                    $computedVariant = 'danger';
                    $computedLabel = 'KADALUARSA';
                } elseif ($cDate->lt($oneMonth)) {
                    $computedVariant = 'orange';
                    $computedLabel = '< 1 Bulan';
                } elseif ($cDate->lt($threeMonths)) {
                    $computedVariant = 'warning';
                    $computedLabel = '< 3 Bulan';
                } else {
                    $computedVariant = 'success';
                    $computedLabel = 'Normal';
                }
            }
            break;

        case 'aktif':
            $isAktif = filter_var($val, FILTER_VALIDATE_BOOLEAN) || in_array(strtolower((string)$val), ['aktif', '1', 'true']);
            $computedVariant = $isAktif ? 'success' : 'danger';
            $computedLabel = $isAktif ? 'Aktif' : 'Nonaktif';
            break;

        case 'resep':
            $butuhResep = filter_var($val, FILTER_VALIDATE_BOOLEAN) || in_array((string)$val, ['1', 'true', 'resep']);
            $computedVariant = $butuhResep ? 'info' : 'secondary';
            $computedLabel = $butuhResep ? 'Wajib Resep' : 'Bebas';
            break;

        case 'role':
            $roleStr = strtolower(trim((string)$val));
            $computedVariant = match($roleStr) {
                'superadmin' => 'danger',
                'admin' => 'info',
                'apoteker' => 'warning',
                'gudang' => 'secondary',
                'kasir' => 'success',
                default => 'secondary',
            };
            $computedLabel = ucfirst($roleStr);
            break;

        case 'member':
            $statusStr = trim((string)$val);
            $computedVariant = match($statusStr) {
                'Member Keluarga Nakes' => 'indigo',
                'Member Only' => 'warning',
                'Member Pelanggan Tetap' => 'info',
                default => ($statusStr ? 'success' : 'secondary'),
            };
            $computedLabel = $statusStr ?: 'Umum';
            break;

        case 'promo':
            $isPromoAktif = isset($attributes['aktif']) ? (bool)$attributes['aktif'] : (bool)($val->aktif ?? true);
            $today = now()->format('Y-m-d');
            $start = isset($attributes['start']) ? $attributes['start'] : ($val && isset($val->tanggal_mulai) ? $val->tanggal_mulai->format('Y-m-d') : null);
            $end = isset($attributes['end']) ? $attributes['end'] : ($val && isset($val->tanggal_selesai) ? $val->tanggal_selesai->format('Y-m-d') : null);

            if (!$isPromoAktif) {
                $computedVariant = 'secondary';
                $computedLabel = 'Non-aktif';
            } elseif ($start && $start > $today) {
                $computedVariant = 'warning';
                $computedLabel = 'Belum Mulai';
            } elseif ($end && $end < $today) {
                $computedVariant = 'danger';
                $computedLabel = 'Berakhir';
            } else {
                $computedVariant = 'success';
                $computedLabel = 'Aktif';
            }
            break;

        default:
            $computedVariant = $variant ?? 'secondary';
            $computedLabel = $label ?? (string)$val;
            break;
    }

    $finalVariant = $variant ?? $computedVariant;
    $finalLabel = $label ?? $computedLabel;
@endphp

<x-badge :variant="$finalVariant" {{ $attributes->except(['type', 'value', 'status', 'stok', 'min', 'date', 'variant', 'label', 'aktif', 'start', 'end']) }}>
    {{ $slot->isNotEmpty() ? $slot : $finalLabel }}
</x-badge>
