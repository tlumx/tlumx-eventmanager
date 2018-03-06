<?php
/**
 * Tlumx (https://tlumx.github.io/)
 *
 * @author    Yaroslav Kharitonchuk <yarik.proger@gmail.com>
 * @link      https://github.com/tlumx/tlumx-eventmanager
 * @copyright Copyright (c) 2016-2018 Yaroslav Kharitonchuk
 * @license   https://github.com/tlumx/tlumx-eventmanager/blob/master/LICENSE  (MIT License)
 */
namespace Tlumx\EventManager;

/**
 * EventManager class.
 *
 * This is implementation of EventManagerInterface
 * (the proposed Psr\EventManager\EventManager).
 */
class EventManager implements EventManagerInterface
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * {@inheritDoc}
     */
    public function attach($event, $callback, $priority = 0)
    {
        if (is_callable($callback)) {
            $this->listeners[(string) $event][(int) $priority][] = $callback;
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function detach($event, $callback)
    {
        $event = (string) $event;

        if (!isset($this->listeners[$event])) {
            return false;
        }

        $flag = false;
        foreach ($this->listeners[$event] as $priority => $listeners) {
            foreach ($listeners as $key => $val) {
                if ($val !== $callback) {
                    continue;
                }

                unset($this->listeners[$event][$priority][$key]);
                $flag = true;

                if (empty($this->listeners[$event][$priority])) {
                    unset($this->listeners[$event][$priority]);
                }
            }

            if (empty($this->listeners[$event])) {
                unset($this->listeners[$event]);
                break;
            }
        }

        return $flag;
    }

    /**
     * {@inheritDoc}
     */
    public function clearListeners($event)
    {
        unset($this->listeners[(string) $event]);
    }

    /**
     * Trigger
     *
     * @param string|\Tlumx\EventManager\Event $event
     * @param array $params
     * @return bool
     */
    public function trigger($event, $target = null, $argv = [])
    {
        if (!$event instanceof Event) {
            $event = new Event($event, $target, $argv);
        } else {
            $event->setTarget($target);
            $event->setParams($argv);
        }

        $allListeners = [];
        if (isset($this->listeners[$event->getName()])) {
            ksort($this->listeners[$event->getName()]);
            $allListeners = array_values($this->listeners[$event->getName()]);
        }

        $result = null;
        foreach ($allListeners as $listeners) {
            foreach ($listeners as $listener) {
                $result = call_user_func($listener, $event);
                if ($event->isPropagationStopped()) {
                    break 2;
                }
            }
        }

        return $result;
    }
}
