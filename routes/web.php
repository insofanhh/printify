<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\OrderForm;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dat-in', OrderForm::class)->name('order.form');
