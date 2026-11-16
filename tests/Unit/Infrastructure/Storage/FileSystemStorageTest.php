<?php

use KamaThumb\Infrastructure\Storage\FileSystemStorage;

beforeEach(function () {
    $this->basePath = sys_get_temp_dir() . '/kama-thumb-test-' . uniqid();
    $this->baseUrl = 'https://example.com/cache';
    $this->storage = new FileSystemStorage($this->basePath, $this->baseUrl);
});

afterEach(function () {
    if (is_dir($this->basePath)) {
        $this->storage->clearCache();
        @rmdir($this->basePath);
    }
});

it('creates storage with correct paths', function () {
    expect($this->storage->path('test.jpg'))
        ->toContain($this->basePath)
        ->and($this->storage->url('test.jpg'))
        ->toContain($this->baseUrl);
});

it('checks if file exists', function () {
    expect($this->storage->exists('nonexistent.jpg'))->toBeFalse();

    $this->storage->put('test.jpg', 'content');
    expect($this->storage->exists('test.jpg'))->toBeTrue();
});

it('puts and gets file content', function () {
    $content = 'test content';
    $this->storage->put('test.jpg', $content);

    expect($this->storage->get('test.jpg'))->toBe($content);
});

it('deletes file', function () {
    $this->storage->put('test.jpg', 'content');
    expect($this->storage->exists('test.jpg'))->toBeTrue();

    $this->storage->delete('test.jpg');
    expect($this->storage->exists('test.jpg'))->toBeFalse();
});

it('returns empty string for nonexistent file', function () {
    expect($this->storage->get('nonexistent.jpg'))->toBe('');
});

it('generates correct url', function () {
    $url = $this->storage->url('subdir/test.jpg');
    expect($url)->toBe($this->baseUrl . '/subdir/test.jpg');
});

it('generates correct path', function () {
    $path = $this->storage->path('subdir/test.jpg');
    expect($path)->toBe($this->basePath . '/subdir/test.jpg');
});

it('clears cache and returns count', function () {
    $this->storage->put('file1.jpg', 'content1');
    $this->storage->put('file2.jpg', 'content2');
    $this->storage->put('subdir/file3.jpg', 'content3');

    $count = $this->storage->clearCache();
    expect($count)->toBeGreaterThanOrEqual(3);
});

it('creates directories automatically', function () {
    $this->storage->put('deep/nested/dir/test.jpg', 'content');
    expect($this->storage->exists('deep/nested/dir/test.jpg'))->toBeTrue();
});
