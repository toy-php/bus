<?php

namespace Bus;

use Bus\Interfaces\Bus;
use Bus\Interfaces\Command;
use Bus\Interfaces\CommandHandler;
use Bus\Interfaces\Message;

class CommandBus implements Bus
{

    protected $handlers;

    public function __construct()
    {
        $this->handlers = new \ArrayObject();
    }

    /**
     * Добавить обработчик команды
     * @param $commandClass
     * @param $handlerClass
     * @throws Exception
     */
    public function addHandler($commandClass, $handlerClass)
    {
        if (!class_exists($commandClass)) {
            throw new Exception('Класс команды недоступен');
        }
        if (!class_exists($handlerClass)) {
            throw new Exception('Класс обработчика команды недоступен');
        }
        if (isset($this->handlers[$commandClass])) {
            throw new Exception('Обработчик для данной команды назначен');
        }
        $this->handlers[$commandClass] = $handlerClass;
    }

    /**
     * Получить обработчик команды
     * @param Command $command
     * @return string|boolean
     * @throws Exception
     */
    public function getHandlers(Command $command)
    {
        $commandType = get_class($command);
        if (!isset($this->handlers[$commandType])) {
            throw new Exception('Обработчик команды не определен');
        }
        return $this->handlers[$commandType];
    }

    /**
     * Обработать сообщение
     * @param Message $message
     * @return boolean
     * @throws Exception
     */
    public function handle(Message $message)
    {
        if (!$message instanceof Command) {
            throw new Exception('Неверный тип сообщения');
        }
        $handlerClass = $this->getHandlers($message);
        $handler = new $handlerClass();
        if (!$handler instanceof CommandHandler) {
            throw new Exception('Неверный тип обработчика');
        }
        return $handler->handle($message);
    }
}