<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Session;
use AppBundle\Entity\Theme;
use Doctrine\ORM\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $switches = [];

        if ($request->isXmlHttpRequest()) {

            $isCrying = $request->request->get('isCrying', null);

            if (!$isCrying) {

                /** @var Session $session */
                $session = $em->getRepository('AppBundle:Session')->findOneBy([
                   'endTime' => null
                ]);

                if (!$session) {
                    throw new EntityNotFoundException('Entity session not found');
                }

                $session->setEndTime(new \DateTime());

                exec('sudo -u pi python /home/pi/baby/gpio/led.py' . ' off ' . Theme::$THEMES_GPIO_NUMBERS[$session->getTheme()]);

                $sessions = $em->getRepository('AppBundle:Session')->findAll();
                $themes = $this->get('baby_service')->getThemesByAverage($sessions);
                $switches["state"] = 'off';
                $switches['result'] = $themes;

            } else {

                $sessions = $em->getRepository('AppBundle:Session')->findAll();

                /** @var Session $session */
                $session = new Session();
                $session->setStartTime(new \DateTime());

                // Set random Theme as long as all themes haven't been used yet
                if (count($sessions) < count(Theme::$THEMES_GPIO_NUMBERS)) {

                    $themes = [];

                    if (count($sessions) == 0) {
                        $randomTheme = array_rand(Theme::$THEMES_GPIO_NUMBERS);
                        $session->setTheme($randomTheme);
                    } else {
                        $arrayThemeAlreadyUsed = [];
                        foreach ($sessions as $sessionUsed) {
                            $arrayThemeAlreadyUsed[] = $sessionUsed->getTheme();
                        }

                        $arrayThemeLeft = array_diff(Theme::$THEMES, $arrayThemeAlreadyUsed);

                        $randomTheme = $arrayThemeLeft[array_rand($arrayThemeLeft)];
                        $session->setTheme($randomTheme);
                    }

                    $gpio = Theme::$THEMES_GPIO_NUMBERS[$randomTheme];

                // Set the theme where the average time is the lowest
                } else {
                    $themes = $this->get('baby_service')->getThemesByAverage($sessions);
                    $session->setTheme($themes[0]['theme_id']);

                    $gpio = Theme::$THEMES_GPIO_NUMBERS[$themes[0]['theme_id']];
                }

                exec('sudo -u pi python /home/pi/baby/gpio/led.py' . ' on ' . $gpio);

                $switches["state"] = 'on';
                $switches['theme'] = $session->getTheme();
                $switches['result'] = $themes;
            }


            $em->persist($session);
            $em->flush();

            return $this->json($switches);

        }

        return $this->render('AppBundle:default:index.html.twig');
    }
}
