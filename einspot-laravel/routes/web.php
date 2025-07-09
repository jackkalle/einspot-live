<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\QuoteRequestController;
use App\Http\Controllers\ContactSubmissionController;
use App\Http\Controllers\NewsletterSubscriptionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PageController; // For static pages and home
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\AdminDashboardController; // Added AdminDashboardController

// Default welcome page, can be changed to home later
// Route::get('/', function () {
//     return view('welcome');
// })->name('welcome');

// Static Pages & Home (will create PageController)
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
// Contact page will have a form, so its GET might be in PageController, POST in ContactSubmissionController
Route::get('/contact', [PageController::class, 'contact'])->name('contact.form');
Route::post('/contact', [ContactSubmissionController::class, 'store'])->name('contact.submit');

// Public facing resource routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show'); // Using slug

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show'); // Using slug

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project:slug}', [ProjectController::class, 'show'])->name('projects.show'); // Using slug

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{blog:slug}', [BlogController::class, 'show'])->name('blog.show'); // Using slug for Blog model

// Quote Request (public submission)
Route::post('/quote-request', [QuoteRequestController::class, 'store'])->name('quote.request.store');

// Newsletter Subscription (public submission)
Route::post('/newsletter/subscribe', [NewsletterSubscriptionController::class, 'subscribe'])->name('newsletter.subscribe');


// Authentication Routes (Manual Setup)
Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    // Placeholder for user dashboard if needed
    // Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
});

// Admin Routes
Route::prefix('admin')
    ->name('admin.')
    // ->middleware(['auth', 'admin']) // TODO: Create and apply 'admin' middleware
    ->middleware(['auth']) // Using auth for now
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Products
        Route::get('/products', [ProductController::class, 'adminIndex'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'adminCreate'])->name('products.create');
        Route::post('/products', [ProductController::class, 'adminStore'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'adminEdit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'adminUpdate'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'adminDestroy'])->name('products.destroy');

        // Categories
        Route::get('/categories', [CategoryController::class, 'adminIndex'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'adminCreate'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'adminStore'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'adminEdit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'adminUpdate'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'adminDestroy'])->name('categories.destroy');

        // Tags
        Route::get('/tags', [TagController::class, 'adminIndex'])->name('tags.index');
        Route::get('/tags/create', [TagController::class, 'adminCreate'])->name('tags.create');
        Route::post('/tags', [TagController::class, 'adminStore'])->name('tags.store');
        Route::get('/tags/{tag}/edit', [TagController::class, 'adminEdit'])->name('tags.edit');
        Route::put('/tags/{tag}', [TagController::class, 'adminUpdate'])->name('tags.update');
        Route::delete('/tags/{tag}', [TagController::class, 'adminDestroy'])->name('tags.destroy');

        // Services
        Route::get('/services', [ServiceController::class, 'adminIndex'])->name('services.index');
        Route::get('/services/create', [ServiceController::class, 'adminCreate'])->name('services.create');
        Route::post('/services', [ServiceController::class, 'adminStore'])->name('services.store');
        Route::get('/services/{service}/edit', [ServiceController::class, 'adminEdit'])->name('services.edit');
        Route::put('/services/{service}', [ServiceController::class, 'adminUpdate'])->name('services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'adminDestroy'])->name('services.destroy');

        // Projects
        Route::get('/projects', [ProjectController::class, 'adminIndex'])->name('projects.index');
        Route::get('/projects/create', [ProjectController::class, 'adminCreate'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'adminStore'])->name('projects.store');
        Route::get('/projects/{project}/edit', [ProjectController::class, 'adminEdit'])->name('projects.edit');
        Route::put('/projects/{project}', [ProjectController::class, 'adminUpdate'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'adminDestroy'])->name('projects.destroy');

        // Blogs
        Route::get('/blogs', [BlogController::class, 'adminIndex'])->name('blogs.index');
        Route::get('/blogs/create', [BlogController::class, 'adminCreate'])->name('blogs.create');
        Route::post('/blogs', [BlogController::class, 'adminStore'])->name('blogs.store');
        Route::get('/blogs/{blog}/edit', [BlogController::class, 'adminEdit'])->name('blogs.edit'); // Uses Route Model Binding for Blog
        Route::put('/blogs/{blog}', [BlogController::class, 'adminUpdate'])->name('blogs.update');
        Route::delete('/blogs/{blog}', [BlogController::class, 'adminDestroy'])->name('blogs.destroy');

        // Quote Requests
        Route::get('/quote-requests', [QuoteRequestController::class, 'adminIndex'])->name('quote-requests.index');
        Route::get('/quote-requests/{quoteRequest}', [QuoteRequestController::class, 'adminShow'])->name('quote-requests.show');
        Route::put('/quote-requests/{quoteRequest}/status', [QuoteRequestController::class, 'adminUpdateStatus'])->name('quote-requests.updateStatus');
        Route::delete('/quote-requests/{quoteRequest}', [QuoteRequestController::class, 'adminDestroy'])->name('quote-requests.destroy');

        // Contact Submissions
        Route::get('/contact-submissions', [ContactSubmissionController::class, 'adminIndex'])->name('contact-submissions.index');
        Route::get('/contact-submissions/{contactSubmission}', [ContactSubmissionController::class, 'adminShow'])->name('contact-submissions.show');
        Route::delete('/contact-submissions/{contactSubmission}', [ContactSubmissionController::class, 'adminDestroy'])->name('contact-submissions.destroy');

        // Newsletter Subscriptions
        Route::get('/newsletter-subscriptions', [NewsletterSubscriptionController::class, 'adminIndex'])->name('newsletter-subscriptions.index');
        Route::delete('/newsletter-subscriptions/{newsletterSubscription}', [NewsletterSubscriptionController::class, 'adminDestroy'])->name('newsletter-subscriptions.destroy');
        Route::put('/newsletter-subscriptions/{newsletterSubscription}/toggle', [NewsletterSubscriptionController::class, 'adminToggleActive'])->name('newsletter-subscriptions.toggle');

        // Settings
        Route::get('/settings', [SettingController::class, 'adminIndex'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'adminUpdate'])->name('settings.update');
    });

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.xml');
