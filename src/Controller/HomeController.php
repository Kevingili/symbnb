<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller {

    /**
     * @Route("/", name="homepage")
     */
    public function home(){

        $prenoms = ["Kevin", "Rayan", "John"];

        return $this->render(
            'home.html.twig',
            [
                'title' => 'Bonjour à tous',
                'age' => 12,
                'prenoms' => $prenoms
            ]
        );

    }

}