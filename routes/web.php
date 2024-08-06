<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\{
    CancelPage,
    CartPage,
    CategoriesPage,
    CheckoutPage,
    Home,
    MyOrderDetailPage,
    MyOrdersPage,
    ProductDetailPage,
    ProductsPage,
    SuccessPage
};
use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;

Route::get('/', Home::class);
Route::get('/categorias', CategoriesPage::class);
Route::get('/produtos', ProductsPage::class);
Route::get('/meu-carrinho-de-compras', CartPage::class);
Route::get('/produtos/{slug}', ProductDetailPage::class);

Route::get('/checkout', CheckoutPage::class);
Route::get('/meus-pedidos', MyOrdersPage::class);
Route::get('/meus-pedidos/{order}', MyOrderDetailPage::class);

Route::get('/login', LoginPage::class);
Route::get('/cadastro', RegisterPage::class);
Route::get('/recuperar-senha', ForgotPasswordPage::class);
Route::get('/mudar-senha', ResetPasswordPage::class);

Route::get('/sucesso', SuccessPage::class);
Route::get('/cancelamento', CancelPage::class);
