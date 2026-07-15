#!/usr/bin/env php
<?php

declare(strict_types=1);

$gitName = run('git config user.name');
$authorName = ask('Author name', $gitName);

$gitEmail = run('git config user.email');
$authorEmail = ask('Author email', $gitEmail);

$usernameGuess = explode(':', run('git config remote.origin.url'))[1] ?? '';

if ($usernameGuess !== '') {
    $usernameGuess = dirname($usernameGuess);
    $usernameGuess = basename($usernameGuess);
}

$authorUsername = ask('Author username', $usernameGuess);

$vendorName = ask('Vendor name', $authorUsername);
$vendorSlug = slugify($vendorName);
$vendorNamespace = str_replace('-', '', ucwords($vendorName));
$vendorNamespace = ask('Vendor namespace', $vendorNamespace);

$currentDirectory = getcwd();
$folderName = basename($currentDirectory);

$packageName = ask('Package name', $folderName);
$packageSlug = slugify($packageName);

$className = titleCase($packageName);
$className = ask('Class name', $className);
$tableName = titleSnake($packageSlug);
$description = ask('Package description', "The {$packageName} addon for Shopper");

writeln("\r");
writeln('------');
writeln("Author     : \e[0;36m{$authorName} ({$authorUsername}, {$authorEmail})\e[0m");
writeln("Vendor     : \e[0;36m{$vendorName} ({$vendorSlug})\e[0m");
writeln('Package    : '."\e[0;36m".$packageSlug.($description ? " <{$description}>" : '')."\e[0m");
writeln("Namespace  : \e[0;36m{$vendorNamespace}\\{$className}\e[0m");
writeln("Class name : \e[0;36m{$className}Addon\e[0m");
writeln("Command    : \e[0;36mphp artisan shopper:{$packageSlug}:install\e[0m");
writeln("Permissions: \e[0;36m{$tableName}.browse / create / edit / delete\e[0m");
writeln('------');
writeln("\r");
writeln('This script will replace the above values in all relevant files in the project directory.');
writeln("\r");

if (! confirm('Modify files?', true)) {
    exit(1);
}

$files = (str_starts_with(mb_strtoupper(PHP_OS), 'WIN') ? replaceForWindows() : replaceForAllOtherOSes());

foreach ($files as $file) {
    replaceInFile($file, [
        ':author_name' => $authorName,
        ':author_username' => $authorUsername,
        'author@domain.com' => $authorEmail,
        ':vendor_name' => $vendorName,
        ':vendor_slug' => $vendorSlug,
        'VendorName' => $vendorNamespace,
        ':package_name' => $packageName,
        ':package_slug' => $packageSlug,
        ':package_description' => $description,
        'Skeleton' => $className,
        'skeleton-slug' => $packageSlug,
        'skeleton_snake' => $tableName,
    ]);

    match (true) {
        str_contains($file, determineSeparator('src/SkeletonAddon.php')) => rename($file, determineSeparator('./src/'.$className.'Addon.php')),
        str_contains($file, determineSeparator('src/SkeletonServiceProvider.php')) => rename($file, determineSeparator('./src/'.$className.'ServiceProvider.php')),
        str_contains($file, determineSeparator('src/Livewire/SkeletonIndex.php')) => rename($file, determineSeparator('./src/Livewire/'.$className.'Index.php')),
        str_contains($file, determineSeparator('src/Sidebar/SkeletonSidebar.php')) => rename($file, determineSeparator('./src/Sidebar/'.$className.'Sidebar.php')),
        str_contains($file, determineSeparator('config/skeleton_snake.php')) => rename($file, determineSeparator('./config/'.$tableName.'.php')),
        str_contains($file, determineSeparator('resources/views/livewire/pages/skeleton-slug-index.blade.php')) => rename($file, determineSeparator('./resources/views/livewire/pages/'.$packageSlug.'-index.blade.php')),
        str_contains($file, 'create_skeleton_snake_table.php') => rename($file, str_replace('skeleton_snake', $tableName, $file)),
        str_contains($file, 'README.md') => removeTag($file, 'delete'),
        default => [],
    };
}

confirm('Execute `composer install`?') && run('composer install');

if (confirm('Let this script delete itself?', true)) {
    unlink(__FILE__);
}

function ask(string $question, string $default = ''): string
{
    fwrite(STDOUT, "\e[0;32m{$question}");

    if ($default !== '') {
        fwrite(STDOUT, "\e[0;33m ({$default})");
    }

    fwrite(STDOUT, "\e[0m: ");

    $answer = mb_trim((string) fgets(STDIN));

    if ($answer === '') {
        return $default;
    }

    return $answer;
}

function confirm(string $question, bool $default = false): bool
{
    $answer = ask($question, ($default ? 'Y/n' : 'y/N'));

    if (mb_strtolower($answer) === 'y/n') {
        return $default;
    }

    return mb_strtolower($answer) === 'y';
}

function writeln(string $line): void
{
    echo $line.PHP_EOL;
}

function run(string $command): string
{
    return mb_trim((string) shell_exec($command));
}

function slugify(string $subject): string
{
    return mb_strtolower(mb_trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', $subject), '-'));
}

function titleCase(string $subject): string
{
    return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $subject)));
}

function titleSnake(string $subject, string $replace = '_'): string
{
    return str_replace(['-', '_'], $replace, $subject);
}

function replaceInFile(string $file, array $replacements): void
{
    $contents = file_get_contents($file);

    file_put_contents(
        $file,
        str_replace(
            array_keys($replacements),
            array_values($replacements),
            $contents,
        ),
    );
}

function removeTag(string $file, string $tag): void
{
    $contents = file_get_contents($file);

    file_put_contents(
        $file,
        preg_replace('/<!--'.$tag.'-->.*<!--\/'.$tag.'-->/s', '', $contents) ?: $contents,
    );
}

function safeUnlink(string $filename): void
{
    if (file_exists($filename) && is_file($filename)) {
        unlink($filename);
    }
}

function determineSeparator(string $path): string
{
    return str_replace('/', DIRECTORY_SEPARATOR, $path);
}

function replaceForWindows(): array
{
    return preg_split('/\\r\\n|\\r|\\n/', run('dir /S /B * | findstr /v /i .git\ | findstr /v /i \\vendor\\ | findstr /v /i '.basename(__FILE__).' | findstr /r /i /M /F:/ ":author :vendor :package VendorName skeleton author@domain.com"'));
}

function replaceForAllOtherOSes(): array
{
    return explode(PHP_EOL, run('find ./* ./.github/* -name "vendor" -type d -prune \
     -o -name "configure.php" -prune \
     -o -type f -print0 | xargs -0 grep -E -r -l -i ":author|:vendor|:package|VendorName|skeleton|author@domain.com"'));
}
