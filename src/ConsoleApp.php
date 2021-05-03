<?php

namespace taguz91\PhpBuild;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class ConsoleApp
{

    /** @var Writer */
    public $writer;

    /** @var Configuration */
    public $config;

    public function __construct()
    {
        $this->writer = new Writer();
        $this->config = new Configuration();
    }

    protected function init()
    {
        $this->writer
            ->success('Running -> PHP BUILD ZIP')
            ->print('');
    }

    public function run(array $args): int
    {
        $this->init();
        $this->writer->print('Your base path is: ' . __DIR__);
        $this->writer->print('Current directory is: ' . getcwd() . DIRECTORY_SEPARATOR)
            ->print('');
        $this->config->load($this->loadArgs($args));

        $zip = new ZipArchive();
        $zip->open($this->config->getDestinationDir(), ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $folder = $this->config->currentDirectory . 'app';
        $this->writer->print("Adding the following file: {$folder}")
            ->print('');

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $filename => $file) {
            if (in_array($filename, ['.', '..'])) continue;
            $filePath = $file->getRealPath();

            if (is_dir($filePath)) {
                $this->writer->error("Ignore empty dir: {$filename}");
            } else if (file_exists($filePath)) {
                $this->writer->print("Real file path:     {$filePath}");
                $relativePath = substr($filename, strlen($folder) + 1);
                $zip->addFile($filePath, $relativePath);
            } else {
                $this->writer->error("Not found file:     {$filename} -> {$filePath}");
            }
        }
        $zip->close();
        $this->writer->print('')
            ->success('Creating the following file:')
            ->success("-> {$this->config->zipName}");
        return 0;
    }

    private function loadArgs(array $args)
    {
        unset($args[0]);
        $params = [];

        foreach ($args as $arg) {
            if (strpos($arg, ':') !== false) {
                list($config, $value) = explode(':', $arg);
                $config = str_replace('--', '', $config);
                $params[$config] = $value;

                $this->writer->print("Loading the following configuration: {$config}");
            }
        }

        return $params;
    }
}
