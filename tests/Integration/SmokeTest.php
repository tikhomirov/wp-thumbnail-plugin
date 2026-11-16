<?php

it('plugin file exists', function () {
    expect(file_exists(__DIR__ . '/../../wp-thumbnail-plugin.php'))->toBeTrue();
});

it('main files exist', function () {
    expect(file_exists(__DIR__ . '/../../autoload.php'))->toBeTrue();
    expect(file_exists(__DIR__ . '/../../functions.php'))->toBeTrue();
    expect(file_exists(__DIR__ . '/../../src/Infrastructure/WordPress/Plugin.php'))->toBeTrue();
});

it('functions file exists', function () {
    expect(file_exists(__DIR__ . '/../../functions.php'))->toBeTrue();
});

it('readme file exists', function () {
    expect(file_exists(__DIR__ . '/../../readme.md'))->toBeTrue();
});

it('documentation exists', function () {
    expect(file_exists(__DIR__ . '/../../docs/MODERNIZATION_COMPLETE.md'))->toBeTrue();
    expect(file_exists(__DIR__ . '/../../docs/CURRENT_API.md'))->toBeTrue();
});

it('composer json is valid', function () {
    $composerJson = file_get_contents(__DIR__ . '/../../composer.json');
    $decoded = json_decode($composerJson, true);

    expect($decoded)->not->toBeNull();
    expect($decoded['name'])->toBe('rwsite/wp-thumbnail-plugin');
    expect($decoded['require-dev'])->toHaveKey('pestphp/pest');
});

it('phpstan config exists', function () {
    expect(file_exists(__DIR__ . '/../../phpstan.neon'))->toBeTrue();
});

it('pest config exists', function () {
    expect(file_exists(__DIR__ . '/../../pest.xml'))->toBeTrue();
});

it('github workflow exists', function () {
    expect(file_exists(__DIR__ . '/../../.github/workflows/test.yml'))->toBeTrue();
});
