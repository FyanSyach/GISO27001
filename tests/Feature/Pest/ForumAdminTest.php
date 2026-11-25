<?php

use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('rejects editing another employees thread', function (): void {
    $thread = ForumThread::factory()->create();
    $this->actingAs(User::factory()->create())->patch(route('forum.update', $thread), ['title' => 'Palsu', 'category' => 'Umum', 'content' => 'Diubah'])->assertForbidden();
});

it('allows owners to create reply and select a solution from their own thread only', function (): void {
    $user = User::factory()->create();
    $thread = ForumThread::factory()->create(['user_id' => $user->id]);
    $foreign = ForumReply::factory()->create();
    $this->actingAs($user)->post(route('forum.solution', $thread), ['reply_id' => $foreign->id])->assertNotFound();
    $this->post(route('forum.replies.store', $thread), ['content' => 'Solusi aman'])->assertRedirect();
    $reply = $thread->replies()->sole();
    $this->post(route('forum.solution', $thread), ['reply_id' => $reply->id])->assertRedirect();
    expect($thread->fresh()->best_reply_id)->toBe($reply->id);
});

it('protects every admin endpoint from regular users', function (): void {
    $this->actingAs(User::factory()->create());
    foreach (['admin.dashboard', 'admin.users.index', 'admin.modules.index', 'admin.reports.excel', 'admin.reports.pdf'] as $route) {
        $this->get(route($route))->assertForbidden();
    }
});

it('creates users securely and prevents self deletion', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)->post(route('admin.users.store'), ['name' => 'Pegawai Baru', 'email' => 'new@example.test', 'division' => 'HR', 'role' => 'user', 'is_active' => true, 'password' => 'SecurePassword!123'])->assertSessionHasNoErrors();
    $new = User::where('email', 'new@example.test')->sole();
    expect(Hash::check('SecurePassword!123', $new->password))->toBeTrue();
    $this->delete(route('admin.users.destroy', $admin))->assertStatus(422);
});
