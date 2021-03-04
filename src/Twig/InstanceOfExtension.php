<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

/**
 * Extension loaded automatically by Twig
 * Class InstanceOfExtension
 * @package App\Twig
 */
class InstanceOfExtension extends AbstractExtension
{
    public function getTests()
    {
        return [
          new TwigTest('instanceOf', [$this, 'isInstanceOf'])
        ];
    }

    /**
     * Check if object is instance of Instance
     * @param $item
     * @param $instance
     * @return bool
     */
    public function isInstanceOf($item, $instance): bool
    {
        return $item instanceof $instance;
    }
}