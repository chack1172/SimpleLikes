<?php

namespace MybbStuff\SimpleLikes\Import;

/**
 * Importer manager class that allows the registering of custom importers extending the AbstractImporter class.
 *
 * @package Simple Likes
 * @author  Euan T. <euan@euantor.com>
 * @license http://opensource.org/licenses/mit-license.php MIT license
 * @version 1.4.0
 */
class Manager
{
    /**
     * Singleton instance.
     *
     * @var Manager $instance
     */
    private static $instance;

    /**
     * @var \DB_MySQLi $db
     */
    private $db;

    /**
     * @var array $importers
     */
    private $importers;

    private function __construct(\DB_MySQLi $db)
    {
        $this->db        = $db;
        $this->importers = array();
    }

    /**
     * Get an instance of the import manager.
     *
     * @return Manager The singleton instance.
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            global $db;

            static::$instance = new static($db);
        }

        return static::$instance;
    }

    /**
     * Add an importer to the manager.
     *
     * @param string $importerClass The importer class to be added.
     * @throws \InvalidArgumentException Thrown if $importerClass does not exist or doesn't extend AbstractImporter.
     */
    public function addImporter($importerClass = '')
    {
        $importerClass = (string) $importerClass;

        if (class_exists($importerClass)) {
            $instance = new $importerClass($this->db);
            if ($instance instanceof AbstractImporter) {
                $this->importers[] = $importerClass;

                return;
            }
        }

        throw new \InvalidArgumentException('$importerClass should be a valid class name that extends AbstractImporter');
    }

    /**
     * Get all of the registered importers.
     *
     * @return array
     */
    public function getImporters()
    {
        $importers = array();

        foreach ($this->importers as $importer) {
            $importers[] = new $importer($this->db);
        }

        return $importers;
    }
}