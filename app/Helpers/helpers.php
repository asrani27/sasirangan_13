<?php

if (! function_exists('toastr')) {
    function toastr()
    {
        return new class
        {
            public function success(string $message, ?string $title = null): static
            {
                session()->flash('toast_success', $message);

                return $this;
            }

            public function error(string $message, ?string $title = null): static
            {
                session()->flash('toast_error', $message);

                return $this;
            }

            public function info(string $message, ?string $title = null): static
            {
                session()->flash('toast_info', $message);

                return $this;
            }

            public function warning(string $message, ?string $title = null): static
            {
                session()->flash('toast_warning', $message);

                return $this;
            }
        };
    }
}

if (! function_exists('rupiah')) {
    function rupiah(mixed $angka, int $decimals = 0): string
    {
        if ($angka === null || $angka === '') {
            return '0';
        }

        return 'Rp '.number_format((float) $angka, $decimals, ',', '.');
    }
}
