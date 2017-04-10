<?php

namespace Bus\Interfaces;

interface CommandHandler extends Handler
{

    /**
     * Обработать команду
     * @param Message $message
     * @return boolean
     */
    public function handle(Message $message);
}