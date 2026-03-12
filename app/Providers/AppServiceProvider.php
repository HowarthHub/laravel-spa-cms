<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repository Interfaces
use App\Repositories\Interfaces\PageRepositoryInterface;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\TagRepositoryInterface;
use App\Repositories\Interfaces\MenuRepositoryInterface;
use App\Repositories\Interfaces\MenuItemRepositoryInterface;
use App\Repositories\Interfaces\EnquiryRepositoryInterface;
use App\Repositories\Interfaces\MediaRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;

// Repository Implementations
use App\Repositories\PageRepository;
use App\Repositories\PostRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Repositories\MenuRepository;
use App\Repositories\MenuItemRepository;
use App\Repositories\EnquiryRepository;
use App\Repositories\MediaRepository;
use App\Repositories\SettingRepository;
use App\Repositories\UserRepository;

// Service Interfaces
use App\Services\Interfaces\PageServiceInterface;
use App\Services\Interfaces\PostServiceInterface;
use App\Services\Interfaces\CategoryServiceInterface;
use App\Services\Interfaces\TagServiceInterface;
use App\Services\Interfaces\MenuServiceInterface;
use App\Services\Interfaces\EnquiryServiceInterface;
use App\Services\Interfaces\MediaServiceInterface;
use App\Services\Interfaces\SettingServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Interfaces\SlugServiceInterface;

// Service Implementations
use App\Services\PageService;
use App\Services\PostService;
use App\Services\CategoryService;
use App\Services\TagService;
use App\Services\MenuService;
use App\Services\EnquiryService;
use App\Services\MediaService;
use App\Services\SettingService;
use App\Services\UserService;
use App\Services\SlugService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repositories
        $this->app->bind(PageRepositoryInterface::class, PageRepository::class);
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(TagRepositoryInterface::class, TagRepository::class);
        $this->app->bind(MenuRepositoryInterface::class, MenuRepository::class);
        $this->app->bind(MenuItemRepositoryInterface::class, MenuItemRepository::class);
        $this->app->bind(EnquiryRepositoryInterface::class, EnquiryRepository::class);
        $this->app->bind(MediaRepositoryInterface::class, MediaRepository::class);
        $this->app->bind(SettingRepositoryInterface::class, SettingRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        // Services
        $this->app->bind(PageServiceInterface::class, PageService::class);
        $this->app->bind(PostServiceInterface::class, PostService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(TagServiceInterface::class, TagService::class);
        $this->app->bind(MenuServiceInterface::class, MenuService::class);
        $this->app->bind(EnquiryServiceInterface::class, EnquiryService::class);
        $this->app->bind(MediaServiceInterface::class, MediaService::class);
        $this->app->bind(SettingServiceInterface::class, SettingService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(SlugServiceInterface::class, SlugService::class);
    }

    public function boot(): void
    {
        // Route model bindings for custom model names
        \Illuminate\Support\Facades\Route::model('page', \App\Models\PageModel::class);
        \Illuminate\Support\Facades\Route::model('post', \App\Models\PostModel::class);
        \Illuminate\Support\Facades\Route::model('category', \App\Models\CategoryModel::class);
        \Illuminate\Support\Facades\Route::model('tag', \App\Models\TagModel::class);
        \Illuminate\Support\Facades\Route::model('menu', \App\Models\MenuModel::class);
        \Illuminate\Support\Facades\Route::model('enquiry', \App\Models\ContactEnquiryModel::class);
        \Illuminate\Support\Facades\Route::model('user', \App\Models\UserModel::class);
    }
}
