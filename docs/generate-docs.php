<?php

use Aptoma\Twig\Extension\MarkdownExtension;
use Aptoma\Twig\Extension\MarkdownEngine;
use Cocur\Slugify\Slugify;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

require_once __DIR__.'/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__);
$twig = new \Twig\Environment($loader);

$icons = new Feather\Icons();
$function = new \Twig\TwigFunction('feather', function (string $featherIconName, array $options = []) use ($icons) {
    return $icons->get($featherIconName, $options);
});

$engine = new MarkdownEngine\ParsedownEngine();
$twig->addFunction($function);
$twig->addExtension(new MarkdownExtension($engine));


$finder = new Finder();
$finder->in('content');
$finder->sortByName();

$slugify = new Slugify();
$filesTree = [];
$menu = [];
foreach ($finder as $item) {
    $depth = substr_count($item->getPathname(), DIRECTORY_SEPARATOR);
    $normalizedFilename = preg_replace('/\d\d_/' , '', $item->getFilename());

    if ($item->isDir()) {
        $menuItem = [
            'depth' => $depth,
            'isDir' => true,
            'label' => str_replace('_', ' ', $normalizedFilename)
        ];

        $menu[] = $menuItem;

        continue;
    }

    $normalizePath = preg_replace('/\d\d_/' , '', $item->getRelativePathname());
    $htmlPath = str_replace('.md', '', $normalizePath);

    $pathComponents = explode(DIRECTORY_SEPARATOR, $htmlPath);
    $pathComponents = array_map(function ($component) use ($slugify) {
        return $slugify->slugify($component);
    }, $pathComponents);

    $htmlPath = 'docs/'.implode(DIRECTORY_SEPARATOR, $pathComponents).'.html';
    $menuItem = [
        'depth' => $depth,
        'isDir' => false,
        'label' => str_replace(['_', '.md'], [' ', ' '], $normalizedFilename),
        'href' => $htmlPath,
    ];
    $menu[] = $menuItem;

    $baseDir = str_repeat('../', $depth);
    $filesTree[] = [
        'depth' => $depth,
        'theme_dir' => $baseDir.'theme',
        'base_dir' => $baseDir,
        'isDir' => $item->isDir(),
        'page_title' => $menuItem['label'],
        'md_path' => $item->getPathname(),
        'html_path' => $htmlPath
    ];

}
$fs = new Filesystem();
foreach ($filesTree as $item) {
    $fs->dumpFile(
        $item['html_path'],
        $twig->render(
            'layout.html.twig',
            [
                'markdown_content' => $item['md_path'],
                'theme_dir' => $item['theme_dir'],
                'base_dir' => $item['base_dir'],
                'menu' => $menu,
                'github_edit_url' => 'https://github.com/tigitz/php-spellchecker/edit/master/docs/'.$item['md_path'],
                'page_title' => $item['page_title'],
            ]
        )
    );
}

$fs->copy('docs/getting-started/what-is-php-spellcheck.html', 'index.html');
