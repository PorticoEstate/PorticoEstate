<?php
class FileExistsException extends Exception
{
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
class CopyFailureException extends Exception
{
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
class ModelExistsException extends Exception
{
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
class ModelDoesNotExistException extends Exception
{
    public function __construct($message, Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
class IncompleteItemException extends Exception
{
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
class BimDataException extends Exception
{
    public function __construct($message, Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
class NoResponseException extends Exception
{
    public function __construct($message, Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}