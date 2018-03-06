<?php
/**
 * Tlumx (https://tlumx.github.io/)
 *
 * @author    Yaroslav Kharitonchuk <yarik.proger@gmail.com>
 * @link      https://github.com/tlumx/tlumx-eventmanager
 * @copyright Copyright (c) 2016-2018 Yaroslav Kharitonchuk
 * @license   https://github.com/tlumx/tlumx-eventmanager/blob/master/LICENSE  (MIT License)
 */
namespace Tlumx\Tests\EventManager;

use Tlumx\EventManager\EventInterface;

class ListenerForTesting
{
    public static function attachListener(EventInterface $e)
    {
        $count = $e->getParam('count');
        $count++;
        $e->setParams(['count' => $count]);
    }

    public function __invoke(EventInterface $e)
    {
        $count = $e->getParam('count');
        $count++;
        $e->setParams(['count' => $count]);
    }
}
