<?php


namespace App\Service\Utility;


class likeMatch
{
    /**
     * SQL Like operator in PHP.
     * Returns TRUE if match else FALSE.
     * @param string $pattern
     * @param string $subject
     * @return bool
     */
    function match($pattern, $subject)
    {
        $pattern = str_replace('%', '.*', preg_quote($pattern,'/'));
        dd(preg_match("/^{$pattern}$/i", $subject));
        return (bool) preg_match("/^{$pattern}$/i", $subject);
    }

    /**
     * Check if pattern began like subject
     * @param $pattern
     * @param $subject
     * @return bool
     */
    function matchCode($pattern, $subject)
    {
        return substr($subject, 0, strlen($pattern)) === $pattern;
    }
}