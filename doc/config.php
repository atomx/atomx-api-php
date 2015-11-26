<?php

use Sami\Sami;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in($dir = __DIR__.'/../Atomx')
;

$versions = GitVersionCollection::create($dir)
    ->add('master', 'master branch')
;

return new Sami($iterator, array(
    'theme'                => 'default',
    'versions'             => $versions,
    'title'                => 'Atomx API',
    'build_dir'            => __DIR__.'/build/%version%',
    'cache_dir'            => __DIR__.'/cache/%version%',
    'remote_repository'    => new GitHubRemoteRepository('atomx/atomx-api-php', dirname($dir)),
    'default_opened_level' => 2,
));

