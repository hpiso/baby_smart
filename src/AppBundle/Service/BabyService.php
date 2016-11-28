<?php

namespace AppBundle\Service;

use AppBundle\Entity\Session;

class BabyService
{

    /**
     * 1) Set the total in second for each theme and the number of time it was used
     * 2) Calculate the average
     * 3) Sort by average
     *
     * @param array $sessions
     * @return array
     */
    public function getThemesByAverage(array $sessions)
    {
        // 1)
        $themes = [];
        /** @var Session $session */
        foreach ($sessions as $session) {
            $interval = $session->getStartTime()->diff($session->getEndTime())->s;

            if (!isset($themes[$session->getTheme()])) {
                $themes[$session->getTheme()] = ['total' => $interval, 'nb' => 1, 'theme_id' => $session->getTheme()];

            } else {
                $themes[$session->getTheme()]['nb'] = $themes[$session->getTheme()]['nb'] + 1;
                $themes[$session->getTheme()]['total'] = $themes[$session->getTheme()]['total'] + $interval;
            }
        }

        // 2)
        foreach ($themes as $key => $theme) {
            $themes[$key]['average'] = $theme['total'] / $theme['nb'];
        }

        // 3)
        usort($themes, function($a, $b) {
            if ($a == $b)
                return 0;
            return ($a['average'] < $b['average']) ? -1 : 1;
        });

        return $themes;
    }

}