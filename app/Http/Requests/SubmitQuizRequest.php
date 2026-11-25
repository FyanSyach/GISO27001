<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Memvalidasi bentuk jawaban; kepemilikan pertanyaan diperiksa layanan penilaian. */
class SubmitQuizRequest extends FormRequest
{
    /** Hanya pengguna terautentikasi boleh mengirim kuis. */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** Mengembalikan aturan payload tanpa menerima skor atau reward dari klien. */
    public function rules(): array
    {
        return ['answers' => ['required', 'array', 'max:100'], 'answers.*' => ['required', 'string', 'max:255']];
    }
}
