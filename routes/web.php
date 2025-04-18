<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;

Route::redirect('/', '/dashboard/login')->name('login');


