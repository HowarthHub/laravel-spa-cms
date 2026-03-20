<?php

namespace App\Providers;

use App\Models\CategoryModel;
use App\Models\FormModel;
use App\Models\FormSubmissionModel;
use App\Models\MediaItemModel;
use App\Models\MenuModel;
use App\Models\PageModel;
use App\Models\PostModel;
use App\Models\RedirectModel;
use App\Models\SettingModel;
use App\Models\TagModel;
use App\Models\UserModel;
use App\Repositories\CategoryRepository;
use App\Repositories\FormRepository;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\FormRepositoryInterface;
use App\Repositories\Interfaces\MediaRepositoryInterface;
use App\Repositories\Interfaces\MenuItemRepositoryInterface;
use App\Repositories\Interfaces\MenuRepositoryInterface;
use App\Repositories\Interfaces\PageRepositoryInterface;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\TagRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\MediaRepository;
use App\Repositories\MenuItemRepository;
use App\Repositories\MenuRepository;
use App\Repositories\PageRepository;
use App\Repositories\PostRepository;
use App\Repositories\SettingRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use App\Services\CategoryService;
use App\Services\FormService;
use App\Services\Interfaces\CategoryServiceInterface;
use App\Services\Interfaces\FormServiceInterface;
use App\Services\Interfaces\MediaServiceInterface;
use App\Services\Interfaces\MenuServiceInterface;
use App\Services\Interfaces\PageServiceInterface;
use App\Services\Interfaces\PostServiceInterface;
use App\Services\Interfaces\SettingServiceInterface;
use App\Services\Interfaces\SlugServiceInterface;
use App\Services\Interfaces\TagServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\MediaService;
use App\Services\MenuService;
use App\Services\PageService;
use App\Services\PostService;
use App\Services\SettingService;
use App\Services\SlugService;
use App\Services\TagService;
use App\Services\UserService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

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
        $this->app->bind(FormRepositoryInterface::class, FormRepository::class);
        $this->app->bind(MediaRepositoryInterface::class, MediaRepository::class);
        $this->app->bind(SettingRepositoryInterface::class, SettingRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        // Services
        $this->app->bind(PageServiceInterface::class, PageService::class);
        $this->app->bind(PostServiceInterface::class, PostService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(TagServiceInterface::class, TagService::class);
        $this->app->bind(MenuServiceInterface::class, MenuService::class);
        $this->app->bind(FormServiceInterface::class, FormService::class);
        $this->app->bind(MediaServiceInterface::class, MediaService::class);
        $this->app->bind(SettingServiceInterface::class, SettingService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(SlugServiceInterface::class, SlugService::class);
    }

    public function boot(): void
    {
        Route::model('page', PageModel::class);
        Route::model('post', PostModel::class);
        Route::model('category', CategoryModel::class);
        Route::model('tag', TagModel::class);
        Route::model('menu', MenuModel::class);
        Route::model('form', FormModel::class);
        Route::model('formSubmission', FormSubmissionModel::class);
        Route::model('user', UserModel::class);
        Route::model('mediaItem', MediaItemModel::class);
        Route::model('redirect', RedirectModel::class);

        $this->configureMailFromSettings();
    }

    private function configureMailFromSettings(): void
    {
        try {
            $mail = SettingModel::where('group', 'mail')->pluck('value', 'key');
        } catch (\Throwable) {
            return;
        }

        if ($mail->isEmpty()) {
            return;
        }

        if ($mail->get('host')) {
            Config::set('mail.default', $mail->get('driver', 'smtp'));
            Config::set('mail.mailers.smtp.host', $mail->get('host'));
            Config::set('mail.mailers.smtp.port', (int) $mail->get('port', 587));
            Config::set('mail.mailers.smtp.username', $mail->get('username'));
            Config::set('mail.mailers.smtp.password', $mail->get('password'));
            Config::set('mail.mailers.smtp.encryption', ((int) $mail->get('port', 587)) === 465 ? 'ssl' : 'tls');
        }

        if ($mail->get('from_email')) {
            Config::set('mail.from.address', $mail->get('from_email'));
        }

        if ($mail->get('from_name')) {
            Config::set('mail.from.name', $mail->get('from_name'));
        }
    }
}
