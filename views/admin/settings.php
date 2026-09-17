<?php
/**
 * @var \KamaThumb\Domain\Contracts\ImageProcessorInterface $processor
 * @var \KamaThumb\Domain\Contracts\StorageInterface $storage
 * @var array<string, mixed> $options
 * @var string $noPhotoPreview
 * @var bool $settings_saved
 */
defined('ABSPATH') || exit;

$cache_cleared = isset($_GET['cache_cleared']) ? (int) $_GET['cache_cleared'] : 0;
?>

<div class="wrap">
    <h1><?php echo esc_html__('Thumbnail Settings', 'thumbnail'); ?></h1>

    <?php if ($settings_saved) { ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html__('Settings saved.', 'thumbnail'); ?></p>
        </div>
    <?php } ?>

    <?php if ($cache_cleared > 0) { ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo sprintf(esc_html__('Cache cleared! %d files deleted.', 'thumbnail'), $cache_cleared); ?></p>
        </div>
    <?php } ?>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="card" style="max-width:960px;padding:16px 20px;">
        <?php wp_nonce_field('kama_thumb_save_settings'); ?>
        <input type="hidden" name="action" value="kama_thumb_save_settings">

        <h2><?php echo esc_html__('General Settings', 'thumbnail'); ?></h2>
        <p class="description">
            <?php echo esc_html__('Stored in the kama_thumbnail option. Used by get_post_thumbnail() and thumb_* functions.', 'thumbnail'); ?>
        </p>

        <table class="form-table" role="presentation">
            <tr>
                <th scope="row">
                    <label for="kama_thumb_no_photo_url"><?php echo esc_html__('No image placeholder', 'thumbnail'); ?></label>
                </th>
                <td>
                    <input
                        type="text"
                        class="regular-text"
                        id="kama_thumb_no_photo_url"
                        name="no_photo_url"
                        value="<?php echo esc_attr((string) ($options['no_photo_url'] ?? '')); ?>"
                        placeholder="<?php echo esc_attr__('Attachment ID or image URL', 'thumbnail'); ?>"
                    >
                    <p>
                        <button type="button" class="button" id="kama-thumb-no-photo-select">
                            <?php echo esc_html__('Select from Media Library', 'thumbnail'); ?>
                        </button>
                        <button type="button" class="button-link-delete" id="kama-thumb-no-photo-clear">
                            <?php echo esc_html__('Clear', 'thumbnail'); ?>
                        </button>
                    </p>
                    <p class="description">
                        <?php echo esc_html__('Shown when a post has no featured image. Attachment ID or direct URL.', 'thumbnail'); ?>
                    </p>
                    <?php if ($noPhotoPreview !== '') { ?>
                        <p><img id="kama-thumb-no-photo-preview" src="<?php echo esc_url($noPhotoPreview); ?>" alt="" style="max-width:240px;height:auto;border:1px solid #ccd0d4;border-radius:4px;"></p>
                    <?php } else { ?>
                        <p><img id="kama-thumb-no-photo-preview" src="" alt="" style="display:none;max-width:240px;height:auto;border:1px solid #ccd0d4;border-radius:4px;"></p>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="kama_thumb_quality"><?php echo esc_html__('JPEG quality', 'thumbnail'); ?></label>
                </th>
                <td>
                    <input
                        type="number"
                        class="small-text"
                        id="kama_thumb_quality"
                        name="quality"
                        min="0"
                        max="100"
                        value="<?php echo esc_attr((string) ($options['quality'] ?? 90)); ?>"
                    >
                    <p class="description"><?php echo esc_html__('Default quality for generated thumbnails (0–100).', 'thumbnail'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html__('WebP output', 'thumbnail'); ?></th>
                <td>
                    <label for="kama_thumb_webp">
                        <input type="checkbox" id="kama_thumb_webp" name="webp" value="1" <?php checked(! empty($options['webp'])); ?>>
                        <?php echo esc_html__('Generate thumbnails in WebP format by default', 'thumbnail'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html__('Placeholder behavior', 'thumbnail'); ?></th>
                <td>
                    <label for="kama_thumb_no_stub">
                        <input type="checkbox" id="kama_thumb_no_stub" name="no_stub" value="1" <?php checked(! empty($options['no_stub'])); ?>>
                        <?php echo esc_html__('Do not show placeholder image (return empty output)', 'thumbnail'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="kama_thumb_allow_hosts"><?php echo esc_html__('Allowed external hosts', 'thumbnail'); ?></label>
                </th>
                <td>
                    <input
                        type="text"
                        class="large-text"
                        id="kama_thumb_allow_hosts"
                        name="allow_hosts"
                        value="<?php echo esc_attr((string) ($options['allow_hosts'] ?? '')); ?>"
                        placeholder="example.com, cdn.example.com"
                    >
                    <p class="description">
                        <?php echo esc_html__('Comma-separated hostnames. Use "any" to allow all external images.', 'thumbnail'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html__('Upscale small images', 'thumbnail'); ?></th>
                <td>
                    <label for="kama_thumb_rise_small">
                        <input type="checkbox" id="kama_thumb_rise_small" name="rise_small" value="1" <?php checked(! empty($options['rise_small'])); ?>>
                        <?php echo esc_html__('Increase thumbnail size if source is smaller than requested dimensions', 'thumbnail'); ?>
                    </label>
                    <p class="description"><?php echo esc_html__('Legacy option; reserved for future use in this version.', 'thumbnail'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html__('Auto cache cleanup', 'thumbnail'); ?></th>
                <td>
                    <label for="kama_thumb_auto_clear">
                        <input type="checkbox" id="kama_thumb_auto_clear" name="auto_clear" value="1" <?php checked(! empty($options['auto_clear'])); ?>>
                        <?php echo esc_html__('Enable automatic cache cleanup', 'thumbnail'); ?>
                    </label>
                    <p>
                        <label for="kama_thumb_auto_clear_days"><?php echo esc_html__('Every', 'thumbnail'); ?></label>
                        <input
                            type="number"
                            class="small-text"
                            id="kama_thumb_auto_clear_days"
                            name="auto_clear_days"
                            min="1"
                            value="<?php echo esc_attr((string) ($options['auto_clear_days'] ?? 7)); ?>"
                        >
                        <?php echo esc_html__('days', 'thumbnail'); ?>
                    </p>
                    <p class="description"><?php echo esc_html__('Legacy option; reserved for future cron integration.', 'thumbnail'); ?></p>
                </td>
            </tr>
        </table>

        <?php submit_button(__('Save Settings', 'thumbnail')); ?>
    </form>

    <div class="card">
        <h2><?php echo esc_html__('System Information', 'thumbnail'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php echo esc_html__('Image Processor', 'thumbnail'); ?></th>
                <td>
                    <strong><?php echo esc_html(get_class($processor)); ?></strong>
                    <p class="description">
                        <?php echo $processor->isAvailable()
                            ? esc_html__('Available', 'thumbnail')
                            : esc_html__('Not available', 'thumbnail'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html__('Supported Formats', 'thumbnail'); ?></th>
                <td>
                    <?php echo esc_html(implode(', ', $processor->getSupportedFormats())); ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html__('Cache Directory', 'thumbnail'); ?></th>
                <td>
                    <code><?php echo esc_html($storage->path('')); ?></code>
                    <p class="description">
                        <?php echo is_writable($storage->path(''))
                            ? esc_html__('Writable', 'thumbnail')
                            : esc_html__('Not writable', 'thumbnail'); ?>
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h2><?php echo esc_html__('Cache Management', 'thumbnail'); ?></h2>
        <p><?php echo esc_html__('Clear all cached thumbnails. They will be regenerated on demand.', 'thumbnail'); ?></p>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('kama_thumb_clear_cache'); ?>
            <input type="hidden" name="action" value="kama_thumb_clear_cache">
            <button type="submit" class="button button-secondary">
                <?php echo esc_html__('Clear Cache', 'thumbnail'); ?>
            </button>
        </form>
    </div>

    <div class="card">
        <h2><?php echo esc_html__('Usage Examples', 'thumbnail'); ?></h2>
        <h3><?php echo esc_html__('New API (Recommended)', 'thumbnail'); ?></h3>
        <pre><code>&lt;?php
$url = thumb_src(['width' => 300, 'height' => 200, 'crop' => true], get_post_thumbnail_id());
echo thumb_img(['width' => 300, 'height' => 200, 'class' => 'img-fluid'], get_post_thumbnail_id());
?&gt;</code></pre>

        <h3><?php echo esc_html__('Legacy API (Still Supported)', 'thumbnail'); ?></h3>
        <pre><code>&lt;?php
echo kama_thumb_src(['width' => 300, 'height' => 200]);
echo kama_thumb_img(['width' => 300, 'height' => 200]);
?&gt;</code></pre>
    </div>
</div>
