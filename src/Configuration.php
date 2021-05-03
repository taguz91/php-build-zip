<?php

namespace taguz91\PhpBuild;

use Symfony\Component\Yaml\Yaml;

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
        self::FILE_NAME => 'php-build',
        self::DESTINATION_FOLDER => 'build'
    ];

    /** @var string File configuration extension */
    const FILE_EXTENSION = '.yml';

    /** @var string */
    public $currentDirectory;

    /** @var array */
    public $config;

    /** @var Writer */
    public $writer;

    public $zipName;

    /** @var string[] */
    public $folders;

    /** @var string[] */
    public $files;

    /** @var string[] */
    private $ignoreFolders;

    /** @var string[] */
    private $ignoreFiles;

    /** @var string */
    private $filesRegularExpresion;

    /** @var string */
    private $foldersRegularExpresion;

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
        // Loading the yml configuration 
        $this->loadYml();
        $this->loadRegularExpresion();
    }

    private function loadYml()
    {
        $ymlFilePath = $this->currentDirectory . $this->config[self::FILE_NAME] . self::FILE_EXTENSION;
        $this->writer->print("Find configuration file: {$ymlFilePath}");
        if (file_exists($ymlFilePath)) {
            $ymlConfiguration = Yaml::parseFile($ymlFilePath);
            $this->writer->success('Loading the yml file');
            $this->folders = $ymlConfiguration['folder']['include'] ?? [];
            $this->files = $ymlConfiguration['file']['include'] ?? [];
            $this->ignoreFolders = $ymlConfiguration['folder']['ignore'] ?? [];
            $this->ignoreFiles = $ymlConfiguration['file']['ignore'] ?? [];
            // Print the corresponding messages
            $this->writer->success('Loading complete: ');
            // Including files
            $this->writer->print("Including the following files: " . implode(', ', $this->files));
            $this->writer->print("Including the following folder: " . implode(', ', $this->folders));
            // Ignore files 
            $this->writer->print("Ignore the following files: " . implode(', ', $this->ignoreFiles));
            $this->writer->print("Ignore the following folders: " . implode(', ', $this->ignoreFolders));
        } else {
            $this->writer->error('File configuration not found. Please create your configuration file {php-build.yml}');
            exit(1);
        }
    }

    private function loadRegularExpresion()
    {
        $extensions = implode('|', $this->ignoreFiles);
        $extensions = str_replace('*.', '', $extensions);
        $this->filesRegularExpresion = $extensions;

        // Loading folders to ignore 
        $this->foldersRegularExpresion = implode('|', $this->ignoreFolders);
    }

    public function ignoreFile(string $filePath): bool
    {
        $this->writer->print("Checking if the file is ignore {$filePath}");
        $extensions = $this->filesRegularExpresion;
        $folders = $this->foldersRegularExpresion;

        return preg_match("/^.*\.({$extensions})$/i", $filePath) || preg_match("/{$folders}/i", $filePath);
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
