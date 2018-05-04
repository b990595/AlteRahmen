<?php

namespace Jblib\File;

use Exception;
use Jblib\Std\Io\FileUtils;
use MySqlDB;
use Zend\Stdlib\ErrorHandler;

/**
 * @author jmn
 */
class DbFile
{

    /**
     * @var MySqlDB
     */
    private $db;

    /**
     * @var callable[]
     */
    private $renames = [];

    /**
     * @var callable[]
     */
    private $deletes = [];

    public function __construct(MySqlDB $db)
    {
        $this->db = $db;
    }

    /**
     * Rename file on commit
     *
     * @param string $from
     * @param string $to
     * @param string $event
     */
    public function rename(string $from, string $to, string $event = MySqlDB::EVENT_COMMIT)
    {
        $this->renames += $this->renameFile($from, $to, $event);
    }

    /**
     * Rename file on commit
     *
     * @param string $file
     * @param string $event
     */
    public function delete(string $file, string $event = MySqlDB::EVENT_POST_COMMIT)
    {
        $this->deletes += $this->deleteFile($file, $event);
    }

    /**
     * @param string $from
     * @param string $to
     * @param string $event
     * @return callable
     */
    protected function renameFile(string $from, string $to, string $event = MySqlDB::EVENT_COMMIT)
    {
        // Move the file right before the commit
        $listener = $this->db->getEventManager()->attach($event, function() use ($from, $to) {
            if (!isset($this->renames[$from])) {
                // Already moved
                return;
            }

            // Detach self (only move the file once)
            $this->db->getEventManager()->detach($this->renames[$from]);
            unset($this->renames[$from]);

            // Make sure the directory exists
            FileUtils::createDir(dirname($to));

            try {
                ErrorHandler::start();
                $moved = move_uploaded_file($from, $to);
                if (!$moved) {
                    throw new Exception("Uploaded file '$from' could not be moved.");
                }
                ErrorHandler::stop(true);
            }
            catch (Exception $e) {
                ErrorHandler::start();
                $result = rename($from, $to);
                $warningException = ErrorHandler::stop();

                if (false === $result || null !== $warningException) {
                    throw new Exception(
                        sprintf('File "%s" could not be renamed. An error occurred while processing the file.', $from), 0, $warningException
                    );
                }
            }
        }, 9999);

        return [$from => $listener];
    }

    /**
     * @param string $file
     * @param string $event
     * @return callable
     */
    protected function deleteFile(string $file, string $event = MySqlDB::EVENT_POST_COMMIT)
    {
        // Move the file right before the commit
        $listener = $this->db->getEventManager()->attach($event, function() use ($file) {
            if (!isset($this->deletes[$file])) {
                // Already moved
                return;
            }

            // Detach self (only delete the file once)
            $this->db->getEventManager()->detach($this->deletes[$file]);
            unset($this->deletes[$file]);

            try {
                ErrorHandler::start();
                $deleted = unlink($file);
                if (!$deleted) {
                    throw new Exception("File '$file' could not be deleted.");
                }
                ErrorHandler::stop(true);
            }
            catch (Exception $e) {
                throw new Exception(
                    sprintf('File "%s" could not be deleted. An error occurred while processing the file.', $file), 0
                );
            }
        }, 9999);

        return [$file => $listener];
    }

}
