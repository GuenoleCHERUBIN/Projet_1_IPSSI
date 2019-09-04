<?php

namespace App\Controller;

use App\Form\ConferenceType;
use App\Entity\Conference;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    /**
     * @Route("/conferenceAdd", name="conferenceAdd")
     */
    public function conferenceAdd(Request $request)
    {
        $conference = new Conference();
        $form = $this->createForm(ConferenceType::class,$conference);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $conference->setCreator($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($conference);
            $entityManager->flush();
        }

        return $this->render('conference/index.html.twig', [
            'form' => $form->createView(),

        ]);
    }
}
