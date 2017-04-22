<?php

namespace Bus;

use Bus\Interfaces\Bus;
use Bus\Interfaces\Message;
use Bus\Interfaces\Query;
use Bus\Interfaces\QueryHandler;

class QueryBus implements Bus
{

    protected $handlers;

    public function __construct()
    {
        $this->handlers = new \ArrayObject();
    }

    /**
     * Добавить обработчик запроса
     * @param $queryClass
     * @param $handlerClass
     * @throws Exception
     */
    public function addHandler($queryClass, $handlerClass)
    {
        if (!class_exists($queryClass)) {
            throw new Exception('Класс запроса недоступен');
        }
        if (!class_exists($handlerClass)) {
            throw new Exception('Класс обработчика запроса недоступен');
        }
        if (isset($this->handlers[$queryClass])) {
            throw new Exception('Обработчик для данного запроса назначен');
        }
        $this->handlers[$queryClass] = $handlerClass;
    }

    /**
     * Получить обработчик запроса
     * @param Query $query
     * @return string|boolean
     * @throws Exception
     */
    public function getHandlers(Query $query)
    {
        $queryType = get_class($query);
        if (!isset($this->handlers[$queryType])) {
            throw new Exception('Обработчик запроса не определен');
        }
        return $this->handlers[$queryType];
    }

    /**
     * Обработать сообщение
     * @param Message $message
     * @return mixed
     * @throws Exception
     */
    public function handle(Message $message)
    {
        if (!$message instanceof Query) {
            throw new Exception('Неверный тип сообщения');
        }
        $handlerClass = $this->getHandlers($message);
        $handler = new $handlerClass();
        if (!$handler instanceof QueryHandler) {
            throw new Exception('Неверный тип обработчика');
        }
        return $handler->handle($message);
    }

}