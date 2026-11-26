@extends('layouts.auth')
@section('content')
<div class="auth-heading"><p class="eyebrow">SELAMAT KEMBALI</p><h1>Log masuk</h1><p class="muted">Masuk untuk mengurus operasi koperasi anda.</p></div>
<form class="auth-form" method="POST" action="{{ route('login.store') }}">
    @csrf
    <label>E-mel<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></label>
    <label>Kata laluan<input type="password" name="password" autocomplete="current-password" required></label>
    <label class="check-label"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
    <button class="button primary" type="submit">Log masuk</button>
</form>
<p class="auth-switch">Akaun baharu hanya boleh dicipta oleh pentadbir iCOOP.</p>
@endsection
