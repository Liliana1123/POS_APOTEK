<?php

return [
    'diskon_member' => max(0, min(50, (int) env('DISKON_MEMBER', 10))), // Validasi 0 - 50
];
