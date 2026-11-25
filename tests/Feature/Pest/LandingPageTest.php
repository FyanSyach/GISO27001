<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia;

/** Memastikan beranda publik menyajikan landing page dan tidak membocorkan data internal. */
it('renders the public landing page without framework metadata', function (): void {
    $this->get('/')->assertOk()->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Welcome')
        ->where('canLogin', true)
        ->where('canRegister', true)
        ->missing('laravelVersion')
        ->missing('phpVersion'));
});

/** Pengunjung anonim tidak membawa data pengguna, sehingga CTA menuju login. */
it('exposes no authenticated user to guests', function (): void {
    $this->get('/')->assertInertia(fn (AssertableInertia $page) => $page
        ->where('auth.user', null));
});

/** Pengguna login tetap mendapat landing page dengan identitas sesi untuk CTA lanjut belajar. */
it('shares the authenticated user on the landing page', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/')->assertInertia(fn (AssertableInertia $page) => $page
        ->where('auth.user.id', $user->id)
        ->where('canLogin', true));
});
