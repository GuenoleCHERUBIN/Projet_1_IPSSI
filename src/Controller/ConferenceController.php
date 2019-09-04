<?php

namespace App\Controller;

use App\Entity\Vote;
use App\Form\ConferenceType;
use App\Entity\Conference;
use App\Form\VoteType;
use App\Repository\ConferenceRepository;
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

        return $this->render('conference/conferenceAdd.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/home", name="home")
     */
    public function index(ConferenceRepository $conferenceRepository)
    {
        $conferences = $conferenceRepository->findAll();
        $averageNote = [];
        foreach ($conferences as $conference)
        {
            $values = [];
            $votes = $conference->getVotes();
            foreach ($votes as $vote)
            {
                $values[] = $vote->getValue();
            }

            //dd($values);
            $average = array_sum($values)/count($values);
            $averageNote[$conference->getId()] = $average;
        }


        return $this->render('conference/index.html.twig',  [

            'conferences' => $conferences,
            'average'     => $averageNote,
        ]);
    }
    /**
     * @Route("/conference/{id}", name="conference-show")
     */
    public function oneConference(int $id,ConferenceRepository $conferenceRepository,Request $request)
    {
        $conference = $conferenceRepository->findOneBy(['id' => $id]);
        $vote= new Vote();
        $form = $this->createForm(VoteType::class, $vote);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $vote->setUser($this->getUser());
            $vote->setConference($conference);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($vote);
            $entityManager->flush();
        }

        return $this->render('conference/confSpe.html.twig', [
            'conference' => $conference,
            'vote'       => $vote,
            'form'       => $form->createView()
        ]);

    }




}
