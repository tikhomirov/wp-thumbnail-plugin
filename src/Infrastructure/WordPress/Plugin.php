<?php

namespace KamaThumb\Infrastructure\WordPress;

final class Plugin
{
    protected static ?self $instance = null;

    protected ServiceContainer $container;

    protected function __construct()
    {
        $this->container = ServiceContainer::getInstance();
        $this->registerHooks();
        TemplateFunctions::register();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    protected function registerHooks(): void
    {
        if (is_admin()) {
            add_action('admin_menu', [$this, 'registerAdminMenu']);
            add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
            add_action('admin_post_kama_thumb_save_settings', [$this, 'handleSaveSettings']);
            add_action('admin_post_kama_thumb_clear_cache', [$this, 'handleClearCache']);
        }

        add_filter('jpeg_quality', fn () => 100);
    }

    public function registerAdminMenu(): void
    {
        add_submenu_page(
            'options-general.php',
            __('Thumbnail Settings', 'thumbnail'),
            __('Thumbnails', 'thumbnail'),
            'manage_options',
            'kama-thumbnail-settings',
            [$this, 'renderSettingsPage']
        );
    }

    public function renderSettingsPage(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        $processor = $this->container->getImageProcessor();
        $storage = $this->container->getStorage();
        $options = Settings::get();
        $noPhotoPreview = Settings::resolveNoPhotoUrl((string) ($options['no_photo_url'] ?? ''));
        $settings_saved = isset($_GET['settings_saved']);

        include __DIR__.'/../../../views/admin/settings.php';
    }

    public function enqueueAdminAssets(string $hook): void
    {
        if ($hook !== 'settings_page_kama-thumbnail-settings') {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script(
            'kama-thumb-settings',
            plugins_url('assets/js/admin-settings.js', KT_MAIN_FILE),
            ['jquery'],
            filemtime(KT_PATH.'assets/js/admin-settings.js'),
            true
        );
    }

    public function handleSaveSettings(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'thumbnail'));
        }

        check_admin_referer('kama_thumb_save_settings');

        Settings::save(wp_unslash($_POST));

        wp_redirect(add_query_arg([
            'page'           => 'kama-thumbnail-settings',
            'settings_saved' => 1,
        ], admin_url('options-general.php')));
        exit;
    }

    public function handleClearCache(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'thumbnail'));
        }

        check_admin_referer('kama_thumb_clear_cache');

        $storage = $this->container->getStorage();
        $count = $storage->clearCache();

        wp_redirect(add_query_arg([
            'page'          => 'kama-thumbnail-settings',
            'cache_cleared' => $count,
        ], admin_url('options-general.php')));
        exit;
    }

    public function getContainer(): ServiceContainer
    {
        return $this->container;
    }
}
