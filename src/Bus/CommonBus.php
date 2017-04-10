<?php

namespace Bus;

use Bus\Interfaces\Bus;
use Bus\Interfaces\Message;

class CommonBus implements Bus
{

    protected $buses;
    protected $types = [];

    public function __construct()
    {
        $this->buses = new \SplObjectStorage();
    }

    /**
     * Добавить маршрут для определенного типа сообщений
     * @param $messageType
     * @param Bus $bus
     * @throws Exception
     */
    public function route($messageType, Bus $bus)
    {
        if(in_array($messageType,$this->types)){
            throw new Exception('Данному типу сообщений назначен обработчик');
        }
        $this->types[] = $messageType;
        $this->buses->attach($bus, $messageType);
    }

    /**
     * Обработать сообщение
     * @param Message $message
     * @return mixed
     * @throws Exception
     */
    public function handle(Message $message)
    {
        /** @var Bus $bus */
        foreach ($this->buses as $bus) {
            $messageType = $this->buses[$bus];
            if($message instanceof $messageType){
                return $bus->handle($message);
            }
        }
        throw new Exception('Нет подходящей шины для полученного типа сообщения');
    }
}