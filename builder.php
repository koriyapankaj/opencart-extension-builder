<?php

class Builder
{

    private $extension_name;
    private $extension_dir_name;
    private $extension_description;
    private $version;
    private $author;
    private $link;
    private $extension;

    private $prototypeDir;
    private $outputDir;
    private $sourceDirectory;
    private $destination;
    private $ouputZipFile;


    public function __construct(array $data = [])
    {

        $this->extension_name = $data['extension_name'];
        $this->extension_dir_name = $data['extension_dir_name'] ?? $data['extension_name'];
        $this->extension_description = $data['extension_description'];
        $this->version = $data['version'] ?? '1.0';
        $this->author = $data['author'];
        $this->link = $data['link'];
        $this->extension = $data['extension'];

        // Set the prototype directory and output directory
        $this->prototypeDir = 'prototypes/' . $data['extension'];
        $this->outputDir = 'temp';

        // Define source and destination
        $this->sourceDirectory = 'temp'; // Your source directory
        $this->destination = 'build/'; // Your destination directory
        $this->ouputZipFile = str_replace(' ', '_', strtolower($data['extension_name'])) . '.ocmod.zip'; // Your output zip file

    }


    private function initiate()
    {

        // Convert to lowercase, remove spaces, and capitalize the first letter of each word
        return [
            't_extension_name' => str_replace(' ', '', ucwords(strtolower($this->extension_name))),
            't_extension_dir_name' => str_replace(' ', '', ucwords(strtolower($this->extension_dir_name))),
            's_extension_name' => str_replace(' ', '_', strtolower($this->extension_name)),
            's_extension_dir_name' => str_replace(' ', '_', strtolower($this->extension_dir_name)),
            'c_extension_name' => ucwords(strtolower($this->extension_name)),
            'n_extension_name' => strtolower($this->extension_name),
            'extension_description' => $this->extension_description,
            'version' => $this->version,
            'author' => $this->author,
            'link' => $this->link,
            'extension' => $this->extension

        ];
    }


    private function getPlaceHolders($data)
    {

        // Define placeholders to replace in the files and directories
        return [
            'extension_file_name'      => $data['s_extension_name'],
            '{{t_extension_dir_name}}' => $data['t_extension_dir_name'],
            '{{t_extension_name}}'     => $data['t_extension_name'],
            '{{s_extension_dir_name}}' => $data['s_extension_dir_name'],
            '{{s_extension_name}}'     => $data['s_extension_name'],
            '{{c_extension_name}}'     => $data['c_extension_name'],
            '{{n_extension_name}}'     => $data['n_extension_name'],
            '{{name}}'                 => $data['c_extension_name'],
            '{{description}}'          => $data['extension_description'],
            '{{version}}'              => $data['version'],
            '{{author}}'               => $data['author'],
            '{{link}}'                 => $data['link'],
        ];
    }

    public function build()
    {

        $ini_array = $this->initiate();
        $placeholders = $this->getPlaceHolders($ini_array);

        // Run the script to create the extension
        $this->createExtension($this->prototypeDir, $this->outputDir, $placeholders);
        // Call the function to zip the directory
        $this->zipDirectory($this->sourceDirectory, $this->destination, $this->ouputZipFile);
        $this->deleteDirectory($this->sourceDirectory);
    }

    /**
     * Recursively copy directories and files from source to destination
     */
    private function copyDirectory($src, $dst, $placeholders = [])
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $srcFile = "$src/$file";
                $dstFile = "$dst/$file";
                if (is_dir($srcFile)) {
                    // Recursively copy subdirectories
                    $this->copyDirectory($srcFile, $dstFile, $placeholders);
                } else {
                    // Replace placeholders in the filenames and file content
                    $dstFile = str_replace(array_keys($placeholders), array_values($placeholders), $dstFile);
                    copy($srcFile, $dstFile);
                    $this->replacePlaceholdersInFile($dstFile, $placeholders);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Replace placeholders in file content
     */
    private function replacePlaceholdersInFile($filePath, $placeholders)
    {
        $content = file_get_contents($filePath);
        $content = str_replace(array_keys($placeholders), array_values($placeholders), $content);
        file_put_contents($filePath, $content);
    }

    /**
     * Main script to create OpenCart extension from prototype
     */
    private function createExtension($prototypeDir, $outputDir, $placeholders)
    {
        if (!is_dir($prototypeDir)) {
            die("Prototype directory does not exist.\n");
        }

        // Create output directory if it doesn't exist
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        // Copy prototype directory to output directory and replace placeholders
        $this->copyDirectory($prototypeDir, $outputDir, $placeholders);
    }

    /**
     * Recursively delete a directory
     */
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $filePath = "$dir/$file";
            is_dir($filePath) ? $this->deleteDirectory($filePath) : unlink($filePath);
        }
        rmdir($dir);
    }



    //create zip
    private function zipDirectory($source, $destination, $ouputZipFile)
    {

        // Create output directory if it doesn't exist
        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $destination = $destination . $ouputZipFile;

        // Create a new zip archive
        $zip = new ZipArchive();

        // Open the zip file for creation
        if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            exit("Unable to open <$destination>\n");
        }

        // Create a recursive directory iterator
        $source = realpath($source);
        if (!$source) {
            exit("Source directory does not exist.\n");
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($iterator as $file) {
            // Skip directories (they will be added automatically)
            if (!$file->isDir()) {
                // Get the real path for the current file
                $filePath = $file->getRealPath();

                // Make sure the path is relative to the source directory
                $relativePath = substr($filePath, strlen($source) + 1);

                // Add the file to the zip archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Close the zip archive
        $zip->close();

        echo "Zip file created at: $destination\n";
    }
}
