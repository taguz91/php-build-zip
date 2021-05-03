<?php

namespace taguz91\PhpBuild;

final class Configuration
{
    /** @var string */
    const VERSION = 'version';

    /** @var string */
    const FILE_NAME = 'config';

    /** @var string */
    const DESTINATION_FOLDER = 'destination';

    const DEFAULT = [
        self::VERSION => '1.0.0',
        self::FILE_NAME => 'php-build.yml',
        self::DESTINATION_FOLDER => 'build'
    ];

    /** @var string */
    public $currentDirectory;

    /** @var array */
    public $config;

    /** @var Writer */
    public $writer;

    public $zipName;

    public function __construct()
    {
        $this->writer = new Writer();

        $this->currentDirectory = getcwd() . DIRECTORY_SEPARATOR;
        $this->writer->success("Loading the currect directory: {$this->currentDirectory}");
        $this->zipName = date('YmdHms') . '.zip';
    }

    public function load(array $configuration)
    {
        $config = array_merge(self::DEFAULT, $configuration);
        $this->config = $config;
    }

    public function getDestinationDir()
    {
        $directory = $this->currentDirectory . $this->config[self::DESTINATION_FOLDER];
        if (!file_exists($directory)) {
            $this->writer->error('Directory not exists');
            $this->writer->print("Creating the following directory {$directory}");
            mkdir($directory, 007, true);
            $this->writer->success("The directory was created");
        }
        return $directory . DIRECTORY_SEPARATOR . $this->zipName;
    }
}
