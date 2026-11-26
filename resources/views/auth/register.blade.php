@extends('layouts.auth')
@section('content')
<div class="auth-heading"><p class="eyebrow">AKAUN BAHARU</p><h1>Daftar akaun</h1><p class="muted">Cipta akaun untuk mula menggunakan iCOOP.</p></div>
<form class="auth-form" method="POST" action="{{ route('register.store') }}">
    @csrf
    <label>Nama penuh<input type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus></label>
    <label>E-mel<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
    <label>Kata laluan<input type="password" name="password" autocomplete="new-password" required><small>Minimum 8 aksara.</small></label>
    <label>Sahkan kata laluan<input type="password" name="password_confirmation" autocomplete="new-password" required></label>
    <button class="button primary" type="submit">Daftar akaun</button>
</form>
<p class="auth-switch">Sudah mempunyai akaun? <a href="{{ route('login') }}">Log masuk</a></p>
@endsection
