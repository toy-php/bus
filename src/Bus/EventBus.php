<?php

namespace Bus;

use Bus\Interfaces\Bus;
use Bus\Interfaces\Event;
use Bus\Interfaces\EventHandler;
use Bus\Interfaces\Message;

class EventBus implements Bus
{

    protected $handlers;

    public function __construct()
    {
        $this->handlers = new \ArrayObject();
    }

    /**
     * Добавить обработчик события
     * @param $eventClass
     * @param $handlerClass
     * @throws Exception
     */
    public function addHandler($eventClass, $handlerClass)
    {
        if(!class_exists($eventClass)){
            throw new Exception('Класс события недоступен');
        }
        if(!class_exists($handlerClass)){
            throw new Exception('Класс обработчика события недоступен');
        }
        if(!isset($this->handlers[$eventClass])){
            $this->handlers[$eventClass] = [];
        }
        $this->handlers[$eventClass][] = $handlerClass;
    }

    /**
     * Получить массив обработчиков
     * @param Event $event
     * @return array
     */
    public function getHandlers(Event $event)
    {
        $eventType = get_class($event);
        return isset($this->handlers[$eventType]) ? $this->handlers[$eventType] : [];
    }

    /**
     * Обработать сообщение
     * @param Message $message
     * @return void
     * @throws Exception
     */
    public function handle(Message $message)
    {
        if(!$message instanceof Event){
            throw new Exception('Неверный тип сообщения');
        }
        $handlers = $this->getHandlers($message);
        foreach ($handlers as $handlerClass){
            $handler = new $handlerClass();
            if(!$handler instanceof EventHandler){
                throw new Exception('Неверный тип обработчика');
            }
            $handler->handle($message);
        }
    }
}